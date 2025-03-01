<?php

namespace Xbigdaddyx\Gatekeeper\Filament\Resources\JobTitleResource\Pages;

use Xbigdaddyx\Gatekeeper\Filament\Resources\JobTitleResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

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
