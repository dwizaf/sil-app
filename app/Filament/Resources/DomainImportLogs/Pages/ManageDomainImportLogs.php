<?php

namespace App\Filament\Resources\DomainImportLogs\Pages;

use App\Filament\Resources\DomainImportLogs\DomainImportLogResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageDomainImportLogs extends ManageRecords
{
    protected static string $resource = DomainImportLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
