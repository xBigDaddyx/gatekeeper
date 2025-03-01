<?php

namespace Xbigdaddyx\Gatekeeper\Filament\Resources\JobTitleResource\Pages;

use Xbigdaddyx\Gatekeeper\Filament\Resources\JobTitleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

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
