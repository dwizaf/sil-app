<?php

namespace App\Filament\Resources\Pses\Pages;

use App\Filament\Resources\Pses\PseResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPse extends EditRecord
{
    protected static string $resource = PseResource::class;

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
