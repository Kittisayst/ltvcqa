<?php

namespace App\Filament\Widgets;

use App\Models\AcademicYear;
use App\Models\BasisMain;
use App\Models\Department;
use Filament\Widgets\ChartWidget;

class DepartmentCompletionChart extends ChartWidget
{
    protected ?string $heading = 'ຄວາມຄືບໜ້າການສົ່ງຫຼັກຖານຕາມພະແນກ';

    protected static ?int $sort = 3;

    public static function canView(): bool
    {
        return auth()->user()?->hasAnyRole(['super_admin', 'assessor']) ?? false;
    }

    protected function getData(): array
    {
        $activeYear = AcademicYear::active();

        if (! $activeYear) {
            return [
                'datasets' => [],
                'labels' => [],
            ];
        }

        $totalBasisMains = BasisMain::whereHas(
            'indicator.standard',
            fn ($query) => $query->where('framework_id', $activeYear->framework_id)
        )->count();

        $departments = Department::orderBy('name')->get();

        $percentages = $departments->map(function (Department $department) use ($activeYear, $totalBasisMains): int {
            if ($totalBasisMains === 0) {
                return 0;
            }

            $completed = BasisMain::whereHas(
                'indicator.standard',
                fn ($query) => $query->where('framework_id', $activeYear->framework_id)
            )->whereHas(
                'documents',
                fn ($query) => $query->where('academic_year_id', $activeYear->id)
                    ->whereHas('user', fn ($query) => $query->where('department_id', $department->id))
            )->count();

            return (int) round($completed / $totalBasisMains * 100);
        });

        return [
            'datasets' => [
                [
                    'label' => 'ອັດຕາຄວາມຄືບໜ້າ (%)',
                    'data' => $percentages->all(),
                    'backgroundColor' => $percentages->map(fn (int $percent): string => match (true) {
                        $percent >= 100 => '#22c55e',
                        $percent >= 50 => '#f59e0b',
                        default => '#ef4444',
                    })->all(),
                ],
            ],
            'labels' => $departments->pluck('name')->all(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'min' => 0,
                    'max' => 100,
                ],
            ],
        ];
    }
}
