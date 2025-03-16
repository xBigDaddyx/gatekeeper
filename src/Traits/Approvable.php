<?php

namespace XBigDaddyx\Gatekeeper\Traits;

use App\Enums\ApprovalStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use XBigDaddyx\Gatekeeper\Events\{
  ApprovalApproved,
  ApprovalRejected,
  ApprovalRequested
};
use XBigDaddyx\Gatekeeper\Jobs\ProcessApproval;
use XBigDaddyx\Gatekeeper\Models\{
  Approval,
  ApprovalFlow
};
use XBigDaddyx\Gatekeeper\Notifications\ApprovalRequestedNotification;

trait Approvable
{
  /** Get the column name for approval status */
  public function getApprovalStatusColumn(): string
  {
    return config('gatekeeper.approval_status_column', 'approval_status');
  }

  /** Get the approval status attribute */
  public function getApprovalStatusAttribute()
  {
    return ApprovalStatus::tryFrom($this->{$this->getApprovalStatusColumn()});
  }

  /** Set the approval status */
  public function setApprovalStatus(ApprovalStatus $status)
  {
    $this->{$this->getApprovalStatusColumn()} = $status->value;
    $this->save();
  }

  /** Approval status checkers */
  public function isPending(): bool
  {
    return $this->getApprovalStatusAttribute() === ApprovalStatus::PENDING;
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
  public function approve($user, $comment = null, $queue = true)
  {
    if ($queue) {
      return ProcessApproval::dispatch($this, $user, 'approve', $comment);
    }

    DB::transaction(function () use ($user, $comment) {
      $this->createApprovalRecord($user, 'approved', $comment);
      $this->processNextApprovalStep();
    });
  }

  /** Reject the request */
  public function reject($user, $comment = null, $queue = true)
  {
    if ($queue) {
      return ProcessApproval::dispatch($this, $user, 'reject', $comment);
    }

    DB::transaction(function () use ($user, $comment) {
      $this->createApprovalRecord($user, 'rejected', $comment);
      $this->setApprovalStatus(ApprovalStatus::REJECTED);
      event(new ApprovalRejected($this));
    });
  }

  /** Create an approval record */
  protected function createApprovalRecord($user, string $status, ?string $comment)
  {
    $this->approvals()->create([
      'user_id' => $user->id,
      'status' => config("gatekeeper.statuses.$status", $status),
      'comment' => $comment,
    ]);
  }

  /** Process the next approval step */
  protected function processNextApprovalStep()
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
  protected function notifyNextApprovers($step)
  {
    $userModel = config('gatekeeper.user_model', \App\Models\User::class);
    $users = $step->job_title_id
      ? $userModel::where('job_title_id', $step->job_title_id)->get()
      : $userModel::role($step->role)->get();

    foreach ($users as $user) {
      $approveUrl = $this->getApprovalUrl($user);
      $rejectUrl = $this->getRejectionUrl($user);
      $user->notify(new ApprovalRequestedNotification($this, $approveUrl, $rejectUrl, $step));
    }
  }

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
}
