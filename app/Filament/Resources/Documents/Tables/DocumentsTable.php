<?php

namespace App\Filament\Resources\Documents\Tables;

use App\Models\AcademicYear;
use App\Models\BasisMain;
use App\Models\Department;
use App\Models\Indicator;
use App\Models\Standard;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class DocumentsTable
{
    /**
     * Reads a sibling filter's current (possibly not-yet-applied) value.
     * `Get` injection into SelectFilter::options() closures isn't wired to
     * a container in this Filament version and errors when the filters
     * panel renders — reading the Livewire component's own filter state
     * directly is the reliable alternative.
     */
    private static function filterValue(HasTable $livewire, string $filter): mixed
    {
        return $livewire->tableFilters[$filter]['value']
            ?? $livewire->tableDeferredFilters[$filter]['value']
            ?? null;
    }

    public static function configure(Table $table): Table
    {
        return $table
            ->groups([
                Group::make('academicYear.name')
                    ->label('ປີການສຶກສາ')
                    ->orderQueryUsing(fn (Builder $query, string $direction) => $query->orderBy('academic_year_id', $direction)),
            ])
            ->defaultGroup('academicYear.name')
            ->columns([
                TextColumn::make('academicYear.name')
                    ->label('ປີການສຶກສາ')
                    ->badge()
                    ->searchable(),
                TextColumn::make('user.department.name')
                    ->label('ພະແນກ/ພາກວິຊາ')
                    ->badge()
                    ->color('gray')
                    ->searchable(),
                TextColumn::make('basisMain.title')
                    ->label('ຫຼັກຖານ')
                    ->wrap()
                    ->searchable(),
                TextColumn::make('files.reference_no')
                    ->label('ເລກທີ່')
                    ->badge()
                    ->limitList(2)
                    ->expandableLimitedList()
                    ->searchable(),
                TextColumn::make('files_count')
                    ->label('ຈຳນວນໄຟລ໌ແນບ')
                    ->counts('files')
                    ->badge(),
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
            ->defaultPaginationPageOption(50)
            ->filters([
                SelectFilter::make('academic_year_id')
                    ->label('ປີການສຶກສາ')
                    ->options(fn () => AcademicYear::orderByDesc('name')->pluck('name', 'id'))
                    ->default(fn () => AcademicYear::active()?->id)
                    ->modifyFormFieldUsing(fn (Select $field) => $field->live()),
                SelectFilter::make('department_id')
                    ->label('ພະແນກ/ພາກວິຊາ')
                    ->options(fn () => Department::pluck('name', 'id'))
                    ->query(fn (Builder $query, array $data) => $query->when(
                        $data['value'],
                        fn (Builder $query, $value) => $query->whereHas(
                            'user',
                            fn (Builder $query) => $query->where('department_id', $value)
                        )
                    )),
                SelectFilter::make('standard_id')
                    ->label('ມາດຕະຖານ')
                    ->options(function (HasTable $livewire): array {
                        $academicYearId = self::filterValue($livewire, 'academic_year_id');
                        $frameworkId = $academicYearId ? AcademicYear::find($academicYearId)?->framework_id : null;

                        return Standard::query()
                            ->when($frameworkId, fn (Builder $query) => $query->where('framework_id', $frameworkId))
                            ->orderBy('order')
                            ->pluck('name', 'id')
                            ->all();
                    })
                    ->modifyFormFieldUsing(fn (Select $field) => $field->live())
                    ->query(fn (Builder $query, array $data) => $query->when(
                        $data['value'],
                        fn (Builder $query, $value) => $query->whereHas(
                            'basisMain.indicator',
                            fn (Builder $query) => $query->where('standard_id', $value)
                        )
                    )),
                SelectFilter::make('indicator_id')
                    ->label('ຕົວຊີ້ວັດ')
                    ->options(function (HasTable $livewire): array {
                        $standardId = self::filterValue($livewire, 'standard_id');
                        $academicYearId = self::filterValue($livewire, 'academic_year_id');
                        $frameworkId = $academicYearId ? AcademicYear::find($academicYearId)?->framework_id : null;

                        return Indicator::query()
                            ->when($standardId, fn (Builder $query) => $query->where('standard_id', $standardId))
                            ->when(
                                (! $standardId) && $frameworkId,
                                fn (Builder $query) => $query->whereHas(
                                    'standard',
                                    fn (Builder $query) => $query->where('framework_id', $frameworkId)
                                )
                            )
                            ->orderBy('order')
                            ->pluck('name', 'id')
                            ->all();
                    })
                    ->modifyFormFieldUsing(fn (Select $field) => $field->live())
                    ->searchable()
                    ->query(fn (Builder $query, array $data) => $query->when(
                        $data['value'],
                        fn (Builder $query, $value) => $query->whereHas(
                            'basisMain',
                            fn (Builder $query) => $query->where('indicator_id', $value)
                        )
                    )),
                SelectFilter::make('basis_main_id')
                    ->label('ຫຼັກຖານ')
                    ->options(function (HasTable $livewire): array {
                        $indicatorId = self::filterValue($livewire, 'indicator_id');
                        $standardId = self::filterValue($livewire, 'standard_id');
                        $academicYearId = self::filterValue($livewire, 'academic_year_id');
                        $frameworkId = $academicYearId ? AcademicYear::find($academicYearId)?->framework_id : null;

                        return BasisMain::query()
                            ->when(
                                $indicatorId,
                                fn (Builder $query) => $query->where('indicator_id', $indicatorId),
                                fn (Builder $query) => $query->when(
                                    $standardId,
                                    fn (Builder $query) => $query->whereHas(
                                        'indicator',
                                        fn (Builder $query) => $query->where('standard_id', $standardId)
                                    ),
                                    fn (Builder $query) => $query->when(
                                        $frameworkId,
                                        fn (Builder $query) => $query->whereHas(
                                            'indicator.standard',
                                            fn (Builder $query) => $query->where('framework_id', $frameworkId)
                                        )
                                    )
                                )
                            )
                            ->orderBy('order')
                            ->pluck('title', 'id')
                            ->all();
                    })
                    ->searchable(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ]);
    }
}
