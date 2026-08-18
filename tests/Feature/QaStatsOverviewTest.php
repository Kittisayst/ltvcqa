<?php

use App\Filament\Widgets\QaStatsOverview;
use App\Models\AcademicYear;
use App\Models\BasisMain;
use App\Models\Indicator;
use App\Models\QaFramework;
use App\Models\Standard;
use Livewire\Livewire;

it('shows standard, indicator, and basis main counts for the active academic year\'s framework', function (): void {
    actingAsSuperAdmin();

    $activeFramework = QaFramework::factory()->create();
    AcademicYear::factory()->for($activeFramework, 'framework')->create(['is_active' => true]);

    $standard = Standard::factory()->for($activeFramework, 'framework')->create();
    $indicator = Indicator::factory()->for($standard)->create();
    BasisMain::factory()->for($indicator)->count(3)->create();

    // Belongs to a different (inactive) framework — must not be counted.
    $otherFramework = QaFramework::factory()->create();
    Standard::factory()->for($otherFramework, 'framework')->create();

    Livewire::test(QaStatsOverview::class)
        ->assertSee('ມາດຕະຖານ')
        ->assertSee('1')
        ->assertSee('3');
});

it('shows zero counts when no academic year is active', function (): void {
    actingAsSuperAdmin();

    AcademicYear::query()->update(['is_active' => false]);

    Livewire::test(QaStatsOverview::class)
        ->assertSee('ຍັງບໍ່ໄດ້ກຳນົດ');
});
