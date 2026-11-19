<?php

namespace App\Filament\Resources\Emails\Pages;

use App\Filament\Resources\Emails\EmailResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditEmail extends EditRecord
{
    protected static string $resource = EmailResource::class;

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
