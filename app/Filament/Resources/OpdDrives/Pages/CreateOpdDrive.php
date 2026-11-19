<?php

namespace App\Filament\Resources\OpdDrives\Pages;

use App\Filament\Resources\OpdDrives\OpdDriveResource;
use Filament\Resources\Pages\CreateRecord;

class CreateOpdDrive extends CreateRecord
{
    protected static string $resource = OpdDriveResource::class;

    public static function canAccess(array $parameters = []): bool
    {
        return auth()->user()?->canModifyData() ?? false;
    }
}
