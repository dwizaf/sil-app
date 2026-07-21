<?php

namespace App\Filament\Resources\OpdDrives\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class OpdDriveForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('opd_name')
                    ->label('Nama OPD')
                    ->required()
                    ->maxLength(255),
                TextInput::make('gdrive_link')
                    ->label('Tautan Folder Google Drive OPD')
                    ->url()
                    ->required()
                    ->maxLength(255),
            ]);
    }
}
