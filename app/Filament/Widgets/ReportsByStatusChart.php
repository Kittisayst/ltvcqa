<?php

namespace App\Filament\Widgets;

use App\Models\Report;
use Filament\Widgets\ChartWidget;

class ReportsByStatusChart extends ChartWidget
{
    protected ?string $heading = 'ບົດລາຍງານຕາມສະຖານະ';

    protected function getData(): array
    {
        $user = auth()->user();
        $isScoped = $user && ! $user->hasAnyRole(['super_admin', 'assessor']);

        $counts = Report::query()
            ->when($isScoped, fn ($query) => $query->where('department_id', $user->department_id))
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return [
            'datasets' => [
                [
                    'label' => 'ຈຳນວນບົດລາຍງານ',
                    'data' => [
                        $counts->get('draft', 0),
                        $counts->get('submitted', 0),
                        $counts->get('approved', 0),
                    ],
                    'backgroundColor' => ['#9ca3af', '#f59e0b', '#22c55e'],
                ],
            ],
            'labels' => ['ຮ່າງ', 'ສົ່ງແລ້ວ', 'ອະນຸມັດ'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
