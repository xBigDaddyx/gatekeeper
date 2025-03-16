<?php

namespace Xbigdaddyx\Gatekeeper\Filament\Resources\JobTitleResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Xbigdaddyx\Gatekeeper\Filament\Resources\JobTitleResource;

class ListJobTitles extends ListRecords
{
    protected static string $resource = JobTitleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
