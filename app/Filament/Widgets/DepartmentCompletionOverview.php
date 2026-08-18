<?php

namespace App\Filament\Widgets;

use App\Models\AcademicYear;
use App\Models\BasisMain;
use App\Models\Department;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Support\Collection;

class DepartmentCompletionOverview extends TableWidget
{
    protected static ?string $heading = 'ຄວາມຄືບໜ້າການສົ່ງຫຼັກຖານຕາມພະແນກ';

    protected static ?int $sort = 10;

    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        return auth()->user()?->hasAnyRole(['super_admin', 'assessor']) ?? false;
    }

    public function table(Table $table): Table
    {
        return $table
            ->records(function (): Collection {
                $activeYear = AcademicYear::active();

                if (! $activeYear) {
                    return collect();
                }

                $totalBasisMains = BasisMain::whereHas(
                    'indicator.standard',
                    fn ($query) => $query->where('framework_id', $activeYear->framework_id)
                )->count();

                return Department::orderBy('name')->get()->map(function (Department $department) use ($activeYear, $totalBasisMains): array {
                    $completed = BasisMain::whereHas(
                        'indicator.standard',
                        fn ($query) => $query->where('framework_id', $activeYear->framework_id)
                    )->whereHas(
                        'documents',
                        fn ($query) => $query->where('academic_year_id', $activeYear->id)
                            ->whereHas('user', fn ($query) => $query->where('department_id', $department->id))
                    )->count();

                    return [
                        'id' => $department->id,
                        'department' => $department->name,
                        'completed' => $completed,
                        'total' => $totalBasisMains,
                        'percent' => $totalBasisMains > 0 ? (int) round($completed / $totalBasisMains * 100) : 0,
                    ];
                })->keyBy('id');
            })
            ->columns([
                TextColumn::make('department')
                    ->label('ພະແນກ/ພາກວິຊາ'),
                TextColumn::make('completed')
                    ->label('ຄົບຖ້ວນ')
                    ->state(fn (array $record): string => "{$record['completed']} / {$record['total']}"),
                TextColumn::make('percent')
                    ->label('ອັດຕາຄວາມຄືບໜ້າ')
                    ->badge()
                    ->suffix('%')
                    ->color(fn (array $record): string => match (true) {
                        $record['percent'] >= 100 => 'success',
                        $record['percent'] >= 50 => 'warning',
                        default => 'danger',
                    }),
            ])
            ->emptyStateHeading('ຍັງບໍ່ໄດ້ກຳນົດປີການສຶກສາປັດຈຸບັນ')
            ->paginated(false);
    }
}
