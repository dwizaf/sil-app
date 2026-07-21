<?php

namespace App\Filament\Resources\Emails\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class EmailForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('email_address')
                    ->email()
                    ->required(),
                TextInput::make('opd'),
                TextInput::make('pic_name'),
                TextInput::make('pic_phone')
                    ->tel(),
                TextInput::make('quota_mb')
                    ->numeric(),
                TextInput::make('status')
                    ->required()
                    ->default('active'),
                Textarea::make('notes')
                    ->columnSpanFull(),
            ]);
    }
}
