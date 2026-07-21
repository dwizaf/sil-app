<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class EmailStatsWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 4;
    protected int | string | array $columnSpan = 1;

    protected function getStats(): array
    {
        return [
            Stat::make('Akun Email', \App\Models\Email::count())
                ->description('Total akun email instansi terdaftar')
                ->descriptionIcon('heroicon-m-envelope')
                ->color('primary'),
        ];
    }
}
