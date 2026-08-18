<?php

namespace App\Filament\Resources\Standards;

use App\Filament\Resources\Standards\Pages\ManageStandards;
use App\Models\Standard;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Validation\Rules\Unique;
use UnitEnum;

class StandardResource extends Resource
{
    protected static ?string $model = Standard::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentCheck;

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'ມາດຕະຖານ';

    protected static string|UnitEnum|null $navigationGroup = 'ມາດຕະຖານການປະກັນຄຸນນະພາບ';

    protected static ?string $modelLabel = 'ມາດຕະຖານ';

    protected static ?string $pluralModelLabel = 'ມາດຕະຖານ';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('framework_id')
                    ->label('ຊຸດມາດຕະຖານ')
                    ->relationship('framework', 'name')
                    ->live()
                    ->required(),
                TextInput::make('name')
                    ->label('ຊື່')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull()
                    ->unique(
                        modifyRuleUsing: fn (Unique $rule, Get $get) => $rule->where('framework_id', $get('framework_id')),
                    ),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('framework.name')
                    ->label('ຊຸດມາດຕະຖານ')
                    ->badge(),
                TextColumn::make('name')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make()->modalWidth('md'),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageStandards::route('/'),
        ];
    }
}
