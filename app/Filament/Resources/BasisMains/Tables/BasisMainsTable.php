<?php

namespace App\Filament\Resources\BasisMains\Tables;

use App\Models\Standard;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class BasisMainsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->reorderable('order')
            ->modifyQueryUsing(fn (Builder $query) => $query->orderBy('indicator_id')->orderBy('order'))
            ->groups([
                Group::make('indicator.name')
                    ->label('ຕົວຊີ້ວັດ')
                    ->orderQueryUsing(fn (Builder $query, string $direction) => $query->orderBy('indicator_id', $direction)),
            ])
            ->defaultGroup('indicator.name')
            ->columns([
                TextColumn::make('indicator.name')
                    ->label('ຕົວຊີ້ວັດ')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('title')
                    ->label('ຫຼັກຖານ')
                    ->wrap()
                    ->searchable(),
                TextColumn::make('order')
                    ->label('ລຳດັບ')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('standard_id')
                    ->label('ມາດຕະຖານ')
                    ->options(fn () => Standard::orderBy('order')->pluck('name', 'id'))
                    ->query(fn (Builder $query, array $data) => $query->when(
                        $data['value'],
                        fn (Builder $query, $value) => $query->whereHas(
                            'indicator',
                            fn (Builder $query) => $query->where('standard_id', $value)
                        )
                    )),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
