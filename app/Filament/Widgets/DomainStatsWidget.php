<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DomainStatsWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 1;
    protected int | string | array $columnSpan = 1;

    protected function getColumns(): int
    {
        return 2;
    }

    protected function getStats(): array
    {
        $activeDomains = \App\Models\Domain::where('status', 'active')->count();
        $inactiveDomains = \App\Models\Domain::where('status', 'inactive')->count();
        $totalOpds = \App\Models\Domain::whereNotNull('opd')->distinct('opd')->count('opd');
        $activeApps = \App\Models\Domain::where('status', 'active')->whereNotNull('app_name')->distinct('app_name')->count('app_name');

        return [
            Stat::make('Aplikasi Aktif', $activeApps)
                ->description('Aplikasi yang diakses via domain')
                ->descriptionIcon('heroicon-m-window')
                ->color('primary'),

            Stat::make('Domain Aktif', $activeDomains)
                ->description('Total domain yang aktif')
                ->descriptionIcon('heroicon-m-globe-alt')
                ->color('success'),

            Stat::make('Perangkat Daerah', $totalOpds)
                ->description('OPD terdaftar')
                ->descriptionIcon('heroicon-m-building-office')
                ->color('info'),

            Stat::make('Domain Tidak Aktif', $inactiveDomains)
                ->description('Domain tidak aktif / dicabut')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color('danger'),
        ];
    }
}
