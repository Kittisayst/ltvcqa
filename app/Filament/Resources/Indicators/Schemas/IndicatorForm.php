<?php

namespace App\Filament\Resources\Indicators\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class IndicatorForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('standard_id')
                    ->label('ມາດຕະຖານ')
                    ->relationship('standard', 'name')
                    ->required(),
                TextInput::make('name')
                    ->label('ຊື່')
                    ->required(),
                TextInput::make('order')
                    ->label('ລຳດັບ')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }
}
