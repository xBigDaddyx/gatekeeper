<?php

namespace XBigDaddyx\Gatekeeper\Filament\Tables;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Actions\Action as NotificationAction;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RejectAction extends Action
{
  protected function setUp(): void
  {
    parent::setUp();

    $this->label(__('gatekeeper::gatekeeper.reject_action.label'))
      ->icon('fluentui-dismiss-circle-20')
      ->color('danger')
      ->modalHeading(__('gatekeeper::gatekeeper.reject_action.modal_heading'))
      ->modalDescription(__('gatekeeper::gatekeeper.reject_action.modal_description'))
      ->modalSubmitActionLabel(__('gatekeeper::gatekeeper.reject_action.modal_submit'))
      ->modalCancelActionLabel(__('gatekeeper::gatekeeper.reject_action.modal_cancel'))
      ->requiresConfirmation()
      ->form([
        Select::make('reason_category')
          ->label(__('gatekeeper::gatekeeper.reject_action.reason_category_label'))
          ->options([
            'incomplete_data' => __('gatekeeper::gatekeeper.reject_action.reasons.incomplete_data'),
            'policy_violation' => __('gatekeeper::gatekeeper.reject_action.reasons.policy_violation'),
            'other' => __('gatekeeper::gatekeeper.reject_action.reasons.other'),
          ])
          ->required()
          ->reactive(),
        Textarea::make('reason')
          ->label(__('gatekeeper::gatekeeper.reject_action.reason_label'))
          ->required()
          ->placeholder(fn ($get) => $get('reason_category') === 'other' ? __('gatekeeper::gatekeeper.reject_action.reason_placeholder') : null),
      ])
      ->visible(fn ($record) => $record->canBeApprovedBy(Auth::user()))
      ->disabled(fn ($record) => $record->isRejected())
      ->tooltip(fn ($record) => $record->isRejected() ? __('gatekeeper::gatekeeper.reject_action.already_rejected') : null)
      ->action(fn (Model $record, array $data) => $this->reject($record, Auth::user(), $data['reason_category'], $data['reason']));
  }

  protected function reject(Model $record, $user, string $reasonCategory, string $reason): void
  {
    try {
      if (method_exists($record, 'reject')) {
        $fullReason = "$reasonCategory: $reason";
        $record->reject($user, $fullReason);

        Log::info('Rejection processed', [
          'record_type' => get_class($record),
          'record_id' => $record->id,
          'user_id' => $user->id,
          'reason_category' => $reasonCategory,
          'reason' => $reason,
        ]);

        Notification::make()
          ->title(__('gatekeeper::gatekeeper.reject_action.success_title'))
          ->success()
          ->body(__('gatekeeper::gatekeeper.reject_action.success_message', ['id' => $record->id]))
          ->actions([
            NotificationAction::make('view_history')
              ->label(__('gatekeeper::gatekeeper.approval_timeline_action.label'))
              ->url(fn () => route('filament.resources.'.strtolower(class_basename($record)).'.index').'?tableAction=view_approval_timeline&record='.$record->id)
              ->openUrlInNewTab(),
          ])
          ->send();
      } else {
        throw new \Exception('Reject method not implemented.');
      }
    } catch (\Exception $e) {
      Log::error('Rejection failed', [
        'record_type' => get_class($record),
        'record_id' => $record->id,
        'user_id' => $user->id,
        'error' => $e->getMessage(),
      ]);

      Notification::make()
        ->title(__('gatekeeper::gatekeeper.reject_action.exception_title'))
        ->danger()
        ->body(__('gatekeeper::gatekeeper.reject_action.exception_message', ['error' => $e->getMessage()]))
        ->send();
    }
  }
}
