<?php

namespace App\Filament\Resources\Indicators\Tables;

use App\Filament\Concerns\HasQaHierarchyFilters;
use App\Models\AcademicYear;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class IndicatorsTable
{
    use HasQaHierarchyFilters;

    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query): Builder => $query
                ->join('standards', 'standards.id', '=', 'indicators.standard_id')
                ->addSelect('indicators.*')
                ->addSelect(['latest_academic_year_at' => AcademicYear::query()
                    ->select('created_at')
                    ->whereColumn('academic_years.framework_id', 'standards.framework_id')
                    ->latest('created_at')
                    ->limit(1),
                ])
                ->orderByDesc('latest_academic_year_at')
                ->orderBy('standards.order')
                ->orderBy('indicators.order'))
            ->columns([
                TextColumn::make('standard.framework.name')
                    ->label('ຊຸດມາດຕະຖານ')
                    ->badge()
                    ->searchable(),
                TextColumn::make('standard.name')
                    ->label('ມາດຕະຖານ')
                    ->searchable(),
                TextColumn::make('order')
                    ->label('ລຳດັບ')
                    ->numeric()
                    ->sortable(['indicators.order']),
                TextColumn::make('name')
                    ->label('ຕົວຊີ້ວັດ')
                    ->limit(120)
                    ->wrap()
                    ->searchable(),
                TextColumn::make('basis_mains_count')
                    ->label('ຈຳນວນຫຼັກຖານ')
                    ->counts('basisMains')
                    ->badge()
                    ->color('gray'),
                TextColumn::make('created_at')
                    ->label('ສ້າງເມື່ອ')
                    ->dateTime()
                    ->sortable(['indicators.created_at'])
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('ແກ້ໄຂເມື່ອ')
                    ->dateTime()
                    ->sortable(['indicators.updated_at'])
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->deferFilters(false)
            ->filters([
                self::frameworkFilter('standard', resetsFilters: ['standard_id']),
                self::standardFilter(scopedByFrameworkFilter: 'framework_id'),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
