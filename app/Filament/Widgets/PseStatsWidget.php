<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PseStatsWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 1;

    protected function getStats(): array
    {
        return [
            Stat::make('PSE Terdaftar', \App\Models\Pse::where('status', 'terdaftar')->count())
                ->description('Penyelenggara Sistem Elektronik (Komdigi)')
                ->descriptionIcon('heroicon-m-shield-check')
                ->color('success'),
            
            Stat::make('PSE Belum/Diproses', \App\Models\Pse::where('status', '!=', 'terdaftar')->count())
                ->description('Menunggu pendaftaran/sertifikat')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),
        ];
    }
}
