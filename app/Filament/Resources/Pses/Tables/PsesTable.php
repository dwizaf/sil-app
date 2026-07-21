<?php

namespace App\Filament\Resources\Pses\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PsesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('app_name')
                    ->searchable(),
                TextColumn::make('url')
                    ->searchable(),
                TextColumn::make('opd')
                    ->searchable(),
                TextColumn::make('tdpse_number')
                    ->searchable(),
                TextColumn::make('registration_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('sector')
                    ->searchable(),
                TextColumn::make('status')
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
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
