<?php

namespace App\Filament\Resources\Standards\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class IndicatorsRelationManager extends RelationManager
{
    protected static string $relationship = 'indicators';

    protected static ?string $title = 'ຕົວຊີ້ວັດ';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Textarea::make('name')
                    ->label('ຊື່ຕົວຊີ້ວັດ')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('order')
                    ->label('ລຳດັບ')
                    ->required()
                    ->numeric()
                    ->default(fn (): int => $this->getOwnerRecord()->indicators()->max('order') + 1),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->reorderable('order')
            ->defaultSort('order')
            ->columns([
                TextColumn::make('name')
                    ->label('ຊື່ຕົວຊີ້ວັດ')
                    ->wrap()
                    ->searchable(),
                TextColumn::make('order')
                    ->label('ລຳດັບ')
                    ->numeric()
                    ->sortable(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('ເພີ່ມຕົວຊີ້ວັດ'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
