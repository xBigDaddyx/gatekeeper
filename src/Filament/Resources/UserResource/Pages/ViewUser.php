<?php

namespace XBigDaddyx\Gatekeeper\Filament\Resources\UserResource\Pages;

use XBigDaddyx\Gatekeeper\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewUser extends ViewRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
