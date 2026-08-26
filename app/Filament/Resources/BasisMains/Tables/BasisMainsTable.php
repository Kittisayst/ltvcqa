<?php

namespace App\Filament\Resources\BasisMains\Tables;

use App\Filament\Concerns\HasQaHierarchyFilters;
use App\Models\BasisMain;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class BasisMainsTable
{
    use HasQaHierarchyFilters;

    public static function configure(Table $table): Table
    {
        return $table
            ->reorderable('order')
            ->modifyQueryUsing(fn (Builder $query) => $query->orderBy('indicator_id')->orderBy('order'))
            ->columns([
                TextColumn::make('indicator.standard.name')
                    ->label('ມາດຕະຖານ')
                    ->formatStateUsing(fn (BasisMain $record): string => "ມາດຕະຖານ {$record->indicator->standard->ordered_name}")
                    ->searchable(),
                TextColumn::make('indicator.name')
                    ->label('ຕົວຊີ້ວັດ')
                    ->formatStateUsing(fn (BasisMain $record): string => "ຕົວຊີ້ວັດ {$record->indicator->ordered_name}")
                    ->limit(50)
                    ->searchable(),
                TextColumn::make('title')
                    ->label('ຫຼັກຖານ')
                    ->wrap()
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
            ->deferFilters(false)
            ->filters([
                self::frameworkFilter('indicator.standard', resetsFilters: ['standard_id', 'indicator_id']),
                self::standardFilter('indicator', scopedByFrameworkFilter: 'framework_id', resetsFilters: ['indicator_id']),
                self::indicatorFilter(scopedByStandardFilter: 'standard_id'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
