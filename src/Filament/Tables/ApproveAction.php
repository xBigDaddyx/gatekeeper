<?php

namespace XBigDaddyx\Gatekeeper\Filament\Tables;

use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ApproveAction extends Action
{
  protected function setUp(): void
  {
    parent::setUp();

    $this->label(__('gatekeeper::gatekeeper.approve_action.label'))
      ->icon('heroicon-o-check-circle')
      ->color('success')
      ->modalHeading(__('gatekeeper::gatekeeper.approve_action.modal_heading'))
      ->modalDescription(__('gatekeeper::gatekeeper.approve_action.modal_description'))
      ->modalSubmitActionLabel(__('gatekeeper::gatekeeper.approve_action.modal_submit'))
      ->modalCancelActionLabel(__('gatekeeper::gatekeeper.approve_action.modal_cancel'))
      ->requiresConfirmation()
      ->form([
        \Filament\Forms\Components\Textarea::make('comment')
          ->label('Comment')
          ->nullable()
      ])
      ->visible(fn ($record) => $record->canBeApprovedBy(Auth::user()))
      ->action(fn (Model $record, array $data) => $this->approve($record, Auth::user(), $data['comment']));
  }

  protected function approve(Model $record, $user, ?string $comment = null): void
  {
    try {
      if (method_exists($record, 'approve')) {
        $record->approve($user, $comment);

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
