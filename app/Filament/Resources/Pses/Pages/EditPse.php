<?php

namespace App\Filament\Resources\Pses\Pages;

use App\Filament\Resources\Pses\PseResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPse extends EditRecord
{
    protected static string $resource = PseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
