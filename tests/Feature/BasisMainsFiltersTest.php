<?php

use App\Filament\Resources\BasisMains\Pages\ListBasisMains;
use App\Models\BasisMain;
use App\Models\Indicator;
use App\Models\Standard;
use Livewire\Livewire;

it('filters basis mains by standard', function (): void {
    actingAsSuperAdmin();

    $standardA = Standard::factory()->create();
    $indicatorA = Indicator::factory()->for($standardA)->create();
    $basisMainA = BasisMain::factory()->for($indicatorA)->create();

    $standardB = Standard::factory()->create();
    $indicatorB = Indicator::factory()->for($standardB)->create();
    $basisMainB = BasisMain::factory()->for($indicatorB)->create();

    Livewire::test(ListBasisMains::class)
        ->filterTable('standard_id', $standardA->id)
        ->assertCanSeeTableRecords([$basisMainA])
        ->assertCanNotSeeTableRecords([$basisMainB]);
});

it('filters basis mains by indicator', function (): void {
    actingAsSuperAdmin();

    $indicatorA = Indicator::factory()->create();
    $basisMainA = BasisMain::factory()->for($indicatorA)->create();

    $indicatorB = Indicator::factory()->create();
    $basisMainB = BasisMain::factory()->for($indicatorB)->create();

    Livewire::test(ListBasisMains::class)
        ->filterTable('indicator_id', $indicatorA->id)
        ->assertCanSeeTableRecords([$basisMainA])
        ->assertCanNotSeeTableRecords([$basisMainB]);
});
