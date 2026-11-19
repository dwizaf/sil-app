<?php

namespace App\Filament\Resources\DomainResource\Tables;

use App\Models\Domain;
use App\Services\CloudflareImportService;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class DomainsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Domain')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('opd')
                    ->label('OPD')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('app_name')
                    ->label('Aplikasi')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('app_description')
                    ->label('Keterangan')
                    ->searchable()
                    ->limit(50)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= $column->getCharacterLimit()) {
                            return null;
                        }
                        return $state;
                    })
                    ->toggleable(),

                Tables\Columns\TextColumn::make('type')
                    ->label('Tipe')
                    ->badge()
                    ->color('gray')
                    ->sortable(),

                Tables\Columns\TextColumn::make('content')
                    ->label('Content')
                    ->searchable()
                    ->limit(30),

                Tables\Columns\TextColumn::make('category')
                    ->label('Kategori')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'pemerintah' => 'primary',
                        'docker' => 'warning',
                        'staging' => 'info',
                        'production' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(?string $state): string => Domain::categories()[$state] ?? '-')
                    ->sortable(),

                Tables\Columns\IconColumn::make('proxied')
                    ->label('Proxy')
                    ->boolean()
                    ->trueIcon('heroicon-o-shield-check')
                    ->falseIcon('heroicon-o-shield-exclamation')
                    ->trueColor('success')
                    ->falseColor('gray'),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('inactive_at')
                    ->label('Tidak Aktif Sejak')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->placeholder('-')
                    ->color('danger')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('imported_at')
                    ->label('Terakhir Diimpor')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Didaftarkan')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('name', 'asc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options(Domain::statuses()),

                Tables\Filters\SelectFilter::make('category')
                    ->label('Kategori')
                    ->options(Domain::categories()),

                Tables\Filters\SelectFilter::make('type')
                    ->label('Tipe DNS')
                    ->options(Domain::dnsTypes()),

                Tables\Filters\TernaryFilter::make('proxied')
                    ->label('Cloudflare Proxy'),

                Tables\Filters\Filter::make('inactive_at')
                    ->label('Tanggal Nonaktif')
                    ->form([
                        Forms\Components\DatePicker::make('inactive_from')
                            ->label('Dari'),
                        Forms\Components\DatePicker::make('inactive_until')
                            ->label('Sampai'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['inactive_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('inactive_at', '>=', $date),
                            )
                            ->when(
                                $data['inactive_until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('inactive_at', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                \Filament\Actions\ViewAction::make(),
                \Filament\Actions\EditAction::make()
                    ->visible(fn () => auth()->user()?->canModifyData()),
                \Filament\Actions\DeleteAction::make()
                    ->visible(fn () => auth()->user()?->canModifyData()),
            ])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()?->canModifyData()),
                ]),
            ])
            ->headerActions([
                \Filament\Actions\Action::make('downloadTemplate')
                    ->label('Download Template')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('success')
                    ->action(fn () => \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\CloudflareTemplateExport, 'cloudflare_template.xlsx')),

                \Filament\Actions\Action::make('importCloudflare')
                    ->label('Import Cloudflare Excel')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->color('primary')
                    ->visible(fn () => auth()->user()?->canModifyData())
                    ->form([
                        Forms\Components\FileUpload::make('excel_file')
                            ->label('File Excel Cloudflare')
                            ->acceptedFileTypes([
                                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                                'application/vnd.ms-excel',
                                'text/csv',
                            ])
                            ->required()
                            ->helperText('Upload file .xlsx, .xls, atau .csv yang diekspor dari Cloudflare DNS.'),
                    ])
                    ->action(function (array $data) {
                        $filePath = storage_path('app/' . $data['excel_file']);

                        // Ambil nama file asli
                        $originalFilename = $data['excel_file'];

                        $service = new CloudflareImportService();
                        $result = $service->import($filePath, $originalFilename);

                        if (!empty($result['errors'])) {
                            Notification::make()
                                ->title('Import Gagal')
                                ->body(implode("\n", $result['errors']))
                                ->danger()
                                ->send();
                            return;
                        }

                        $body = "Total record: {$result['total_records']}\n"
                            . "Domain baru: {$result['new_domains']}\n"
                            . "Domain diperbarui: {$result['updated_domains']}\n"
                            . "Domain dinonaktifkan: {$result['deactivated_domains']}\n"
                            . "Domain diaktifkan kembali: {$result['reactivated_domains']}";

                        Notification::make()
                            ->title('Import Cloudflare Berhasil!')
                            ->body($body)
                            ->success()
                            ->duration(10000)
                            ->send();
                    })
                    ->modalHeading('Import Data DNS dari Cloudflare')
                    ->modalDescription('Upload file Excel yang diekspor dari dashboard Cloudflare DNS. Sistem akan secara otomatis mendeteksi domain baru, domain yang hilang (ditandai inactive), dan perubahan data.')
                    ->modalSubmitActionLabel('Mulai Import')
                    ->modalWidth('lg'),
            ]);
    }
}
