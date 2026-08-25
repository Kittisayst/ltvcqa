<?php

namespace App\Filament\Resources\Standards\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Schema;

class StandardForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('framework_id')
                    ->label('ຊຸດມາດຕະຖານ')
                    ->relationship('framework', 'name')
                    ->columnSpanFull()
                    ->required(),
                Group::make()
                    ->schema([
                        TextInput::make('name')
                            ->label('ຊື່ມາດຕະຖານ')
                            ->required()
                            ->columnSpan(3),
                        TextInput::make('order')
                            ->label('ລຳດັບ')
                            ->required()
                            ->numeric()
                            ->default(0),
                    ])
                    ->columns(4)
                    ->columnSpanFull(),
            ]);
    }
}
