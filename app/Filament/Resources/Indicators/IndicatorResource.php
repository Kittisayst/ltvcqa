<?php

namespace App\Filament\Resources\Indicators;

use App\Filament\Resources\Indicators\Pages\CreateIndicator;
use App\Filament\Resources\Indicators\Pages\EditIndicator;
use App\Filament\Resources\Indicators\Pages\ListIndicators;
use App\Filament\Resources\Indicators\RelationManagers\BasisMainsRelationManager;
use App\Filament\Resources\Indicators\Schemas\IndicatorForm;
use App\Filament\Resources\Indicators\Tables\IndicatorsTable;
use App\Models\Indicator;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class IndicatorResource extends Resource
{
    protected static ?string $model = Indicator::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBar;

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationLabel = 'ຕົວຊີ້ວັດ';

    protected static string|UnitEnum|null $navigationGroup = 'ມາດຕະຖານການປະກັນຄຸນນະພາບ';

    protected static ?string $modelLabel = 'ຕົວຊີ້ວັດ';

    protected static ?string $pluralModelLabel = 'ຕົວຊີ້ວັດ';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return IndicatorForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return IndicatorsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            BasisMainsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListIndicators::route('/'),
            'create' => CreateIndicator::route('/create'),
            'edit' => EditIndicator::route('/{record}/edit'),
        ];
    }
}
