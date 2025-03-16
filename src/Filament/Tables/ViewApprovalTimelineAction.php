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

    $this->label(__('gatekeeper::gatekeeper.approval_timeline_action.label'))
      ->slideOver()
      ->modal()
      ->modalIcon('fluentui-timeline-20')
      ->icon('fluentui-timeline-20')
      ->modalHeading(fn (Model $record) => __('gatekeeper::gatekeeper.approval_timeline_action.modal_heading', [
        'id' => $record->id,
        'model' => class_basename($record),
      ]))
      ->modalContent(fn (Model $record) => view('gatekeeper:components.activity-timeline', [
        'pendingApprovals' => $record->pendingApprovals()->with('user')->latest()->get(),
        'approvalHistory' => $record->approvalHistory()->with('user')->latest()->get(),
        'modelName' => class_basename($record),
      ]))
      ->modalWidth(MaxWidth::ExtraLarge)
      ->modalSubmitAction(false)
      ->modalCancelActionLabel(__('gatekeeper::gatekeeper.approval_timeline_action.modal_cancel_label'))
      ->tooltip(__('gatekeeper::gatekeeper.approval_timeline_action.tooltip'));
  }
}
