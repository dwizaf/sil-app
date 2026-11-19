<?php

namespace App\Filament\Resources\OpdDrives;

use App\Filament\Resources\OpdDrives\Pages\CreateOpdDrive;
use App\Filament\Resources\OpdDrives\Pages\EditOpdDrive;
use App\Filament\Resources\OpdDrives\Pages\ListOpdDrives;
use App\Filament\Resources\OpdDrives\Schemas\OpdDriveForm;
use App\Filament\Resources\OpdDrives\Tables\OpdDrivesTable;
use App\Models\OpdDrive;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class OpdDriveResource extends Resource
{
    protected static ?string $model = OpdDrive::class;

    protected static ?string $navigationLabel = 'Folder GDrive OPD';

    protected static \UnitEnum|string|null $navigationGroup = 'Manajemen PSE';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedFolder;

    protected static ?string $recordTitleAttribute = 'opd_name';

    public static function form(Schema $schema): Schema
    {
        return OpdDriveForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return OpdDrivesTable::configure($table);
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
            'index' => ListOpdDrives::route('/'),
            'create' => CreateOpdDrive::route('/create'),
            'edit' => EditOpdDrive::route('/{record}/edit'),
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
