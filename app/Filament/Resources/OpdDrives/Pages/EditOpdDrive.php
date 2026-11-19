<?php

namespace App\Filament\Resources\OpdDrives\Pages;

use App\Filament\Resources\OpdDrives\OpdDriveResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditOpdDrive extends EditRecord
{
    protected static string $resource = OpdDriveResource::class;

    public static function canAccess(array $parameters = []): bool
    {
        return auth()->user()?->canModifyData() ?? false;
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
