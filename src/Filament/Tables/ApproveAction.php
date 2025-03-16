<?php

namespace XBigDaddyx\Gatekeeper\Filament\Tables;

use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Model;

class ApproveAction extends Action
{
  protected function setUp(): void
  {
    parent::setUp();

    $this->label(__('gatekeeper::gatekeeper.approve_action.label'))
      ->icon('fluentui-checkmark-circle-20')
      ->modalHeading(__('gatekeeper::gatekeeper.approve_action.modal_heading'))
      ->modalDescription(__('gatekeeper::gatekeeper.approve_action.modal_description'))
      ->modalSubmitActionLabel(__('gatekeeper::gatekeeper.approve_action.modal_submit'))
      ->modalCancelActionLabel(__('gatekeeper::gatekeeper.approve_action.modal_cancel'))
      ->requiresConfirmation()
      ->action(fn (Model $record) => $this->approve(auth()->user()));
  }

  protected function approve(Model $record): void
  {
    try {
      if (method_exists($record, 'approve')) {
        $record->approve();

        Notification::make()
          ->title(__('gatekeeper::gatekeeper.approve_action.success_title'))
          ->success()
          ->body(__('gatekeeper::gatekeeper.approve_action.success_message'))
          ->send();
      } else {
        Notification::make()
          ->title(__('gatekeeper::gatekeeper.approve_action.error_title'))
          ->danger()
          ->body(__('gatekeeper::gatekeeper.approve_action.error_message'))
          ->send();
      }
    } catch (\Exception $e) {
      Notification::make()
        ->title(__('gatekeeper::gatekeeper.approve_action.exception_title'))
        ->danger()
        ->body(__('gatekeeper::gatekeeper.approve_action.exception_message', ['error' => $e->getMessage()]))
        ->send();
    }
  }
}
