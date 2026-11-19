<?php

namespace App\Filament\Resources\OpdDrives\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class OpdDrivesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('opd_name')
                    ->label('Nama OPD')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('gdrive_link')
                    ->label('Tautan Google Drive')
                    ->limit(50)
                    ->color('primary')
                    ->url(fn (\App\Models\OpdDrive $record) => $record->gdrive_link)
                    ->openUrlInNewTab()
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                \Filament\Actions\Action::make('copy_link')
                    ->label('Copy Link')
                    ->icon('heroicon-o-clipboard-document-list')
                    ->color('success')
                    ->extraAttributes(fn (\App\Models\OpdDrive $record) => [
                        'x-on:click' => 'window.navigator.clipboard.writeText("'.$record->gdrive_link.'"); $tooltip("Berhasil disalin!");',
                    ]),
                \Filament\Actions\Action::make('buka_gdrive')
                    ->label('Buka')
                    ->icon('heroicon-o-folder')
                    ->color('info')
                    ->url(fn (\App\Models\OpdDrive $record): ?string => $record->gdrive_link)
                    ->openUrlInNewTab(),
                EditAction::make()
                    ->visible(fn () => auth()->user()?->canModifyData()),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()?->canModifyData()),
                ]),
            ]);
    }
}
