<x-filament-panels::page>
    <div style="display: flex; flex-direction: column; gap: 1.5rem;">

        {{-- 🔴 Section Domain --}}
        <div style="border: 2px solid rgba(239, 68, 68, 0.5); background: rgba(239, 68, 68, 0.05); border-radius: 0.75rem; padding: 1rem;">
            <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.75rem;">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" style="width: 1.25rem; height: 1.25rem; color: #f87171;">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16ZM4.332 8.027a6.012 6.012 0 0 1 7.84-3.659.993.993 0 0 1-.644-.07 6.007 6.007 0 0 0-4.168-.152c-.412.14-.812.314-1.192.52L4.332 8.027Zm1.244 4.747A5.98 5.98 0 0 1 4 10c0-.974.232-1.894.644-2.707a.993.993 0 0 1-.069.644 6.007 6.007 0 0 0-.152 4.168c.14.412.314.812.52 1.192l.633-1.523Zm4.397 3.194a5.98 5.98 0 0 1-2.746-1.244.993.993 0 0 1 .644.069 6.007 6.007 0 0 0 4.168.152c.412-.14.812-.314 1.192-.52l-1.523-.633a6.012 6.012 0 0 1-1.735 2.176Zm3.695-3.695c.816-.813 1.413-1.813 1.735-2.907a.993.993 0 0 1-.069.644 6.007 6.007 0 0 0-.152 4.168c.14.412.314.812.52 1.192l-.633-1.523a6.012 6.012 0 0 1-1.401-1.574Z" clip-rule="evenodd" />
                </svg>
                <h3 style="font-size: 1.125rem; font-weight: 600; color: #f87171; margin: 0;">Domain & Aplikasi</h3>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div>
                    @livewire(\App\Filament\Widgets\DomainStatsWidget::class)
                </div>
                <div>
                    @livewire(\App\Filament\Widgets\DomainMonthlyChart::class)
                </div>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
            {{-- 🟡 Section PSE --}}
            <div style="border: 2px solid rgba(234, 179, 8, 0.5); background: rgba(234, 179, 8, 0.05); border-radius: 0.75rem; padding: 1rem;">
                <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.75rem;">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" style="width: 1.25rem; height: 1.25rem; color: #facc15;">
                        <path fill-rule="evenodd" d="M9.661 2.237a.531.531 0 0 1 .678 0 11.947 11.947 0 0 0 7.078 2.749.5.5 0 0 1 .479.425c.069.52.104 1.05.104 1.59 0 5.162-3.26 9.563-7.834 11.256a.48.48 0 0 1-.332 0C5.26 16.564 2 12.163 2 7c0-.538.035-1.069.104-1.589a.5.5 0 0 1 .48-.425 11.947 11.947 0 0 0 7.077-2.75Zm4.196 5.954a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z" clip-rule="evenodd" />
                    </svg>
                    <h3 style="font-size: 1.125rem; font-weight: 600; color: #facc15; margin: 0;">PSE (Penyelenggara Sistem Elektronik)</h3>
                </div>
                @livewire(\App\Filament\Widgets\PseStatsWidget::class)
            </div>

            {{-- 🟢 Section Email --}}
            <div style="border: 2px solid rgba(34, 197, 94, 0.5); background: rgba(34, 197, 94, 0.05); border-radius: 0.75rem; padding: 1rem;">
                <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.75rem;">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" style="width: 1.25rem; height: 1.25rem; color: #4ade80;">
                        <path d="M3 4a2 2 0 0 0-2 2v1.161l8.441 4.221a1.25 1.25 0 0 0 1.118 0L19 7.162V6a2 2 0 0 0-2-2H3Z" />
                        <path d="m19 8.839-7.77 3.885a2.75 2.75 0 0 1-2.46 0L1 8.839V14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V8.839Z" />
                    </svg>
                    <h3 style="font-size: 1.125rem; font-weight: 600; color: #4ade80; margin: 0;">Email Instansi</h3>
                </div>
                @livewire(\App\Filament\Widgets\EmailStatsWidget::class)
            </div>
        </div>

    </div>
</x-filament-panels::page>
