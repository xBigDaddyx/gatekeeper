<?php

namespace XBigDaddyx\Gatekeeper\Filament\Tables;

use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Model;

class SubmitApprovalAction extends Action
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('gatekeeper::submit_approval_action.label'))
            ->icon('fluentui-checkmark-lock-20')
            ->modalHeading(__('gatekeeper::submit_approval_action.modal_heading'))
            ->modalDescription(__('gatekeeper::submit_approval_action.modal_description'))
            ->modalSubmitActionLabel(__('gatekeeper::submit_approval_action.modal_submit'))
            ->modalCancelActionLabel(__('gatekeeper::submit_approval_action.modal_cancel'))
            ->requiresConfirmation()
            ->action(fn (Model $record) => $this->submitApproval($record));
    }

    protected function submitApproval(Model $record): void
    {
        try {
            if (method_exists($record, 'submitForApproval')) {
                $record->submitForApproval();

                Notification::make()
                    ->title(__('gatekeeper::submit_approval_action.success_title'))
                    ->success()
                    ->body(__('gatekeeper::submit_approval_action.success_message'))
                    ->send();
            } else {
                Notification::make()
                    ->title(__('gatekeeper::submit_approval_action.error_title'))
                    ->danger()
                    ->body(__('gatekeeper::submit_approval_action.error_message'))
                    ->send();
            }
        } catch (\Exception $e) {
            Notification::make()
                ->title(__('gatekeeper::submit_approval_action.exception_title'))
                ->danger()
                ->body(__('gatekeeper::submit_approval_action.exception_message', ['error' => $e->getMessage()]))
                ->send();
        }
    }
}
