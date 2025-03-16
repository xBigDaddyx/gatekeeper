<?php

namespace XBigDaddyx\Gatekeeper\Traits;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use XBigDaddyx\Gatekeeper\Enums\ApprovalStatus;
use XBigDaddyx\Gatekeeper\Events\ApprovalApproved;
use XBigDaddyx\Gatekeeper\Events\ApprovalRejected;
use XBigDaddyx\Gatekeeper\Events\ApprovalRequested;
use XBigDaddyx\Gatekeeper\Jobs\ProcessApproval;
use XBigDaddyx\Gatekeeper\Models\Approval;
use XBigDaddyx\Gatekeeper\Models\ApprovalFlow;
use XBigDaddyx\Gatekeeper\Notifications\ApprovalRequestedNotification;

trait Approvable
{
  /** Get the column name for approval status */
  public function getApprovalStatusColumn(): string
  {
    return config('gatekeeper.approval_status_column', 'approval_status');
  }

  public function getApprovalStatusAttribute()
  {
    $status = $this->{$this->getApprovalStatusColumn()};

    return $status instanceof ApprovalStatus ? $status : ApprovalStatus::tryFrom($status);
  }

  /** Set the approval status */
  public function setApprovalStatus(ApprovalStatus $status): void
  {
    $this->{$this->getApprovalStatusColumn()} = $status->value;
    $this->save();
  }

  /** Approval status checkers */
  public function isPending(): bool
  {
    return $this->getApprovalStatusAttribute() === ApprovalStatus::PENDING;
  }

  public function approvalHistory()
  {
    return $this->approvals()->with('user')->orderBy('created_at', 'desc');
  }

  public function pendingApprovals()
  {
    return $this->approvals()->with('user')->where('status', ApprovalStatus::PENDING->value)->orderBy('created_at', 'desc');
  }

  public function isApproved(): bool
  {
    return $this->getApprovalStatusAttribute() === ApprovalStatus::APPROVED;
  }

  public function isRejected(): bool
  {
    return $this->getApprovalStatusAttribute() === ApprovalStatus::REJECTED;
  }

  /** Relationships */
  public function approvals()
  {
    return $this->morphMany(Approval::class, 'approvable');
  }

  public function approvalFlow()
  {
    return ApprovalFlow::where('approvable_type', self::class);
  }

  /** Check if user can approve */
  public function canBeApprovedBy($user): bool
  {
    $currentStep = $this->approvalFlow()->where('step_order', $this->current_step)->first();

    return $currentStep && $this->isPending() && (
      ($currentStep->job_title_id && $user->job_title_id === $currentStep->job_title_id) ||
      ($currentStep->role && $user->hasRole($currentStep->role))
    );
  }

  /** Approve the request */
  public function approve($user, $comment = null, $queue = true): void
  {
    if ($queue) {
      ProcessApproval::dispatch($this, $user, 'approve', $comment);

      return;
    }

    DB::transaction(function () use ($user, $comment) {
      $this->createApprovalRecord($user, 'approved', $comment);
      $this->processNextApprovalStep();
    });
  }

  /** Reject the request */
  public function reject($user, $comment = null, $queue = true): void
  {
    if ($queue) {
      ProcessApproval::dispatch($this, $user, 'reject', $comment);

      return;
    }

    DB::transaction(function () use ($user, $comment) {
      $this->createApprovalRecord($user, 'rejected', $comment);
      $this->setApprovalStatus(ApprovalStatus::REJECTED);
      event(new ApprovalRejected($this));
    });
  }

  /** Create an approval record */
  protected function createApprovalRecord($user, string $status, ?string $comment): void
  {
    $this->approvals()->create([
      'user_id' => $user->id,
      'status' => config("gatekeeper.statuses.$status", $status),
      'comment' => $comment,
      'action_at' => now(),
    ]);
  }

  /** Process the next approval step */
  protected function processNextApprovalStep(): void
  {
    $nextStep = $this->getNextApplicableStep();
    if ($nextStep) {
      $this->current_step = $nextStep->step_order;
      $this->notifyNextApprovers($nextStep);
      event(new ApprovalRequested($this));
    } else {
      $this->setApprovalStatus(ApprovalStatus::APPROVED);
      event(new ApprovalApproved($this));
    }
    $this->save();
  }

  /** Get the next applicable approval step */
  protected function getNextApplicableStep()
  {
    return $this->approvalFlow()
      ->where('step_order', '>', $this->current_step)
      ->orderBy('step_order')
      ->get()
      ->first(fn ($step) => ! $step->condition || $this->evaluateCondition($step->condition));
  }

  /** Evaluate approval conditions */
  protected function evaluateCondition($condition): bool
  {
    if (is_string($condition)) {
      $condition = json_decode($condition, true);
    }
    if (! is_array($condition) || ! isset($condition['field'], $condition['operator'], $condition['value'])) {
      return true;
    }

    return match ($condition['operator']) {
      '<' => $this->{$condition['field']} < $condition['value'],
      '>' => $this->{$condition['field']} > $condition['value'],
      '=' => $this->{$condition['field']} == $condition['value'],
      default => true,
    };
  }

  /** Notify the next approvers */
  protected function notifyNextApprovers($step): void
  {
    $userModel = config('gatekeeper.user_model', \App\Models\User::class);
    $users = $step->job_title_id
      ? $userModel::where('job_title_id', $step->job_title_id)->get()
      : $userModel::role($step->role)->get();

    Log::info('Notifying next approvers', [
      'step_order' => $step->step_order,
      'job_title_id' => $step->job_title_id,
      'role' => $step->role,
      'user_count' => $users->count(),
      'users' => $users->pluck('id')->toArray(),
    ]);

    if ($users->isEmpty()) {
      Log::warning('No users found to notify for approval step', [
        'approvable_type' => get_class($this),
        'approvable_id' => $this->id,
        'step_order' => $step->step_order,
      ]);
      return;
    }

    foreach ($users as $user) {
      $approveUrl = $this->getApprovalUrl($user);
      $rejectUrl = $this->getRejectionUrl($user);
      try {
        $user->notify(new ApprovalRequestedNotification($this, $approveUrl, $rejectUrl, $step));
        Log::info('Notification sent to user', [
          'user_id' => $user->id,
          'approvable_type' => get_class($this),
          'approvable_id' => $this->id,
        ]);
      } catch (\Exception $e) {
        Log::error('Failed to send approval notification', [
          'user_id' => $user->id,
          'approvable_type' => get_class($this),
          'approvable_id' => $this->id,
          'error' => $e->getMessage(),
        ]);
      }
    }
  }

  // ... (remaining methods unchanged)

  /** Submit the record for approval */


  /** Generate a signed approval URL */
  public function getApprovalUrl($user, $ttl = 60): string
  {
    return $this->generateSignedUrl('gatekeeper.approve', $user, $ttl);
  }

  /** Generate a signed rejection URL */
  public function getRejectionUrl($user, $ttl = 60): string
  {
    return $this->generateSignedUrl('gatekeeper.reject', $user, $ttl);
  }

  /** Generate a signed URL */
  protected function generateSignedUrl(string $route, $user, int $ttl): string
  {
    return URL::temporarySignedRoute($route, now()->addMinutes($ttl), [
      'approvable_type' => get_class($this),
      'approvable_id' => $this->id,
      'user_id' => $user->id,
    ]);
  }

  /** Submit the record for approval */
  public function submitForApproval($user, ?string $comment = null): void
  {
    DB::transaction(function () use ($user, $comment) {
      $this->setApprovalStatus(ApprovalStatus::PENDING);
      $this->current_step = $this->approvalFlow()->min('step_order') ?? 1;
      $this->createApprovalRecord($user, 'pending', $comment ?: 'Submitted for approval');
      Log::info('Approval submission initiated', [
        'approvable_type' => get_class($this),
        'approvable_id' => $this->id,
        'user_id' => $user->id,
        'current_step' => $this->current_step,
      ]);
      $this->processNextApprovalStep();
    });
  }
}
