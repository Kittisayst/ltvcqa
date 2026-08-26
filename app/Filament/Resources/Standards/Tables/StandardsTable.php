<?php

namespace App\Filament\Resources\Standards\Tables;

use App\Filament\Concerns\HasQaHierarchyFilters;
use App\Models\AcademicYear;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class StandardsTable
{
    use HasQaHierarchyFilters;

    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query): Builder => $query
                ->addSelect(['latest_academic_year_at' => AcademicYear::query()
                    ->select('created_at')
                    ->whereColumn('academic_years.framework_id', 'standards.framework_id')
                    ->latest('created_at')
                    ->limit(1),
                ])
                ->orderByDesc('latest_academic_year_at')
                ->orderBy('standards.order'))
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
                self::frameworkFilter(),
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }
}
