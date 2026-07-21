<x-filament-panels::page>
    {{-- Filter Form --}}
    <form wire:submit="applyFilter">
        {{ $this->form }}

        <div style="margin-top: 1rem; display: flex; gap: 0.75rem;">
            <x-filament::button type="submit" icon="heroicon-o-funnel">
                Terapkan Filter
            </x-filament::button>

            @if($hasResults)
                <x-filament::button color="success" wire:click="exportExcel" icon="heroicon-o-arrow-down-tray">
                    Ekspor Excel
                </x-filament::button>

                <x-filament::button color="danger" wire:click="exportPdf" icon="heroicon-o-document-arrow-down">
                    Ekspor PDF
                </x-filament::button>
            @endif
        </div>
    </form>

    @if($hasResults)
        {{-- Periode Label --}}
        <div style="margin-top: 1.5rem; margin-bottom: 1rem;">
            <h2 style="font-size: 1.125rem; font-weight: bold;">
                📊 Laporan Domain
            </h2>
            <p style="font-size: 0.875rem; color: #6b7280; margin-top: 0.25rem;">
                Waktu Daftar: <strong>{{ $periodLabel }}</strong> | 
                OPD: <strong>{{ $filter_opd === 'all' ? 'Semua' : $filter_opd }}</strong> | 
                Kategori: <strong>{{ $filter_category === 'all' ? 'Semua' : (\App\Models\Domain::categories()[$filter_category] ?? $filter_category) }}</strong>
            </p>
        </div>

        {{-- Statistik Cards --}}
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
            {{-- Total Domain --}}
            <div style="background-color: var(--bg-white, #ffffff); border-radius: 0.75rem; padding: 1rem; border: 1px solid var(--border-color, #e5e7eb); box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);">
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <div style="padding: 0.5rem; border-radius: 0.5rem; background-color: rgba(59, 130, 246, 0.1);">
                        <x-heroicon-o-globe-alt style="width: 1.5rem; height: 1.5rem; color: #3b82f6;" />
                    </div>
                    <div>
                        <p style="font-size: 1.5rem; font-weight: bold; color: #2563eb;">{{ $totalDomains }}</p>
                        <p style="font-size: 0.75rem; color: #6b7280;">Total Domain (Sesuai Filter)</p>
                    </div>
                </div>
            </div>

            {{-- Total Aktif --}}
            <div style="background-color: var(--bg-white, #ffffff); border-radius: 0.75rem; padding: 1rem; border: 1px solid var(--border-color, #e5e7eb); box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);">
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <div style="padding: 0.5rem; border-radius: 0.5rem; background-color: rgba(16, 185, 129, 0.1);">
                        <x-heroicon-o-check-circle style="width: 1.5rem; height: 1.5rem; color: #10b981;" />
                    </div>
                    <div>
                        <p style="font-size: 1.5rem; font-weight: bold; color: #059669;">{{ $totalActive }}</p>
                        <p style="font-size: 0.75rem; color: #6b7280;">Status Aktif</p>
                    </div>
                </div>
            </div>

            {{-- Total Tidak Aktif --}}
            <div style="background-color: var(--bg-white, #ffffff); border-radius: 0.75rem; padding: 1rem; border: 1px solid var(--border-color, #e5e7eb); box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);">
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <div style="padding: 0.5rem; border-radius: 0.5rem; background-color: rgba(239, 68, 68, 0.1);">
                        <x-heroicon-o-x-circle style="width: 1.5rem; height: 1.5rem; color: #ef4444;" />
                    </div>
                    <div>
                        <p style="font-size: 1.5rem; font-weight: bold; color: #dc2626;">{{ $totalInactive }}</p>
                        <p style="font-size: 0.75rem; color: #6b7280;">Status Tidak Aktif</p>
                    </div>
                </div>
            </div>
            
            {{-- Rincian Kategori --}}
            <div style="background-color: var(--bg-white, #ffffff); border-radius: 0.75rem; padding: 1rem; border: 1px solid var(--border-color, #e5e7eb); box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);">
                <h4 style="font-size: 0.75rem; font-weight: 600; color: #6b7280; margin-bottom: 0.5rem;">Berdasarkan Kategori</h4>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem; font-size: 0.75rem;">
                    @foreach($categoryCounts as $label => $count)
                        @if($count > 0)
                            <div style="display: flex; justify-content: space-between;">
                                <span>{{ $label }}</span>
                                <span style="font-weight: 500;">{{ $count }}</span>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Tabel Laporan Utama --}}
        <div style="margin-bottom: 1.5rem;">
            @if($domains->count() > 0)
                <div style="overflow-x: auto; background-color: var(--bg-white, #ffffff); border-radius: 0.75rem; border: 1px solid var(--border-color, #e5e7eb); box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);">
                    <table style="width: 100%; border-collapse: collapse; font-size: 0.875rem; text-align: left;">
                        <thead style="background-color: rgba(0,0,0, 0.03); border-bottom: 1px solid var(--border-color, #e5e7eb);">
                            <tr>
                                <th style="padding: 0.75rem 1rem; font-weight: 600;">No</th>
                                <th style="padding: 0.75rem 1rem; font-weight: 600;">Domain</th>
                                <th style="padding: 0.75rem 1rem; font-weight: 600;">OPD</th>
                                <th style="padding: 0.75rem 1rem; font-weight: 600;">Aplikasi & Keterangan</th>
                                <th style="padding: 0.75rem 1rem; font-weight: 600;">Kategori</th>
                                <th style="padding: 0.75rem 1rem; font-weight: 600;">Status</th>
                                <th style="padding: 0.75rem 1rem; font-weight: 600;">Tgl Daftar</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($domains as $index => $domain)
                                <tr style="border-bottom: 1px solid var(--border-color, #e5e7eb);">
                                    <td style="padding: 0.75rem 1rem; vertical-align: top;">{{ $index + 1 }}</td>
                                    <td style="padding: 0.75rem 1rem; vertical-align: top;">
                                        <div style="font-weight: 500;">{{ $domain->name }}</div>
                                        <div style="font-size: 0.75rem; color: #6b7280; margin-top: 0.25rem;">
                                            {{ $domain->type }} : {{ $domain->content ?? '-' }}
                                        </div>
                                    </td>
                                    <td style="padding: 0.75rem 1rem; vertical-align: top;">{{ $domain->opd ?? '-' }}</td>
                                    <td style="padding: 0.75rem 1rem; vertical-align: top;">
                                        @if($domain->app_name)
                                            <div style="font-weight: 500;">{{ $domain->app_name }}</div>
                                        @endif
                                        @if($domain->app_description)
                                            <div style="font-size: 0.75rem; color: #6b7280; font-style: italic; margin-top: 0.125rem;">{{ $domain->app_description }}</div>
                                        @endif
                                        @if(!$domain->app_name && !$domain->app_description)
                                            <span style="color: #9ca3af;">-</span>
                                        @endif
                                    </td>
                                    <td style="padding: 0.75rem 1rem; vertical-align: top;">
                                        @if($domain->category)
                                            <span style="display: inline-flex; align-items: center; padding: 0.125rem 0.5rem; border-radius: 0.25rem; font-size: 0.75rem; font-weight: 500; background-color: rgba(107, 114, 128, 0.1);">
                                                {{ \App\Models\Domain::categories()[$domain->category] ?? '-' }}
                                            </span>
                                        @else
                                            <span style="color: #9ca3af;">-</span>
                                        @endif
                                    </td>
                                    <td style="padding: 0.75rem 1rem; vertical-align: top;">
                                        @if($domain->status === \App\Models\Domain::STATUS_ACTIVE)
                                            <span style="display: inline-flex; align-items: center; padding: 0.125rem 0.5rem; border-radius: 0.25rem; font-size: 0.75rem; font-weight: 500; background-color: rgba(16, 185, 129, 0.1); color: #10b981;">
                                                Aktif
                                            </span>
                                        @else
                                            <span style="display: inline-flex; align-items: center; padding: 0.125rem 0.5rem; border-radius: 0.25rem; font-size: 0.75rem; font-weight: 500; background-color: rgba(239, 68, 68, 0.1); color: #ef4444;">
                                                Tidak Aktif
                                            </span>
                                            <div style="font-size: 10px; color: #ef4444; margin-top: 0.25rem;">Sejak: {{ $domain->inactive_at ? $domain->inactive_at->format('d/m/Y') : '-' }}</div>
                                        @endif
                                    </td>
                                    <td style="padding: 0.75rem 1rem; vertical-align: top;">{{ $domain->created_at->format('d M Y') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div style="background-color: var(--bg-white, #ffffff); border-radius: 0.75rem; border: 1px solid var(--border-color, #e5e7eb); padding: 2rem; text-align: center;">
                    <x-heroicon-o-inbox style="width: 3rem; height: 3rem; margin: 0 auto 0.75rem auto; color: #9ca3af;" />
                    <p style="color: #6b7280;">Tidak ada domain yang cocok dengan kriteria filter tersebut.</p>
                </div>
            @endif
        </div>
    @else
        {{-- Empty State --}}
        <div style="margin-top: 1.5rem; background-color: var(--bg-white, #ffffff); border-radius: 0.75rem; border: 1px solid var(--border-color, #e5e7eb); padding: 3rem; text-align: center;">
            <x-heroicon-o-document-magnifying-glass style="width: 4rem; height: 4rem; margin: 0 auto 1rem auto; color: #9ca3af;" />
            <h3 style="font-size: 1.125rem; font-weight: 500; margin-bottom: 0.5rem;">Laporan Domain Kosong</h3>
            <p style="color: #6b7280;">Pilih kriteria filter di atas lalu klik "Terapkan Filter" untuk melihat data laporan domain.</p>
        </div>
    @endif
    
    <style>
        :root {
            --bg-white: #ffffff;
            --border-color: #e5e7eb;
        }
        @media (prefers-color-scheme: dark) {
            :root {
                --bg-white: rgba(255, 255, 255, 0.05);
                --border-color: rgba(255, 255, 255, 0.1);
            }
        }
    </style>
</x-filament-panels::page>
