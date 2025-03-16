<?php

namespace XBigDaddyx\Gatekeeper\Filament\Tables;

use Filament\Support\Enums\MaxWidth;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Model;

class ViewApprovalTimelineAction extends Action
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('approval_timeline_action.label'))
            ->slideOver()
            ->modal()
            ->modalIcon('fluentui-timeline-20')
            ->icon('fluentui-timeline-20')
            ->modalHeading(fn (Model $record) => __('approval_timeline_action.modal_heading', ['id' => $record->id]))
            ->modalContent(fn (Model $record) => view('components.approval-timeline', [
                'pendingApprovals' => $record->pendingApprovals()->latest()->get(),
                'approvalHistory' => $record->approvalHistory()->latest()->get(),
                'modelName' => class_basename($record),
            ]))
            ->modalWidth(MaxWidth::ExtraLarge)
            ->modalSubmitAction(false)
            ->modalCancelActionLabel(__('approval_timeline_action.modal_cancel_label'));
    }
}
