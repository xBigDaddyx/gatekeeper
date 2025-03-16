<?php

namespace XBigDaddyx\Gatekeeper\Filament\Tables;

use Filament\Notifications\Actions\Action as NotificationAction;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SubmitApprovalAction extends Action
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('gatekeeper::gatekeeper.submit_approval_action.label'))
            ->icon('fluentui-checkmark-lock-20')
            ->color('primary')
            ->modalHeading(__('gatekeeper::gatekeeper.submit_approval_action.modal_heading'))
            ->modalDescription(__('gatekeeper::gatekeeper.submit_approval_action.modal_description'))
            ->modalSubmitActionLabel(__('gatekeeper::gatekeeper.submit_approval_action.modal_submit'))
            ->modalCancelActionLabel(__('gatekeeper::gatekeeper.submit_approval_action.modal_cancel'))
            ->requiresConfirmation()
            ->form([
                \Filament\Forms\Components\Textarea::make('comment')
                    ->label(__('gatekeeper::gatekeeper.submit_approval_action.comment_label'))
                    ->nullable()
                    ->placeholder(__('gatekeeper::gatekeeper.submit_approval_action.comment_placeholder')),
            ])
            ->visible(fn ($record) => ! $record->isPending() && ! $record->isApproved() && ! $record->isRejected())
            ->disabled(fn ($record) => $record->isPending() || $record->isApproved() || $record->isRejected())
            ->tooltip(fn ($record) => $record->isPending() ? __('gatekeeper::gatekeeper.submit_approval_action.already_pending') : ($record->isApproved() ? __('gatekeeper::gatekeeper.submit_approval_action.already_approved') : ($record->isRejected() ? __('gatekeeper::gatekeeper.submit_approval_action.already_rejected') : null)))
            ->action(fn (Model $record, array $data) => $this->submitApproval($record, Auth::user(), $data['comment']));
    }

    protected function submitApproval(Model $record, $user, ?string $comment = null): void
    {
        try {
            if (method_exists($record, 'submitForApproval')) {
                $record->submitForApproval($user, $comment);

                Log::info('Submitted for approval', [
                    'record_type' => get_class($record),
                    'record_id' => $record->id,
                    'user_id' => $user->id,
                    'comment' => $comment,
                ]);

                Notification::make()
                    ->title(__('gatekeeper::gatekeeper.submit_approval_action.success_title'))
                    ->success()
                    ->body(__('gatekeeper::gatekeeper.submit_approval_action.success_message', ['id' => $record->id]))
                    ->actions([
                        NotificationAction::make('view_history')
                            ->label(__('gatekeeper::gatekeeper.approval_timeline_action.label'))
                            ->url(fn () => route('filament.resources.' . strtolower(class_basename($record)) . '.index') . '?tableAction=view_approval_timeline&record=' . $record->id)
                            ->openUrlInNewTab(),
                    ])
                    ->send();
            } else {
                throw new \Exception('Submit for approval method not implemented.');
            }
        } catch (\Exception $e) {
            Log::error('Submission failed', [
                'record_type' => get_class($record),
                'record_id' => $record->id,
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            Notification::make()
                ->title(__('gatekeeper::gatekeeper.submit_approval_action.exception_title'))
                ->danger()
                ->body(__('gatekeeper::gatekeeper.submit_approval_action.exception_message', ['error' => $e->getMessage()]))
                ->send();
        }
    }
}
