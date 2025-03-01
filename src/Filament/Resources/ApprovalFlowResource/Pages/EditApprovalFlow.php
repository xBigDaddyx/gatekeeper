<?php

namespace Xbigdaddyx\Gatekeeper\Filament\Resources\ApprovalFlowResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Xbigdaddyx\Gatekeeper\Filament\Resources\ApprovalFlowResource;

class EditApprovalFlow extends EditRecord
{
    protected static string $resource = ApprovalFlowResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
