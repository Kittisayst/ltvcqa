<?php

namespace App\Filament\Resources\Standards\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class StandardsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('framework.name')
                    ->label('ຊຸດມາດຕະຖານ')
                    ->searchable(),
                TextColumn::make('name')
                    ->label('ຊື່ມາດຕະຖານ')
                    ->searchable(),
                TextColumn::make('order')
                    ->label('ລຳດັບ')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('ສ້າງເມື່ອ')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('ແກ້ໄຂເມື່ອ')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }
}
