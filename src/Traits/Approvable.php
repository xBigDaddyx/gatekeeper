<?php

namespace XBigDaddyx\Gatekeeper\Traits;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use XBigDaddyx\Gatekeeper\Events\ApprovalApproved;
use XBigDaddyx\Gatekeeper\Events\ApprovalRejected;
use XBigDaddyx\Gatekeeper\Events\ApprovalRequested;
use XBigDaddyx\Gatekeeper\Jobs\ProcessApproval;
use XBigDaddyx\Gatekeeper\Models\Approval;
use XBigDaddyx\Gatekeeper\Models\ApprovalFlow;
use XBigDaddyx\Gatekeeper\Notifications\ApprovalRequestedNotification;

trait Approvable
{
    public function approvals()
    {
        return $this->morphMany(Approval::class, 'approvable');
    }

    public function approvalFlow()
    {
        return ApprovalFlow::where('approvable_type', self::class);
    }

    public function canBeApprovedBy($user)
    {
        $currentStep = $this->approvalFlow()->where('step_order', $this->current_step)->first();
        if (!$currentStep || $this->management_approval_status->value !== config('gatekeeper.statuses.pending', 'pending')) {
            return false;
        }
        return ($currentStep->job_title_id && $user->job_title_id === $currentStep->job_title_id) ||
            ($currentStep->role && $user->hasRole($currentStep->role));
    }

    public function approve($user, $comment = null, $queue = true)
    {
        if ($queue) {
            ProcessApproval::dispatch($this, $user, 'approve', $comment);
            return;
        }

        DB::transaction(function () use ($user, $comment) {
            $this->approvals()->create([
                'user_id' => $user->id,
                'status' => config('gatekeeper.statuses.approved', 'approved'),
                'comment' => $comment,
            ]);

            $currentStep = $this->approvalFlow()->where('step_order', $this->current_step)->first();
            if ($currentStep && $currentStep->is_parallel) {
                $requiredApprovals = $this->getRequiredParallelApprovals($currentStep);
                $currentApprovals = $this->approvals()
                    ->where('status', config('gatekeeper.statuses.approved', 'approved'))
                    ->whereIn('user_id', $requiredApprovals)
                    ->count();
                if ($currentApprovals < count($requiredApprovals)) {
                    $this->notifyNextApprovers($currentStep);
                    return;
                }
            }

            $nextStep = $this->getNextApplicableStep();
            if ($nextStep) {
                $this->current_step = $nextStep->step_order;
                $this->notifyNextApprovers($nextStep);
                event(new ApprovalRequested($this));
            } else {
                $this->management_approval_status = config('gatekeeper.statuses.approved', 'approved');
                event(new ApprovalApproved($this));
            }
            $this->save();
        });
    }

    public function reject($user, $comment = null, $queue = true)
    {
        if ($queue) {
            ProcessApproval::dispatch($this, $user, 'reject', $comment);
            return;
        }

        DB::transaction(function () use ($user, $comment) {
            $this->approvals()->create([
                'user_id' => $user->id,
                'status' => config('gatekeeper.statuses.rejected', 'rejected'),
                'comment' => $comment,
            ]);
            $this->management_approval_status = config('gatekeeper.statuses.rejected', 'rejected');
            $this->save();
            event(new ApprovalRejected($this));
        });
    }

    protected function getNextApplicableStep()
    {
        $steps = $this->approvalFlow()->where('step_order', '>', $this->current_step)->orderBy('step_order')->get();

        foreach ($steps as $step) {
            $conditionResult = !$step->condition || $this->evaluateCondition($step->condition);

            if ($conditionResult) {
                return $step;
            }
        }
        return null;
    }

    protected function evaluateCondition($condition)
    {
        // Handle cases where $condition might be a string (e.g., JSON)
        if (!is_array($condition)) {
            $condition = json_decode($condition, true);
            if (!is_array($condition)) {
                return true; // If not a valid array, skip condition
            }
        }

        // Ensure all required keys exist
        if (!isset($condition['field']) || !isset($condition['operator']) || !isset($condition['value'])) {
            return true; // Skip if condition is incomplete
        }

        $field = $condition['field'];
        $operator = $condition['operator'];
        $value = $condition['value'];

        switch ($operator) {
            case '<':
                return $this->$field < $value;
            case '>':
                return $this->$field > $value;
            case '=':
                return $this->$field == $value;
            default:
                return true;
        }
    }

    protected function getRequiredParallelApprovals($step)
    {
        $userModel = config('gatekeeper.user_model', \App\Models\User::class);
        return $step->job_title_id
            ? $userModel::where('job_title_id', $step->job_title_id)->pluck('id')->toArray()
            : $userModel::role($step->role)->pluck('id')->toArray();
    }

    protected function notifyNextApprovers($step)
    {
        $userModel = config('gatekeeper.user_model', \App\Models\User::class);
        $users = $step->job_title_id
            ? $userModel::where('job_title_id', $step->job_title_id)->get()
            : $userModel::role($step->role)->get();
        $users->each(function ($user) use ($step) {
            $approveUrl = $this->getApprovalUrl(tenant()->slug, $user, 1440); // 24 hours
            $rejectUrl = $this->getRejectionUrl(tenant()->slug, $user, 1440);
            $user->notify(new ApprovalRequestedNotification(tenant()->slug, $this, $approveUrl, $rejectUrl, $step));
        });
    }
    /**
     * Generate a signed URL for approving this resource.
     *
     * @param \Illuminate\Contracts\Auth\Authenticatable $user
     * @param int $ttl Minutes until the URL expires (default: 60 minutes)
     * @return string
     */
    public function getApprovalUrl($tenant, $user, $ttl = 60)
    {
        return URL::temporarySignedRoute(
            'gatekeeper.approve', // Route name for approval
            now()->addMinutes($ttl), // Expiration time
            [
                'approvable_type' => get_class($this), // e.g., App\Models\Accuracy\PackingList
                'approvable_id' => $this->id,
                'user_id' => $user->id,
                'tenant_slug' => $tenant
            ]
        );
    }

    /**
     * Generate a signed URL for rejecting this resource.
     *
     * @param \Illuminate\Contracts\Auth\Authenticatable $user
     * @param int $ttl Minutes until the URL expires (default: 60 minutes)
     * @return string
     */
    public function getRejectionUrl($tenant, $user, $ttl = 60)
    {
        return URL::temporarySignedRoute(
            'gatekeeper.reject', // Route name for rejection
            now()->addMinutes($ttl), // Expiration time
            [
                'approvable_type' => get_class($this),
                'approvable_id' => $this->id,
                'user_id' => $user->id,
                'tenant_slug' => $tenant
            ]
        );
    }
    /**
     * Generate a signed URL for resending the approval request.
     *
     * @param \Illuminate\Contracts\Auth\Authenticatable $user
     * @param int $ttl Minutes until the URL expires (default: 60 minutes)
     * @return string
     */
    public function getResendApprovalUrl($tenant, $user, $ttl = 60)
    {
        return URL::temporarySignedRoute(
            'gatekeeper.resend',
            now()->addMinutes($ttl),
            [
                'approvable_type' => get_class($this),
                'approvable_id' => $this->id,
                'user_id' => $user->id,
                'tenant_slug' => $tenant
            ]
        );
    }

    /**
     * Resend the approval request to the next approvers.
     *
     * @param \Illuminate\Contracts\Auth\Authenticatable $user
     * @return void
     */
    public function resendApprovalRequest($user)
    {
        $currentStep = $this->approvalFlow()->where('step_order', $this->current_step)->first();
        if ($currentStep) {
            $this->notifyNextApprovers($currentStep);
            event(new ApprovalRequested($this));
            Log::info("Approval request resent for {$this->id} at step {$this->current_step} by user #{$user->id}");
        } else {
            Log::warning("No current step found to resend approval for {$this->id}");
        }
    }
}
