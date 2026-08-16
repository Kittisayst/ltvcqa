<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Reports\ReportResource;
use App\Models\Report;
use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class PendingReportReviews extends TableWidget
{
    protected static ?string $heading = 'ບົດລາຍງານລໍຖ້າການປະເມີນ';

    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        return auth()->user()?->hasAnyRole(['assessor', 'super_admin']) ?? false;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Report::query()->where('status', 'submitted'))
            ->defaultSort('updated_at')
            ->columns([
                TextColumn::make('department.name')
                    ->label('ພະແນກ/ພາກວິຊາ')
                    ->badge(),
                TextColumn::make('academicYear.name')
                    ->label('ປີການສຶກສາ'),
                TextColumn::make('indicator.name')
                    ->label('ຕົວຊີ້ວັດ')
                    ->wrap(),
                TextColumn::make('updated_at')
                    ->label('ສົ່ງເມື່ອ')
                    ->dateTime()
                    ->sortable(),
            ])
            ->recordActions([
                Action::make('evaluate')
                    ->label('ປະເມີນ')
                    ->icon(Heroicon::OutlinedPencilSquare)
                    ->url(fn (Report $record): string => ReportResource::getUrl('edit', ['record' => $record])),
            ])
            ->emptyStateHeading('ບໍ່ມີບົດລາຍງານລໍຖ້າການປະເມີນ')
            ->paginated(false);
    }
}
