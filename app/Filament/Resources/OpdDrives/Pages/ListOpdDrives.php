<?php

namespace App\Filament\Resources\OpdDrives\Pages;

use App\Filament\Resources\OpdDrives\OpdDriveResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListOpdDrives extends ListRecords
{
    protected static string $resource = OpdDriveResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
