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
            \Filament\Actions\Action::make('public_form')
                ->label('Buka Form Public OPD')
                ->icon('heroicon-o-arrow-top-right-on-square')
                ->color('success')
                ->url(url('/pendataan-pse'))
                ->openUrlInNewTab(),
            CreateAction::make()
                ->visible(fn () => auth()->user()?->canModifyData()),
        ];
    }
}
