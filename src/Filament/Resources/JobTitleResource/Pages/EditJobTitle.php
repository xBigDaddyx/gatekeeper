<?php

namespace Xbigdaddyx\Gatekeeper\Filament\Resources\JobTitleResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Xbigdaddyx\Gatekeeper\Filament\Resources\JobTitleResource;

class EditJobTitle extends EditRecord
{
    protected static string $resource = JobTitleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
