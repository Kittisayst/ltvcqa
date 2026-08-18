<?php

namespace App\Filament\Resources\Indicators\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class BasisMainsRelationManager extends RelationManager
{
    protected static string $relationship = 'basisMains';

    protected static ?string $title = 'ຫຼັກຖານ';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label('ຫຼັກຖານ')
                    ->required()
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->reorderable('order')
            ->defaultSort('order')
            ->columns([
                TextColumn::make('title')
                    ->label('ຫຼັກຖານ')
                    ->wrap()
                    ->searchable(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('ເພີ່ມຫຼັກຖານ')
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['order'] = $this->getOwnerRecord()->basisMains()->max('order') + 1;

                        return $data;
                    }),
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
