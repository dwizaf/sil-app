<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Models\User;
use Filament\Forms;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Informasi Pengguna')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Lengkap')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Masukkan nama pengguna'),

                        Forms\Components\TextInput::make('email')
                            ->label('Alamat Email')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->placeholder('contoh: user@example.com'),

                        Forms\Components\Select::make('role')
                            ->label('Role / Peran')
                            ->options(User::roles())
                            ->default(User::ROLE_ADMIN)
                            ->required(),

                        Forms\Components\Select::make('status')
                            ->label('Status Akun')
                            ->options(User::statuses())
                            ->default(User::STATUS_ACTIVE)
                            ->required(),
                    ])->columns(2),

                Section::make('Keamanan & Password')
                    ->schema([
                        Forms\Components\TextInput::make('password')
                            ->label('Password')
                            ->password()
                            ->revealable()
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->dehydrateStateUsing(fn (?string $state): ?string => filled($state) ? Hash::make($state) : null)
                            ->dehydrated(fn (?string $state): bool => filled($state))
                            ->maxLength(255)
                            ->helperText('Kosongkan jika tidak ingin mengubah password saat merubah data pengguna.'),
                    ])->columns(1),
            ]);
    }
}
