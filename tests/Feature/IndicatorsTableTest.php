<?php

use App\Filament\Resources\Indicators\Pages\ListIndicators;
use App\Models\BasisMain;
use App\Models\Indicator;
use App\Models\Standard;
use Livewire\Livewire;

it('shows the basis mains count for each indicator', function (): void {
    actingAsSuperAdmin();

    $indicator = Indicator::factory()->create();
    BasisMain::factory()->for($indicator)->count(3)->create();

    Livewire::test(ListIndicators::class)
        ->assertTableColumnStateSet('basis_mains_count', 3, record: $indicator);
});

it('filters indicators by standard', function (): void {
    actingAsSuperAdmin();

    $standardA = Standard::factory()->create();
    $indicatorA = Indicator::factory()->for($standardA)->create();

    $standardB = Standard::factory()->create();
    $indicatorB = Indicator::factory()->for($standardB)->create();

    Livewire::test(ListIndicators::class)
        ->filterTable('standard_id', $standardA->id)
        ->assertCanSeeTableRecords([$indicatorA])
        ->assertCanNotSeeTableRecords([$indicatorB]);
});
