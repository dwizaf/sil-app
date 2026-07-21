<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

class DomainMonthlyChart extends ChartWidget
{
    protected static ?int $sort = 2;
    protected ?string $heading = 'Grafik Pertumbuhan Domain (6 Bulan Terakhir)';
    protected int | string | array $columnSpan = 1;

    protected function getData(): array
    {
        $months = [];
        $added = [];
        $removed = [];

        $now = \Carbon\Carbon::now()->startOfMonth();

        for ($i = 5; $i >= 0; $i--) {
            $month = $now->copy()->subMonths($i);
            $months[] = $month->translatedFormat('M Y');

            $added[] = \App\Models\Domain::whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->count();

            $removed[] = \App\Models\Domain::whereYear('inactive_at', $month->year)
                ->whereMonth('inactive_at', $month->month)
                ->count();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Domain Ditambahkan',
                    'data' => $added,
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.2)',
                    'fill' => true,
                ],
                [
                    'label' => 'Domain Dinonaktifkan',
                    'data' => $removed,
                    'borderColor' => '#ef4444',
                    'backgroundColor' => 'rgba(239, 68, 68, 0.2)',
                    'fill' => true,
                ],
            ],
            'labels' => $months,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
