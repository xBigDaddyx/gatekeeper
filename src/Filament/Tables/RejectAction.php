<?php

namespace XBigDaddyx\Gatekeeper\Filament\Tables;

use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Model;

class RejectAction extends Action
{
  protected function setUp(): void
  {
    parent::setUp();

    $this->label(__('gatekeeper::gatekeeper.reject_action.label'))
      ->icon('fluentui-dismiss-circle-20')
      ->modalHeading(__('gatekeeper::gatekeeper.reject_action.modal_heading'))
      ->modalDescription(__('gatekeeper::gatekeeper.reject_action.modal_description'))
      ->modalSubmitActionLabel(__('gatekeeper::gatekeeper.reject_action.modal_submit'))
      ->modalCancelActionLabel(__('gatekeeper::gatekeeper.reject_action.modal_cancel'))
      ->requiresConfirmation()
      ->form([
        Textarea::make('reason')
          ->label(__('gatekeeper::gatekeeper.reject_action.reason_label'))
          ->required(),
      ])
      ->action(fn (Model $record, array $data) => $this->reject($record, $data['reason']));
  }

  protected function reject(Model $record, string $reason): void
  {
    try {
      if (method_exists($record, 'reject')) {
        $record->reject($reason);

        Notification::make()
          ->title(__('gatekeeper::gatekeeper.reject_action.success_title'))
          ->success()
          ->body(__('gatekeeper::gatekeeper.reject_action.success_message'))
          ->send();
      } else {
        Notification::make()
          ->title(__('gatekeeper::gatekeeper.reject_action.error_title'))
          ->danger()
          ->body(__('gatekeeper::gatekeeper.reject_action.error_message'))
          ->send();
      }
    } catch (\Exception $e) {
      Notification::make()
        ->title(__('gatekeeper::gatekeeper.reject_action.exception_title'))
        ->danger()
        ->body(__('gatekeeper::gatekeeper.reject_action.exception_message', ['error' => $e->getMessage()]))
        ->send();
    }
  }
}
