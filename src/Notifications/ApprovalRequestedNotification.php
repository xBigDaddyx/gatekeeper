<?php

namespace XBigDaddyx\Gatekeeper\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ApprovalRequestedNotification extends Notification
{
    protected $approvable;

    protected $approveUrl;

    protected $rejectUrl;

    protected $step;

    protected $tenant_slug;

    public function __construct($tenant_slug, $approvable, $approveUrl, $rejectUrl, $step = null)
    {
        $this->approvable = $approvable;
        $this->approveUrl = $approveUrl;
        $this->rejectUrl = $rejectUrl;
        $this->step = $step;
        $this->tenant = $tenant_slug;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $resourceName = class_basename($this->approvable);
        $stepInfo = $this->step ? "Step {$this->step->step_order}: " . ($this->step->job_title_id ? 'Packing Supervisor' : $this->step->role) : 'Final Approval';

        // Compute validated items from CartonBox records
        $validatedItems = $this->approvable->cartonBoxes()
            ->where('validation_status', \App\Enums\CartonValidationStatus::VALIDATED->value)
            ->with('items') // Eager load items
            ->get()
            ->sum(function ($cartonBox) {
                return $cartonBox->items->where('pivot.is_validated', true)->count();
            });

        return (new MailMessage)
            ->subject("Approval Required: {$resourceName} #{$this->approvable->id}")
            ->view('gatekeeper::mail.gatekeeper.approval_requested', [
                'po' => $this->approvable->purchase_order_number,
                'po_quantity' => $this->approvable->purchase_order_quantity,
                'approvable' => $this->approvable,
                'approveUrl' => $this->approveUrl,
                'rejectUrl' => $this->rejectUrl,
                'stepInfo' => $stepInfo,
                'resourceName' => $resourceName,
                'currentStatus' => $this->approvable->management_approval_status ?? 'Pending',
                'currentStep' => $this->approvable->current_step ?? 1,
                'validatedItems' => $validatedItems ?: 'N/A', // Sum of validated items from cartons
                'validatedCartons' => $this->approvable->completed_cartons_count ?: 'N/A', // Cached count
                'contract' => $this->approvable->contract ?? 'N/A',
                'style' => $this->approvable->style ?? 'N/A',
                'size' => $this->approvable->size ?? 'N/A',
                'color' => $this->approvable->color ?? 'N/A',
                'buyerName' => $this->approvable->buyer ? $this->approvable->buyer->name : 'N/A',
            ]);
    }
}
