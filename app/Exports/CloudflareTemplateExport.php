<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Collection;

class CloudflareTemplateExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return new Collection([
            [
                'example.go.id',
                'Badan Kepegawaian Daerah',
                'Sistem Informasi Manajemen',
                'Digunakan untuk internal pegawai',
                'A',
                '103.111.222.123',
                '300',
                'false',
                'Pemerintah',
                'active'
            ],
            [
                'app.example.go.id',
                'Badan Kepegawaian Daerah',
                'Aplikasi Layanan Publik',
                'Portal untuk warga',
                'CNAME',
                'target.server.com',
                '1',
                'true',
                'Lainnya',
                'active'
            ]
        ]);
    }

    public function headings(): array
    {
        return [
            'Name',
            'OPD',
            'App Name',
            'App Description',
            'Type',
            'Content',
            'TTL',
            'Proxied',
            'Category',
            'Status'
        ];
    }
}
