<?php

namespace XBigDaddyx\Gatekeeper\Filament\Resources\UserResource\Pages;

use XBigDaddyx\Gatekeeper\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
