<?php

namespace App\Filament\Resources\Documents\Tables;

use App\Models\AcademicYear;
use App\Models\Department;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class DocumentsTable
{
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
            ->filters([
                SelectFilter::make('academic_year_id')
                    ->label('ປີການສຶກສາ')
                    ->options(fn () => AcademicYear::orderByDesc('name')->pluck('name', 'id')),
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
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
