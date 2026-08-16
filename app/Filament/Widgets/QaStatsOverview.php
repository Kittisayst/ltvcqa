<?php

namespace App\Filament\Widgets;

use App\Models\AcademicYear;
use App\Models\Document;
use App\Models\Report;
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

        $activeYear = AcademicYear::where('is_active', true)->first();

        return [
            Stat::make('ປີການສຶກສາປັດຈຸບັນ', $activeYear?->name ?? 'ຍັງບໍ່ໄດ້ກຳນົດ')
                ->description($activeYear?->framework?->name ?? '')
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
