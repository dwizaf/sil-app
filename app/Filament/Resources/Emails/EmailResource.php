<?php

namespace App\Filament\Resources\Emails;

use App\Filament\Resources\Emails\EmailResource\Pages;
use App\Models\Email;
use BackedEnum;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class EmailResource extends Resource
{
    protected static ?string $model = Email::class;

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-envelope';
    
    protected static ?string $navigationLabel = 'Akun Email';
    protected static string | UnitEnum | null $navigationGroup = 'Manajemen Email';
    protected static ?string $modelLabel = 'Email';
    protected static ?string $pluralModelLabel = 'Email';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                \Filament\Schemas\Components\Section::make('Informasi Email')->schema([
                    Forms\Components\TextInput::make('email_address')
                        ->label('Alamat Email')
                        ->email()
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(255),
                    Forms\Components\TextInput::make('opd')
                        ->label('OPD')
                        ->maxLength(255),
                    Forms\Components\TextInput::make('pic_name')
                        ->label('Nama PIC')
                        ->maxLength(255),
                    Forms\Components\TextInput::make('pic_phone')
                        ->label('Kontak PIC (HP/WA)')
                        ->maxLength(255),
                    Forms\Components\TextInput::make('quota_mb')
                        ->label('Kapasitas/Kuota (MB)')
                        ->numeric()
                        ->nullable(),
                    Forms\Components\Select::make('status')
                        ->label('Status')
                        ->options([
                            'active' => 'Aktif',
                            'inactive' => 'Tidak Aktif',
                            'suspend' => 'Ditangguhkan',
                        ])
                        ->default('active')
                        ->required(),
                    Forms\Components\Textarea::make('notes')
                        ->label('Catatan/Keterangan')
                        ->columnSpanFull(),
                ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('email_address')
                    ->label('Alamat Email')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('opd')
                    ->label('OPD')
                    ->searchable()
                    ->sortable()
                    ->wrap(),
                Tables\Columns\TextColumn::make('pic_name')
                    ->label('PIC')
                    ->searchable()
                    ->description(fn (Email $record): string => $record->pic_phone ?? ''),
                Tables\Columns\TextColumn::make('quota_mb')
                    ->label('Kuota')
                    ->numeric()
                    ->suffix(' MB')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'danger',
                        'suspend' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'active' => 'Aktif',
                        'inactive' => 'Tidak Aktif',
                        'suspend' => 'Ditangguhkan',
                        default => $state,
                    })
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'active' => 'Aktif',
                        'inactive' => 'Tidak Aktif',
                        'suspend' => 'Ditangguhkan',
                    ]),
                Tables\Filters\SelectFilter::make('opd')
                    ->options(fn () => Email::query()->whereNotNull('opd')->where('opd', '!=', '')->distinct()->pluck('opd', 'opd')->toArray())
                    ->searchable(),
            ])
            ->actions([
                \Filament\Actions\EditAction::make(),
                \Filament\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\Emails\Pages\ListEmails::route('/'),
            'create' => \App\Filament\Resources\Emails\Pages\CreateEmail::route('/create'),
            'edit' => \App\Filament\Resources\Emails\Pages\EditEmail::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->canModifyData() ?? false;
    }

    public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return auth()->user()?->canModifyData() ?? false;
    }

    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return auth()->user()?->canModifyData() ?? false;
    }

    public static function canDeleteAny(): bool
    {
        return auth()->user()?->canModifyData() ?? false;
    }
}
