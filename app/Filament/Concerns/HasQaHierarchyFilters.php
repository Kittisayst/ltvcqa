<?php

namespace App\Filament\Concerns;

use App\Models\Indicator;
use App\Models\QaFramework;
use App\Models\Standard;
use Filament\Forms\Components\Select;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;

/**
 * Reusable, cascading filters for the QaFramework -> Standard -> Indicator ->
 * BasisMain hierarchy, shared across the Standards, Indicators, and
 * BasisMains admin tables. Pass the dot-relation path to the model that
 * actually owns the filtered-on column when the current table's model
 * doesn't have that column directly (e.g. Indicator has no framework_id,
 * but reaches it via `standard`).
 *
 * Picking a parent (framework/standard) narrows the child filter's own
 * option list to that parent, and clears any already-selected child value
 * that no longer belongs to it. Tables using these filters should call
 * ->deferFilters(false) so the narrowing applies immediately.
 */
trait HasQaHierarchyFilters
{
    /**
     * @param  array<int, string>  $resetsFilters  child filter names to clear when this one changes
     */
    protected static function frameworkFilter(?string $throughRelation = null, array $resetsFilters = []): SelectFilter
    {
        $filter = SelectFilter::make('framework_id')
            ->label('ຊຸດມາດຕະຖານ')
            ->options(fn (): array => QaFramework::query()->orderBy('name')->pluck('name', 'id')->all())
            ->default(fn (): ?int => QaFramework::query()->where('status', 'published')->value('id'));

        static::liveAndResets($filter, $resetsFilters);

        return static::scopeThroughRelation($filter, $throughRelation, 'framework_id');
    }

    /**
     * @param  array<int, string>  $resetsFilters  child filter names to clear when this one changes
     */
    protected static function standardFilter(
        ?string $throughRelation = null,
        ?string $scopedByFrameworkFilter = null,
        array $resetsFilters = [],
    ): SelectFilter {
        $filter = SelectFilter::make('standard_id')
            ->label('ມາດຕະຖານ')
            ->options(function ($livewire) use ($scopedByFrameworkFilter): array {
                $query = Standard::query()->orderBy('order');

                $frameworkId = $scopedByFrameworkFilter
                    ? ($livewire->tableFilters[$scopedByFrameworkFilter]['value'] ?? null)
                    : null;

                if ($frameworkId) {
                    $query->where('framework_id', $frameworkId);
                }

                return $query->pluck('name', 'id')->all();
            });

        static::liveAndResets($filter, $resetsFilters);

        return static::scopeThroughRelation($filter, $throughRelation, 'standard_id');
    }

    protected static function indicatorFilter(?string $throughRelation = null, ?string $scopedByStandardFilter = null): SelectFilter
    {
        $filter = SelectFilter::make('indicator_id')
            ->label('ຕົວຊີ້ວັດ')
            ->options(function ($livewire) use ($scopedByStandardFilter): array {
                $query = Indicator::query()->orderBy('order');

                $standardId = $scopedByStandardFilter
                    ? ($livewire->tableFilters[$scopedByStandardFilter]['value'] ?? null)
                    : null;

                if ($standardId) {
                    $query->where('standard_id', $standardId);
                }

                return $query->pluck('name', 'id')->all();
            });

        return static::scopeThroughRelation($filter, $throughRelation, 'indicator_id');
    }

    /**
     * @param  array<int, string>  $resetsFilters
     */
    private static function liveAndResets(SelectFilter $filter, array $resetsFilters): void
    {
        if ($resetsFilters === []) {
            return;
        }

        $filter->modifyFormFieldUsing(fn (Select $field): Select => $field
            ->live()
            ->afterStateUpdated(function ($set) use ($resetsFilters): void {
                foreach ($resetsFilters as $resetsFilter) {
                    $set("../{$resetsFilter}.value", null);
                }
            }));
    }

    private static function scopeThroughRelation(SelectFilter $filter, ?string $relation, string $column): SelectFilter
    {
        if ($relation === null) {
            return $filter;
        }

        return $filter->query(fn (Builder $query, array $data): Builder => $query->when(
            $data['value'],
            fn (Builder $query, $value) => $query->whereHas(
                $relation,
                fn (Builder $query) => $query->where($column, $value)
            ),
        ));
    }
}
