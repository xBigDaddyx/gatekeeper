<?php

namespace Xbigdaddyx\Gatekeeper\Filament\Resources\ApprovalFlowResource\Pages;

use Xbigdaddyx\Gatekeeper\Filament\Resources\ApprovalFlowResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

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
