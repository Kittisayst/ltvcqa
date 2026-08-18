<?php

namespace App\Filament\Widgets;

use App\Models\AcademicYear;
use App\Models\BasisMain;
use App\Models\Document;
use App\Models\Indicator;
use App\Models\Report;
use App\Models\Standard;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class QaStatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $user = auth()->user();
        $isScoped = $user && ! $user->hasAnyRole(['super_admin', 'assessor']);

        $documents = Document::query()
            ->when($isScoped, fn ($query) => $query->whereHas(
                'user',
                fn ($query) => $query->where('department_id', $user->department_id)
            ))
            ->count();

        $reports = Report::query()
            ->when($isScoped, fn ($query) => $query->where('department_id', $user->department_id));

        $activeYear = AcademicYear::active();
        $frameworkId = $activeYear?->framework_id;

        $standards = Standard::where('framework_id', $frameworkId)->count();
        $indicators = Indicator::whereHas('standard', fn ($query) => $query->where('framework_id', $frameworkId))->count();
        $basisMains = BasisMain::whereHas('indicator.standard', fn ($query) => $query->where('framework_id', $frameworkId))->count();

        return [
            Stat::make('ປີການສຶກສາປັດຈຸບັນ', $activeYear?->name ?? 'ຍັງບໍ່ໄດ້ກຳນົດ')
                ->description($activeYear?->framework?->name ?? '')
                ->color('gray'),
            Stat::make('ມາດຕະຖານ', $standards)
                ->color('gray'),
            Stat::make('ຕົວຊີ້ວັດ', $indicators)
                ->color('gray'),
            Stat::make('ຫຼັກຖານ', $basisMains)
                ->color('gray'),
            Stat::make('ເອກະສານທັງໝົດ', $documents)
                ->color('primary'),
            Stat::make('ບົດລາຍງານ - ຮ່າງ', (clone $reports)->where('status', 'draft')->count())
                ->color('gray'),
            Stat::make('ບົດລາຍງານ - ອະນຸມັດແລ້ວ', (clone $reports)->where('status', 'approved')->count())
                ->color('success'),
        ];
    }
}
