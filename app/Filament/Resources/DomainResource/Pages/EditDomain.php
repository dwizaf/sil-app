<?php

namespace App\Filament\Resources\DomainResource\Pages;

use App\Filament\Resources\DomainResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDomain extends EditRecord
{
    protected static string $resource = DomainResource::class;

    public static function canAccess(array $parameters = []): bool
    {
        return auth()->user()?->canModifyData() ?? false;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
