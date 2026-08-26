<?php

use App\Filament\Resources\BasisMains\Pages\ListBasisMains;
use App\Filament\Resources\Indicators\Pages\ListIndicators;
use App\Models\BasisMain;
use App\Models\Indicator;
use App\Models\QaFramework;
use App\Models\Standard;
use Livewire\Livewire;

it('narrows the indicators standard filter options to the selected framework', function (): void {
    actingAsSuperAdmin();

    $frameworkA = QaFramework::factory()->create();
    $standardA = Standard::factory()->for($frameworkA, 'framework')->create();

    $frameworkB = QaFramework::factory()->create();
    $standardB = Standard::factory()->for($frameworkB, 'framework')->create();

    Livewire::test(ListIndicators::class)
        ->filterTable('framework_id', $frameworkA->id)
        ->assertSee($standardA->name)
        ->assertDontSee($standardB->name);
});

it('narrows the basis mains indicator filter options to the selected standard', function (): void {
    actingAsSuperAdmin();

    $standardA = Standard::factory()->create();
    $indicatorA = Indicator::factory()->for($standardA)->create();
    BasisMain::factory()->for($indicatorA)->create();

    $standardB = Standard::factory()->create();
    $indicatorB = Indicator::factory()->for($standardB)->create();
    BasisMain::factory()->for($indicatorB)->create();

    Livewire::test(ListBasisMains::class)
        ->filterTable('standard_id', $standardA->id)
        ->assertSee($indicatorA->name)
        ->assertDontSee($indicatorB->name);
});
