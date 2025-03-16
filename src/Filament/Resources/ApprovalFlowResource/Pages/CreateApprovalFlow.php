<?php

namespace Xbigdaddyx\Gatekeeper\Filament\Resources\ApprovalFlowResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Xbigdaddyx\Gatekeeper\Filament\Resources\ApprovalFlowResource;

class CreateApprovalFlow extends CreateRecord
{
    protected static string $resource = ApprovalFlowResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return $data;
    }

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        return static::getModel()::create($data);
    }
}
