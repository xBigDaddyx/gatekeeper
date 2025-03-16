<?php

namespace XBigDaddyx\Gatekeeper\Filament\Tables;

use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;

class ApproveAction extends Action
{
  protected function setUp(): void
  {
    parent::setUp();

    $this->label(__('approve_action.label'))
      ->icon('fluentui-checkmark-circle-20')
      ->modalHeading(__('approve_action.modal_heading'))
      ->modalDescription(__('approve_action.modal_description'))
      ->modalSubmitActionLabel(__('approve_action.modal_submit'))
      ->modalCancelActionLabel(__('approve_action.modal_cancel'))
      ->requiresConfirmation()
      ->action(fn (Model $record) => $this->approve($record));
  }

  protected function approve(Model $record): void
  {
    try {
      if (method_exists($record, 'approve')) {
        $record->approve();

        Notification::make()
          ->title(__('approve_action.success_title'))
          ->success()
          ->body(__('approve_action.success_message'))
          ->send();
      } else {
        Notification::make()
          ->title(__('approve_action.error_title'))
          ->danger()
          ->body(__('approve_action.error_message'))
          ->send();
      }
    } catch (\Exception $e) {
      Notification::make()
        ->title(__('approve_action.exception_title'))
        ->danger()
        ->body(__('approve_action.exception_message', ['error' => $e->getMessage()]))
        ->send();
    }
  }
}
