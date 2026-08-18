<?php

namespace App\Filament\Resources\BasisMains\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class BasisMainForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('indicator_id')
                    ->label('ຕົວຊີ້ວັດ')
                    ->relationship('indicator', 'name')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('title')
                    ->label('ຫຼັກຖານ')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('order')
                    ->label('ລຳດັບ')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }
}
