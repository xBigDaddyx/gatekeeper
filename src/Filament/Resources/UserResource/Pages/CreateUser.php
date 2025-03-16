<?php

namespace XBigDaddyx\Gatekeeper\Filament\Resources\UserResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use XBigDaddyx\Gatekeeper\Filament\Resources\UserResource;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;
}
