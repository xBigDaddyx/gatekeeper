<?php

namespace Xbigdaddyx\Gatekeeper\Filament\Resources\JobTitleResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Xbigdaddyx\Gatekeeper\Filament\Resources\JobTitleResource;

class CreateJobTitle extends CreateRecord
{
    protected static string $resource = JobTitleResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return $data;
    }

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        return static::getModel()::create($data);
    }
}
