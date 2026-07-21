<?php

namespace App\Exports;

use App\Models\Domain;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class DomainReportExport implements FromCollection, WithHeadings, WithTitle, WithStyles, ShouldAutoSize
{
    protected Collection $domains;
    protected string $periodLabel;
    protected string $filterOpd;

    public function __construct(Collection $domains, string $periodLabel, string $filterOpd)
    {
        $this->domains = $domains;
        $this->periodLabel = $periodLabel;
        $this->filterOpd = $filterOpd;
    }

    public function collection()
    {
        return $this->domains->map(function ($domain, $index) {
            return [
                'no' => $index + 1,
                'name' => $domain->name,
                'opd' => $domain->opd ?? '-',
                'app_name' => $domain->app_name ?? '-',
                'app_description' => $domain->app_description ?? '-',
                'type' => $domain->type,
                'content' => $domain->content,
                'category' => Domain::categories()[$domain->category] ?? '-',
                'status' => $domain->status === Domain::STATUS_ACTIVE ? 'Aktif' : 'Tidak Aktif',
                'proxied' => $domain->proxied ? 'Ya' : 'Tidak',
                'created_at' => $domain->created_at->format('d M Y'),
                'inactive_at' => $domain->inactive_at ? $domain->inactive_at->format('d M Y') : '-',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'No', 
            'Domain', 
            'Perangkat Daerah (OPD)', 
            'Nama Aplikasi', 
            'Keterangan', 
            'Tipe DNS', 
            'Content / IP', 
            'Kategori', 
            'Status', 
            'Proxy CF', 
            'Tanggal Daftar',
            'Tanggal Nonaktif'
        ];
    }

    public function title(): string
    {
        return 'Data Domain';
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '1F2937'], // Dark Gray
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }
}
