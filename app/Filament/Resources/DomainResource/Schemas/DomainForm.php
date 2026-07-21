<?php

namespace App\Filament\Resources\DomainResource\Schemas;

use App\Models\Domain;
use Filament\Forms;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class DomainForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Informasi Domain')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Domain')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->placeholder('contoh: app.example.go.id'),

                        Forms\Components\TextInput::make('opd')
                            ->label('OPD (Perangkat Daerah)')
                            ->maxLength(255)
                            ->placeholder('contoh: Badan Kepegawaian Daerah'),

                        Forms\Components\TextInput::make('app_name')
                            ->label('Nama Aplikasi')
                            ->maxLength(255)
                            ->placeholder('contoh: Sistem Informasi Pegawai'),

                        Forms\Components\Select::make('type')
                            ->label('Tipe DNS')
                            ->options(Domain::dnsTypes())
                            ->default('A')
                            ->required(),

                        Forms\Components\TextInput::make('content')
                            ->label('Content / IP Address')
                            ->maxLength(255)
                            ->placeholder('contoh: 103.xxx.xxx.xxx'),

                        Forms\Components\TextInput::make('ttl')
                            ->label('TTL')
                            ->numeric()
                            ->nullable(),

                        Forms\Components\Toggle::make('proxied')
                            ->label('Cloudflare Proxy')
                            ->default(false),
                    ])->columns(2),

                Section::make('Kategori & Status')
                    ->schema([
                        Forms\Components\Select::make('category')
                            ->label('Kategori')
                            ->options(Domain::categories())
                            ->nullable()
                            ->placeholder('Pilih kategori atau biarkan untuk auto-detect'),

                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options(Domain::statuses())
                            ->default(Domain::STATUS_ACTIVE)
                            ->required(),

                        Forms\Components\DateTimePicker::make('inactive_at')
                            ->label('Tidak Aktif Sejak')
                            ->nullable()
                            ->disabled()
                            ->helperText('Terisi otomatis saat domain terdeteksi tidak aktif oleh import Cloudflare.'),

                        Forms\Components\DateTimePicker::make('imported_at')
                            ->label('Terakhir Diimpor')
                            ->nullable()
                            ->disabled(),
                    ])->columns(2),

                Section::make('Catatan')
                    ->schema([
                        Forms\Components\Textarea::make('app_description')
                            ->label('Deskripsi / Keterangan Aplikasi')
                            ->nullable()
                            ->rows(3),

                        Forms\Components\Textarea::make('notes')
                            ->label('Catatan Tambahan')
                            ->nullable()
                            ->rows(2),
                    ]),
            ]);
    }
}
