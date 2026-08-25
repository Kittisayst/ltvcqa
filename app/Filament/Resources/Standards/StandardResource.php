<?php

namespace App\Filament\Resources\Standards;

use App\Filament\Resources\Standards\Pages\CreateStandard;
use App\Filament\Resources\Standards\Pages\EditStandard;
use App\Filament\Resources\Standards\Pages\ListStandards;
use App\Filament\Resources\Standards\RelationManagers\IndicatorsRelationManager;
use App\Filament\Resources\Standards\Schemas\StandardForm;
use App\Filament\Resources\Standards\Tables\StandardsTable;
use App\Models\Standard;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class StandardResource extends Resource
{
    protected static ?string $model = Standard::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'ມາດຕະຖານ';

    protected static string|UnitEnum|null $navigationGroup = 'ມາດຕະຖານການປະກັນຄຸນນະພາບ';

    protected static ?string $modelLabel = 'ມາດຕະຖານ';

    protected static ?string $pluralModelLabel = 'ມາດຕະຖານ';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return StandardForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StandardsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            IndicatorsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStandards::route('/'),
            'create' => CreateStandard::route('/create'),
            'edit' => EditStandard::route('/{record}/edit'),
        ];
    }
}
