<?php

namespace XBigDaddyx\Gatekeeper\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class ApprovalRequestedNotification extends Notification
{
  protected $approvable;
  protected $approveUrl;
  protected $rejectUrl;
  protected $step;
  protected $tenant_slug;
  protected $customData;
  protected $subjectTemplate;
  protected $view;

  public function __construct(
    string $tenant_slug,
    $approvable,
    string $approveUrl,
    string $rejectUrl,
    $step = null,
    array $customData = [],
    ?string $subjectTemplate = null,
    ?string $view = null
  ) {
    $this->tenant_slug = $tenant_slug;
    $this->approvable = $approvable;
    $this->approveUrl = $approveUrl;
    $this->rejectUrl = $rejectUrl;
    $this->step = $step;
    $this->customData = $customData;
    $this->subjectTemplate = $subjectTemplate ?? 'Approval Required: {resourceName} #{id}';
    $this->view = $view ?? 'gatekeeper::mail.gatekeeper.approval_requested';
  }

  public function via($notifiable)
  {
    return ['mail'];
  }

  public function toMail($notifiable)
  {
    $resourceName = class_basename($this->approvable);
    $stepInfo = $this->step
      ? "Step {$this->step->step_order}: ".($this->step->job_title_id ? $this->getJobTitleName($this->step->job_title_id) : $this->step->role)
      : 'Final Approval';

    // Gunakan purchase_order_number jika ada, fallback ke id
    $identifier = $this->approvable->purchase_order_number ?? $this->approvable->id;

    // Prepare data array
    $data = array_merge([
      'approvable' => $this->approvable,
      'approveUrl' => $this->approveUrl,
      'rejectUrl' => $this->rejectUrl,
      'stepInfo' => $stepInfo,
      'resourceName' => $resourceName,
      'currentStatus' => $this->approvable->approval_status instanceof \XBigDaddyx\Gatekeeper\Enums\ApprovalStatus
        ? $this->approvable->approval_status->value
        : ($this->approvable->approval_status ?? 'pending'),
      'currentStep' => $this->approvable->current_step ?? 1,
      'id' => $identifier,
    ], $this->customData);

    // Log untuk debug
    Log::debug('Preparing email subject', [
      'template' => $this->subjectTemplate,
      'data' => ['resourceName' => $resourceName, 'id' => $identifier],
    ]);

    // Ganti placeholder di subject
    $subject = $this->replacePlaceholders($this->subjectTemplate, [
      'resourceName' => $resourceName,
      'id' => $identifier,
    ]);

    // Log subject setelah penggantian
    Log::debug('Subject after replacement', ['subject' => $subject]);

    $mail = (new MailMessage)
      ->subject($subject)
      ->view($this->view, ['data' => $data]);

    if ($cc = config('gatekeeper.notification_cc')) {
      $mail->cc($cc);
    }

    return $mail;
  }

  protected function replacePlaceholders(string $template, array $data): string
  {
    // Pastikan placeholder sesuai dengan format {key}
    $placeholders = array_map(fn ($key) => "{ {$key} }", array_keys($data));
    $values = array_map(function ($value) {
      return is_scalar($value) || (is_object($value) && method_exists($value, '__toString'))
        ? (string) $value
        : '';
    }, array_values($data));

    $result = str_replace($placeholders, $values, $template);

    // Jika placeholder tidak terganti, log dan gunakan fallback
    if (preg_match('/{\s*\w+\s*}/', $result)) {
      Log::warning('Placeholder replacement failed', [
        'template' => $template,
        'data' => $data,
        'result' => $result,
      ]);
      return "Approval Required: {$data['resourceName']} #{$data['id']}";
    }

    return $result;
  }

  protected function getJobTitleName(int $jobTitleId): string
  {
    $jobTitle = \XBigDaddyx\Gatekeeper\Models\JobTitle::find($jobTitleId);
    if (! $jobTitle) {
      Log::warning('Job title not found', [
        'job_title_id' => $jobTitleId,
        'table' => config('gatekeeper.tables.job_titles'),
      ]);
      return 'Unknown Job Title';
    }
    return $jobTitle->title;
  }
}
