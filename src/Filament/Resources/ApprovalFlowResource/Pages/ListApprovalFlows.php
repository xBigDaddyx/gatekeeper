<?php

namespace Xbigdaddyx\Gatekeeper\Filament\Resources\ApprovalFlowResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Xbigdaddyx\Gatekeeper\Filament\Resources\ApprovalFlowResource;

class ListApprovalFlows extends ListRecords
{
    protected static string $resource = ApprovalFlowResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
