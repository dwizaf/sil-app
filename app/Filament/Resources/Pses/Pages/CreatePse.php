<?php

namespace App\Filament\Resources\Pses\Pages;

use App\Filament\Resources\Pses\PseResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePse extends CreateRecord
{
    protected static string $resource = PseResource::class;

    public static function canAccess(array $parameters = []): bool
    {
        return auth()->user()?->canModifyData() ?? false;
    }
}
