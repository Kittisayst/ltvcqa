<?php

namespace App\Filament\Resources\BasisMains;

use App\Filament\Resources\BasisMains\Pages\CreateBasisMain;
use App\Filament\Resources\BasisMains\Pages\EditBasisMain;
use App\Filament\Resources\BasisMains\Pages\ListBasisMains;
use App\Filament\Resources\BasisMains\Schemas\BasisMainForm;
use App\Filament\Resources\BasisMains\Tables\BasisMainsTable;
use App\Models\BasisMain;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class BasisMainResource extends Resource
{
    protected static ?string $model = BasisMain::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?int $navigationSort = 4;

    protected static ?string $navigationLabel = 'ຫຼັກຖານ';

    protected static string|UnitEnum|null $navigationGroup = 'ມາດຕະຖານການປະກັນຄຸນນະພາບ';

    protected static ?string $modelLabel = 'ຫຼັກຖານ';

    protected static ?string $pluralModelLabel = 'ຫຼັກຖານ';

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return BasisMainForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BasisMainsTable::configure($table);
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
            'index' => ListBasisMains::route('/'),
            'create' => CreateBasisMain::route('/create'),
            'edit' => EditBasisMain::route('/{record}/edit'),
        ];
    }
}
