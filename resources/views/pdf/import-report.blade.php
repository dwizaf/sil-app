<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Import Domain</title>
    <style>
        body { font-family: sans-serif; font-size: 14px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #ddd; padding-bottom: 10px; }
        .info-table { width: 100%; margin-bottom: 20px; }
        .info-table td { padding: 5px 0; }
        .section-title { font-size: 16px; font-weight: bold; background: #f4f4f4; padding: 5px; margin-top: 20px; border: 1px solid #ddd; }
        .list-box { border: 1px solid #ddd; border-top: none; padding: 10px; min-height: 20px; }
        ul { margin: 0; padding-left: 20px; }
        li { margin-bottom: 3px; }
        .text-success { color: green; }
        .text-danger { color: red; }
        .text-warning { color: orange; }
        .text-info { color: blue; }
    </style>
</head>
<body>

<div class="header">
    <h2>Laporan Sinkronisasi Domain & Aplikasi</h2>
    <p>Tanggal: {{ $log->created_at->format('d F Y, H:i') }}</p>
</div>

<table class="info-table">
    <tr>
        <td width="30%"><strong>Diimpor Oleh (Sistem)</strong></td>
        <td>: {{ $log->importer->name ?? 'Sistem' }}</td>
    </tr>
    <tr>
        <td><strong>Pembuat Laporan</strong></td>
        <td>: Dwi Zafira Kusbiyantarti</td>
    </tr>
    <tr>
        <td><strong>Nama File Excel</strong></td>
        <td>: {{ $log->filename }}</td>
    </tr>
    <tr>
        <td><strong>Total Baris Dibaca</strong></td>
        <td>: {{ $log->total_records }}</td>
    </tr>
</table>

@php
    $summary = $log->summary ?? [];
    $new = $summary['new'] ?? [];
    $updated = $summary['updated'] ?? [];
    $deactivated = $summary['deactivated'] ?? [];
    $reactivated = $summary['reactivated'] ?? [];
@endphp

<div class="section-title text-success">
    Domain Baru Ditambahkan ({{ count($new) }})
</div>
<div class="list-box">
    @if(count($new) > 0)
        <ul>
            @foreach($new as $domain)
                <li>{{ $domain }}</li>
            @endforeach
        </ul>
    @else
        <p><i>Tidak ada domain baru.</i></p>
    @endif
</div>

<div class="section-title text-info">
    Domain Diperbarui ({{ count($updated) }})
</div>
<div class="list-box">
    @if(count($updated) > 0)
        <ul>
            @foreach($updated as $domain)
                <li>{{ $domain }}</li>
            @endforeach
        </ul>
    @else
        <p><i>Tidak ada domain yang diperbarui.</i></p>
    @endif
</div>

<div class="section-title text-danger">
    Domain Dinonaktifkan ({{ count($deactivated) }})
</div>
<div class="list-box">
    @if(count($deactivated) > 0)
        <ul>
            @foreach($deactivated as $domain)
                <li>{{ $domain }}</li>
            @endforeach
        </ul>
    @else
        <p><i>Tidak ada domain yang dinonaktifkan.</i></p>
    @endif
</div>

<div class="section-title text-warning">
    Domain Diaktifkan Kembali ({{ count($reactivated) }})
</div>
<div class="list-box">
    @if(count($reactivated) > 0)
        <ul>
            @foreach($reactivated as $domain)
                <li>{{ $domain }}</li>
            @endforeach
        </ul>
    @else
        <p><i>Tidak ada domain yang diaktifkan kembali.</i></p>
    @endif
</div>

</body>
</html>
