<?php

namespace App\Filament\Resources\Pses\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class PseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('app_name')
                    ->required(),
                TextInput::make('url')
                    ->url(),
                TextInput::make('opd'),
                TextInput::make('tdpse_number'),
                DatePicker::make('registration_date'),
                TextInput::make('sector')
                    ->required()
                    ->default('publik'),
                TextInput::make('status')
                    ->required()
                    ->default('belum_terdaftar'),
                Textarea::make('notes')
                    ->columnSpanFull(),
            ]);
    }
}
