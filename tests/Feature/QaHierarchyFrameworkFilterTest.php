<?php

use App\Filament\Resources\BasisMains\Pages\ListBasisMains;
use App\Filament\Resources\Indicators\Pages\ListIndicators;
use App\Filament\Resources\Standards\Pages\ListStandards;
use App\Models\BasisMain;
use App\Models\Indicator;
use App\Models\QaFramework;
use App\Models\Standard;
use Livewire\Livewire;

it('defaults the standards framework filter to the published framework', function (): void {
    actingAsSuperAdmin();

    $published = QaFramework::factory()->create(['status' => 'published']);
    $standardInPublished = Standard::factory()->for($published, 'framework')->create();

    $draft = QaFramework::factory()->draft()->create();
    $standardInDraft = Standard::factory()->for($draft, 'framework')->create();

    Livewire::test(ListStandards::class)
        ->assertCanSeeTableRecords([$standardInPublished])
        ->assertCanNotSeeTableRecords([$standardInDraft]);
});

it('filters indicators by framework through their standard', function (): void {
    actingAsSuperAdmin();

    $frameworkA = QaFramework::factory()->create();
    $standardA = Standard::factory()->for($frameworkA, 'framework')->create();
    $indicatorA = Indicator::factory()->for($standardA)->create();

    $frameworkB = QaFramework::factory()->create();
    $standardB = Standard::factory()->for($frameworkB, 'framework')->create();
    $indicatorB = Indicator::factory()->for($standardB)->create();

    Livewire::test(ListIndicators::class)
        ->filterTable('framework_id', $frameworkA->id)
        ->assertCanSeeTableRecords([$indicatorA])
        ->assertCanNotSeeTableRecords([$indicatorB]);
});

it('filters basis mains by framework through their indicator and standard', function (): void {
    actingAsSuperAdmin();

    $frameworkA = QaFramework::factory()->create();
    $standardA = Standard::factory()->for($frameworkA, 'framework')->create();
    $indicatorA = Indicator::factory()->for($standardA)->create();
    $basisMainA = BasisMain::factory()->for($indicatorA)->create();

    $frameworkB = QaFramework::factory()->create();
    $standardB = Standard::factory()->for($frameworkB, 'framework')->create();
    $indicatorB = Indicator::factory()->for($standardB)->create();
    $basisMainB = BasisMain::factory()->for($indicatorB)->create();

    Livewire::test(ListBasisMains::class)
        ->filterTable('framework_id', $frameworkA->id)
        ->assertCanSeeTableRecords([$basisMainA])
        ->assertCanNotSeeTableRecords([$basisMainB]);
});
