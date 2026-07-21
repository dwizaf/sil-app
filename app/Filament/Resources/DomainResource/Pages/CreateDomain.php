<?php

namespace App\Filament\Resources\DomainResource\Pages;

use App\Filament\Resources\DomainResource;
use App\Models\Domain;
use Filament\Resources\Pages\CreateRecord;

class CreateDomain extends CreateRecord
{
    protected static string $resource = DomainResource::class;

    /**
     * Setelah domain dibuat, auto-detect kategori jika belum diisi.
     */
    protected function afterCreate(): void
    {
        $domain = $this->record;

        if (empty($domain->category)) {
            $category = $domain->autoDetectCategory();
            if ($category) {
                $domain->update(['category' => $category]);
            }
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
