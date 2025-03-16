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

        $this->label(__('reject_action.label'))
            ->icon('fluentui-dismiss-circle-20')
            ->modalHeading(__('reject_action.modal_heading'))
            ->modalDescription(__('reject_action.modal_description'))
            ->modalSubmitActionLabel(__('reject_action.modal_submit'))
            ->modalCancelActionLabel(__('reject_action.modal_cancel'))
            ->requiresConfirmation()
            ->form([
                Textarea::make('reason')
                    ->label(__('reject_action.reason_label'))
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
                    ->title(__('reject_action.success_title'))
                    ->success()
                    ->body(__('reject_action.success_message'))
                    ->send();
            } else {
                Notification::make()
                    ->title(__('reject_action.error_title'))
                    ->danger()
                    ->body(__('reject_action.error_message'))
                    ->send();
            }
        } catch (\Exception $e) {
            Notification::make()
                ->title(__('reject_action.exception_title'))
                ->danger()
                ->body(__('reject_action.exception_message', ['error' => $e->getMessage()]))
                ->send();
        }
    }
}
