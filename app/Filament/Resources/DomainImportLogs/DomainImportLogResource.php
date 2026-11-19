<?php

namespace App\Filament\Resources\DomainImportLogs;

use App\Filament\Resources\DomainImportLogs\Pages\ManageDomainImportLogs;
use App\Models\DomainImportLog;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class DomainImportLogResource extends Resource
{
    protected static ?string $model = DomainImportLog::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $navigationLabel = 'Log Sinkronisasi';
    protected static string|\UnitEnum|null $navigationGroup = 'Manajemen Domain';
    protected static ?string $modelLabel = 'Log Import';
    protected static ?string $pluralModelLabel = 'Log Import Data';
    
    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Import')
                    ->schema([
                        TextEntry::make('created_at')->label('Waktu Import')->dateTime('d M Y, H:i'),
                        TextEntry::make('importer.name')->label('Diimpor Oleh'),
                        TextEntry::make('filename')->label('Nama File Excel'),
                        TextEntry::make('total_records')->label('Total Baris Dibaca'),
                    ])->columns(2),

                Section::make('Domain Baru Ditambahkan')
                    ->schema([
                        TextEntry::make('summary.new')
                            ->label('')
                            ->formatStateUsing(fn ($record) => is_array($record->summary['new'] ?? null) && count($record->summary['new']) > 0 ? implode(', ', $record->summary['new']) : 'Tidak ada data')
                            ->color('success'),
                    ]),

                Section::make('Domain Diperbarui')
                    ->schema([
                        TextEntry::make('summary.updated')
                            ->label('')
                            ->formatStateUsing(fn ($record) => is_array($record->summary['updated'] ?? null) && count($record->summary['updated']) > 0 ? implode(', ', $record->summary['updated']) : 'Tidak ada data')
                            ->color('info'),
                    ]),

                Section::make('Domain Dinonaktifkan')
                    ->schema([
                        TextEntry::make('summary.deactivated')
                            ->label('')
                            ->formatStateUsing(fn ($record) => is_array($record->summary['deactivated'] ?? null) && count($record->summary['deactivated']) > 0 ? implode(', ', $record->summary['deactivated']) : 'Tidak ada data')
                            ->color('danger'),
                    ]),

                Section::make('Domain Diaktifkan Kembali')
                    ->schema([
                        TextEntry::make('summary.reactivated')
                            ->label('')
                            ->formatStateUsing(fn ($record) => is_array($record->summary['reactivated'] ?? null) && count($record->summary['reactivated']) > 0 ? implode(', ', $record->summary['reactivated']) : 'Tidak ada data')
                            ->color('warning'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('created_at')
                    ->label('Waktu Import')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('importer.name')
                    ->label('Diimpor Oleh')
                    ->searchable(),
                \Filament\Tables\Columns\TextColumn::make('filename')
                    ->label('Nama File')
                    ->searchable(),
                \Filament\Tables\Columns\TextColumn::make('total_records')
                    ->label('Total Baris')
                    ->numeric()
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('new_domains')
                    ->label('Baru')
                    ->numeric()
                    ->color('success')
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('updated_domains')
                    ->label('Diperbarui')
                    ->numeric()
                    ->color('info')
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('deactivated_domains')
                    ->label('Dinonaktifkan')
                    ->numeric()
                    ->color('danger')
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('reactivated_domains')
                    ->label('Diaktifkan Kembali')
                    ->numeric()
                    ->color('warning')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                \Filament\Actions\ViewAction::make(),
                \Filament\Actions\Action::make('download_pdf')
                    ->label('Download PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('danger')
                    ->url(fn (DomainImportLog $record) => route('logs.pdf', $record->id))
                    ->openUrlInNewTab(),
            ])
            ->toolbarActions([
                //
            ]);
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->canModifyData() ?? false;
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return false;
    }

    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return auth()->user()?->canModifyData() ?? false;
    }

    public static function canDeleteAny(): bool
    {
        return auth()->user()?->canModifyData() ?? false;
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageDomainImportLogs::route('/'),
        ];
    }
}
