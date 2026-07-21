<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Domain — {{ $periodLabel }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 10px;
            color: #1f2937;
            line-height: 1.5;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 12px;
            border-bottom: 3px solid #2563eb;
        }

        .header h1 {
            font-size: 18px;
            color: #1e3a5f;
            margin-bottom: 4px;
        }

        .header .subtitle {
            font-size: 12px;
            color: #6b7280;
        }

        .header .period {
            font-size: 13px;
            color: #2563eb;
            margin-top: 4px;
        }

        .meta-info {
            font-size: 9px;
            color: #4b5563;
            margin-bottom: 16px;
            width: 100%;
        }

        .meta-info td {
            padding: 2px 5px;
        }

        .stats-grid {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }

        .stat-card {
            display: table-cell;
            width: 25%;
            padding: 8px;
            text-align: center;
            border: 1px solid #e5e7eb;
            background-color: #f9fafb;
        }

        .stat-card .number {
            font-size: 20px;
            font-weight: bold;
        }

        .stat-card .label {
            font-size: 9px;
            color: #6b7280;
            text-transform: uppercase;
        }

        .stat-card.total .number { color: #2563eb; }
        .stat-card.active .number { color: #059669; }
        .stat-card.inactive .number { color: #dc2626; }

        .section-title {
            font-size: 13px;
            font-weight: bold;
            color: #1e3a5f;
            margin: 16px 0 8px 0;
            padding-bottom: 4px;
            border-bottom: 2px solid #e5e7eb;
        }

        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
            font-size: 9px;
        }

        table.data-table thead th {
            background-color: #1f2937;
            color: #ffffff;
            padding: 6px 8px;
            text-align: left;
            font-weight: bold;
            font-size: 9px;
            border: 1px solid #374151;
        }

        table.data-table tbody td {
            padding: 5px 8px;
            border: 1px solid #e5e7eb;
            vertical-align: top;
        }

        table.data-table tbody tr:nth-child(even) {
            background-color: #f9fafb;
        }

        .empty-state {
            text-align: center;
            padding: 20px;
            color: #6b7280;
            font-style: italic;
            border: 1px dashed #d1d5db;
        }
        
        .footer {
            margin-top: 30px;
            width: 100%;
        }

        .footer td {
            width: 50%;
            vertical-align: top;
        }

        .footer .signature {
            text-align: right;
            padding-right: 50px;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>Laporan Inventaris Domain</h1>
    </div>

    <table class="meta-info">
        <tr>
            <td width="15%"><strong>Waktu Daftar</strong></td>
            <td>: {{ $periodLabel }}</td>
            <td width="15%"><strong>Kategori</strong></td>
            <td>: {{ $filterCategory }}</td>
        </tr>
        <tr>
            <td><strong>Perangkat Daerah</strong></td>
            <td>: {{ $filterOpd }}</td>
            <td><strong>Status</strong></td>
            <td>: {{ $filterStatus }}</td>
        </tr>
    </table>

    <div class="stats-grid">
        <div class="stat-card total">
            <div class="number">{{ $totalDomains }}</div>
            <div class="label">Total Domain Filtered</div>
        </div>
        <div class="stat-card active">
            <div class="number">{{ $totalActive }}</div>
            <div class="label">Status Aktif</div>
        </div>
        <div class="stat-card inactive">
            <div class="number">{{ $totalInactive }}</div>
            <div class="label">Status Tidak Aktif</div>
        </div>
        <div class="stat-card" style="text-align: left; padding: 5px 10px;">
            <div class="label" style="text-align: left; margin-bottom: 3px;">Kategori:</div>
            <table style="width: 100%; font-size: 8px;">
                @foreach($categoryCounts as $label => $count)
                    @if($count > 0)
                        <tr>
                            <td>{{ $label }}</td>
                            <td style="text-align: right; font-weight: bold;">{{ $count }}</td>
                        </tr>
                    @endif
                @endforeach
            </table>
        </div>
    </div>

    <div class="section-title">Detail Laporan Data Domain</div>

    @if(count($domains) > 0)
        <table class="data-table">
            <thead>
                <tr>
                    <th width="3%">No</th>
                    <th width="15%">Domain</th>
                    <th width="20%">Perangkat Daerah (OPD)</th>
                    <th width="20%">Aplikasi & Keterangan</th>
                    <th width="12%">DNS / IP</th>
                    <th width="10%">Kategori</th>
                    <th width="10%">Status</th>
                    <th width="10%">Tanggal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($domains as $index => $domain)
                    <tr>
                        <td style="text-align: center;">{{ $index + 1 }}</td>
                        <td><strong>{{ $domain->name }}</strong></td>
                        <td>{{ $domain->opd ?? '-' }}</td>
                        <td>
                            @if($domain->app_name)
                                <strong>{{ $domain->app_name }}</strong><br>
                            @endif
                            @if($domain->app_description)
                                <span style="font-size: 8px; color: #6b7280;">{{ $domain->app_description }}</span>
                            @endif
                            @if(!$domain->app_name && !$domain->app_description)
                                -
                            @endif
                        </td>
                        <td>
                            <strong>{{ $domain->type }}</strong><br>
                            <span style="font-size: 8px; color: #4b5563;">{{ $domain->content ?? '-' }}</span>
                        </td>
                        <td>{{ \App\Models\Domain::categories()[$domain->category] ?? '-' }}</td>
                        <td>
                            @if($domain->status === \App\Models\Domain::STATUS_ACTIVE)
                                <span style="color: #059669; font-weight: bold;">Aktif</span>
                            @else
                                <span style="color: #dc2626; font-weight: bold;">Tidak Aktif</span><br>
                                <span style="font-size: 8px; color: #6b7280;">Sejak: {{ $domain->inactive_at ? $domain->inactive_at->format('d/m/Y') : '-' }}</span>
                            @endif
                        </td>
                        <td>{{ $domain->created_at->format('d/m/Y') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="empty-state">
            Tidak ada domain yang cocok dengan filter yang dipilih pada periode ini.
        </div>
    @endif

    <table class="footer">
        <tr>
            <td>
                Dicetak pada: {{ $generatedAt }}
            </td>
            <td class="signature">
                Mengetahui,<br>
                Pembuat Laporan<br><br><br><br>
                <strong>Dwi Zafira Kusbiyantarti</strong>
            </td>
        </tr>
    </table>

</body>
</html>
