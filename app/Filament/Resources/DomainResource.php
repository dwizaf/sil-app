<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DomainResource\Pages;
use App\Filament\Resources\DomainResource\Schemas\DomainForm;
use App\Filament\Resources\DomainResource\Tables\DomainsTable;
use App\Models\Domain;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class DomainResource extends Resource
{
    protected static ?string $model = Domain::class;

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-globe-alt';

    protected static ?string $navigationLabel = 'Domain';

    protected static string | UnitEnum | null $navigationGroup = 'Manajemen Domain';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'Domain';

    protected static ?string $pluralModelLabel = 'Domain';

    public static function form(Schema $schema): Schema
    {
        return DomainForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DomainsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDomains::route('/'),
            'create' => Pages\CreateDomain::route('/create'),
            'edit' => Pages\EditDomain::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', Domain::STATUS_ACTIVE)->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }
}
