<?php

use App\Filament\Resources\QaFrameworks\Pages\ListQaFrameworks;
use App\Models\BasisMain;
use App\Models\Indicator;
use App\Models\QaFramework;
use App\Models\Standard;
use Livewire\Livewire;

it('shows the standard, indicator, and basis main counts for each framework', function (): void {
    actingAsSuperAdmin();

    $framework = QaFramework::factory()->create();

    $standardA = Standard::factory()->for($framework, 'framework')->create();
    $standardB = Standard::factory()->for($framework, 'framework')->create();

    $indicatorA = Indicator::factory()->for($standardA)->create();
    $indicatorB = Indicator::factory()->for($standardB)->create();
    $indicatorC = Indicator::factory()->for($standardB)->create();

    BasisMain::factory()->for($indicatorA)->count(2)->create();
    BasisMain::factory()->for($indicatorB)->create();
    BasisMain::factory()->for($indicatorC)->count(3)->create();

    Livewire::test(ListQaFrameworks::class)
        ->assertTableColumnStateSet('standards_count', 2, record: $framework)
        ->assertTableColumnStateSet('indicators_count', 3, record: $framework)
        ->assertTableColumnStateSet('basis_mains_count', 6, record: $framework);
});
