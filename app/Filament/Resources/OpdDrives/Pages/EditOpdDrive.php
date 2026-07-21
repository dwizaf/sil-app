<?php

namespace App\Filament\Resources\OpdDrives\Pages;

use App\Filament\Resources\OpdDrives\OpdDriveResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditOpdDrive extends EditRecord
{
    protected static string $resource = OpdDriveResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
