<?php

namespace App\Filament\Resources\Users\Tables;

use App\Models\User;
use Filament\Actions;
use Filament\Tables;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->icon('heroicon-o-envelope'),

                Tables\Columns\TextColumn::make('role')
                    ->label('Role')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        User::ROLE_ADMIN => 'danger',
                        User::ROLE_OPERATOR => 'info',
                        User::ROLE_USER => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (?string $state): string => User::roles()[$state] ?? ($state ?: '-'))
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        User::STATUS_ACTIVE => 'success',
                        User::STATUS_INACTIVE => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (?string $state): string => User::statuses()[$state] ?? ($state ?: '-'))
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Terdaftar Pada')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('name', 'asc')
            ->filters([
                Tables\Filters\SelectFilter::make('role')
                    ->label('Filter Role')
                    ->options(User::roles()),

                Tables\Filters\SelectFilter::make('status')
                    ->label('Filter Status')
                    ->options(User::statuses()),
            ])
            ->actions([
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
