<?php

namespace App\Filament\Resources\Reports\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ReportsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('academicYear.name')
                    ->label('ປີການສຶກສາ')
                    ->searchable(),
                TextColumn::make('department.name')
                    ->label('ພະແນກ/ພາກວິຊາ')
                    ->searchable(),
                TextColumn::make('indicator.name')
                    ->label('ຕົວຊີ້ວັດ')
                    ->wrap()
                    ->searchable(),
                TextColumn::make('score')
                    ->label('ຄະແນນ')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('status')
                    ->label('ສະຖານະ')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'submitted' => 'ສົ່ງແລ້ວ',
                        'approved' => 'ອະນຸມັດ',
                        default => 'ຮ່າງ',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'submitted' => 'warning',
                        'approved' => 'success',
                        default => 'gray',
                    }),
                TextColumn::make('assessor.name')
                    ->label('ຜູ້ປະເມີນ')
                    ->searchable(),
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
                SelectFilter::make('academic_year_id')
                    ->label('ປີການສຶກສາ')
                    ->relationship('academicYear', 'name'),
                SelectFilter::make('department_id')
                    ->label('ພະແນກ/ພາກວິຊາ')
                    ->relationship('department', 'name'),
                SelectFilter::make('status')
                    ->label('ສະຖານະ')
                    ->options([
                        'draft' => 'ຮ່າງ',
                        'submitted' => 'ສົ່ງແລ້ວ',
                        'approved' => 'ອະນຸມັດ',
                    ]),
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
