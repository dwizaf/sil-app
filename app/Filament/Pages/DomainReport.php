<?php

namespace App\Filament\Pages;

use App\Models\Domain;
use App\Exports\DomainReportExport;
use BackedEnum;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Schema;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;
use UnitEnum;

class DomainReport extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-document-chart-bar';

    protected static ?string $navigationLabel = 'Laporan Domain';

    protected static string | UnitEnum | null $navigationGroup = 'Manajemen Domain';

    protected static ?int $navigationSort = 2;

    protected static ?string $title = 'Laporan Domain';

    protected static ?string $slug = 'domain-report';

    protected string $view = 'filament.pages.domain-report';

    /*
    |--------------------------------------------------------------------------
    | Form Properties
    |--------------------------------------------------------------------------
    */

    public ?string $filter_opd = 'all';
    public ?string $filter_status = 'all';
    public ?string $filter_category = 'all';
    public ?string $filter_period_mode = 'all'; // 'all', 'month', 'range'
    public ?string $filter_month = null;
    public ?string $filter_year = null;
    public ?string $date_from = null;
    public ?string $date_until = null;

    /*
    |--------------------------------------------------------------------------
    | Computed Data
    |--------------------------------------------------------------------------
    */

    public bool $hasResults = false;
    public int $totalDomains = 0;
    public int $totalActive = 0;
    public int $totalInactive = 0;
    public array $categoryCounts = [];
    public Collection $domains;
    public string $periodLabel = 'Semua Waktu';
    public ?string $startDate = null;
    public ?string $endDate = null;

    public function mount(): void
    {
        $this->filter_month = (string) now()->month;
        $this->filter_year = (string) now()->year;
        $this->domains = collect();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                \Filament\Schemas\Components\Section::make('Filter Laporan')
                    ->schema([
                        \Filament\Schemas\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\Select::make('filter_opd')
                                    ->label('Perangkat Daerah (OPD)')
                                    ->options(function () {
                                        $opds = Domain::whereNotNull('opd')->where('opd', '!=', '')->distinct()->pluck('opd', 'opd')->toArray();
                                        return array_merge(['all' => 'Semua OPD'], $opds);
                                    })
                                    ->default('all')
                                    ->searchable(),

                                Forms\Components\Select::make('filter_status')
                                    ->label('Status Domain')
                                    ->options([
                                        'all' => 'Semua Status',
                                        Domain::STATUS_ACTIVE => 'Aktif',
                                        Domain::STATUS_INACTIVE => 'Tidak Aktif',
                                    ])
                                    ->default('all'),

                                Forms\Components\Select::make('filter_category')
                                    ->label('Kategori')
                                    ->options(array_merge(['all' => 'Semua Kategori'], Domain::categories()))
                                    ->default('all'),
                            ]),

                        \Filament\Schemas\Components\Grid::make(1)
                            ->schema([
                                Forms\Components\ToggleButtons::make('filter_period_mode')
                                    ->label('Filter Waktu (Pendaftaran)')
                                    ->options([
                                        'all' => 'Semua Waktu',
                                        'month' => 'Per Bulan',
                                        'range' => 'Rentang Tanggal',
                                    ])
                                    ->default('all')
                                    ->inline()
                                    ->live(),
                            ]),

                        \Filament\Schemas\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('filter_month')
                                    ->label('Bulan')
                                    ->options([
                                        '1' => 'Januari', '2' => 'Februari', '3' => 'Maret', '4' => 'April',
                                        '5' => 'Mei', '6' => 'Juni', '7' => 'Juli', '8' => 'Agustus',
                                        '9' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember',
                                    ])
                                    ->default((string) now()->month),

                                Forms\Components\Select::make('filter_year')
                                    ->label('Tahun')
                                    ->options(function () {
                                        $years = [];
                                        for ($y = now()->year; $y >= now()->year - 5; $y--) {
                                            $years[(string) $y] = (string) $y;
                                        }
                                        return $years;
                                    })
                                    ->default((string) now()->year),
                            ])
                            ->visible(fn($get) => $get('filter_period_mode') === 'month'),

                        \Filament\Schemas\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\DatePicker::make('date_from')
                                    ->label('Tanggal Mulai'),

                                Forms\Components\DatePicker::make('date_until')
                                    ->label('Tanggal Akhir'),
                            ])
                            ->visible(fn($get) => $get('filter_period_mode') === 'range'),
                    ])
                    ->columns(1),
            ]);
    }

    /**
     * Terapkan filter dan ambil data.
     */
    public function applyFilter(): void
    {
        $query = Domain::query();

        // 1. Filter Waktu
        if ($this->filter_period_mode === 'month') {
            $start = Carbon::create((int) $this->filter_year, (int) $this->filter_month, 1)->startOfDay();
            $end = $start->copy()->endOfMonth()->endOfDay();
            $query->whereBetween('created_at', [$start, $end]);
            
            $monthNames = [
                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
            ];
            $this->periodLabel = $monthNames[(int) $this->filter_month] . ' ' . $this->filter_year;
            $this->startDate = $start->toDateString();
            $this->endDate = $end->toDateString();
        } elseif ($this->filter_period_mode === 'range') {
            if ($this->date_from && $this->date_until) {
                $start = Carbon::parse($this->date_from)->startOfDay();
                $end = Carbon::parse($this->date_until)->endOfDay();
                $query->whereBetween('created_at', [$start, $end]);
                $this->periodLabel = $start->format('d M Y') . ' — ' . $end->format('d M Y');
                $this->startDate = $start->toDateString();
                $this->endDate = $end->toDateString();
            } else {
                $this->periodLabel = 'Rentang Tanggal Tidak Lengkap';
            }
        } else {
            $this->periodLabel = 'Semua Waktu';
            $this->startDate = null;
            $this->endDate = null;
        }

        // 2. Filter OPD
        if ($this->filter_opd !== 'all') {
            $query->where('opd', $this->filter_opd);
        }

        // 3. Filter Status
        if ($this->filter_status !== 'all') {
            $query->where('status', $this->filter_status);
        }

        // 4. Filter Kategori
        if ($this->filter_category !== 'all') {
            $query->where('category', $this->filter_category);
        }

        $this->domains = $query->orderBy('created_at', 'desc')->get();

        // Statistik
        $this->totalDomains = $this->domains->count();
        $this->totalActive = $this->domains->where('status', Domain::STATUS_ACTIVE)->count();
        $this->totalInactive = $this->domains->where('status', Domain::STATUS_INACTIVE)->count();
        
        $this->categoryCounts = [];
        foreach (Domain::categories() as $key => $label) {
            $this->categoryCounts[$label] = $this->domains->where('category', $key)->count();
        }

        $this->hasResults = true;
    }

    /**
     * Ekspor laporan ke Excel.
     */
    public function exportExcel()
    {
        if (!$this->hasResults) {
            Notification::make()->title('Terapkan filter terlebih dahulu')->warning()->send();
            return;
        }

        return Excel::download(
            new DomainReportExport($this->domains, $this->periodLabel, $this->filter_opd),
            'laporan-domain-' . now()->format('YmdHis') . '.xlsx'
        );
    }

    /**
     * Ekspor laporan ke PDF.
     */
    public function exportPdf()
    {
        if (!$this->hasResults) {
            Notification::make()->title('Terapkan filter terlebih dahulu')->warning()->send();
            return;
        }

        $pdf = Pdf::loadView('exports.domain-report-pdf', [
            'periodLabel' => $this->periodLabel,
            'filterOpd' => $this->filter_opd !== 'all' ? $this->filter_opd : 'Semua OPD',
            'filterStatus' => $this->filter_status !== 'all' ? ($this->filter_status === Domain::STATUS_ACTIVE ? 'Aktif' : 'Tidak Aktif') : 'Semua Status',
            'filterCategory' => $this->filter_category !== 'all' ? (Domain::categories()[$this->filter_category] ?? 'Lainnya') : 'Semua Kategori',
            'totalDomains' => $this->totalDomains,
            'totalActive' => $this->totalActive,
            'totalInactive' => $this->totalInactive,
            'categoryCounts' => $this->categoryCounts,
            'domains' => $this->domains,
            'generatedAt' => now()->format('d M Y H:i'),
        ]);

        $pdf->setPaper('a4', 'landscape');

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'laporan-domain-' . now()->format('YmdHis') . '.pdf');
    }
}
