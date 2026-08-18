<?php

namespace App\Filament\Resources\QaFrameworks;

use App\Filament\Resources\QaFrameworks\Pages\CreateQaFramework;
use App\Filament\Resources\QaFrameworks\Pages\EditQaFramework;
use App\Filament\Resources\QaFrameworks\Pages\ListQaFrameworks;
use App\Filament\Resources\QaFrameworks\Pages\ManageStructure;
use App\Filament\Resources\QaFrameworks\Schemas\QaFrameworkForm;
use App\Filament\Resources\QaFrameworks\Tables\QaFrameworksTable;
use App\Models\QaFramework;
use BackedEnum;
use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class QaFrameworkResource extends Resource
{
    protected static ?string $model = QaFramework::class;

    protected static ?SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBookOpen;

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'ຊຸດມາດຕະຖານ';

    protected static string|UnitEnum|null $navigationGroup = 'ມາດຕະຖານການປະກັນຄຸນນະພາບ';

    protected static ?string $modelLabel = 'ຊຸດມາດຕະຖານ';

    protected static ?string $pluralModelLabel = 'ຊຸດມາດຕະຖານ';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return QaFrameworkForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return QaFrameworksTable::configure($table);
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
            'index' => ListQaFrameworks::route('/'),
            'create' => CreateQaFramework::route('/create'),
            'edit' => EditQaFramework::route('/{record}/edit'),
            'structure' => ManageStructure::route('/{record}/structure'),
        ];
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            EditQaFramework::class,
            ManageStructure::class,
        ]);
    }
}
