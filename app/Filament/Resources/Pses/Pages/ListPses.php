<?php

namespace App\Filament\Resources\Pses\Pages;

use App\Filament\Resources\Pses\PseResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPses extends ListRecords
{
    protected static string $resource = PseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
