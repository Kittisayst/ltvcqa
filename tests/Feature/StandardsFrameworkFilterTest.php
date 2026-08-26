<?php

use App\Filament\Resources\Standards\Pages\ListStandards;
use App\Models\QaFramework;
use App\Models\Standard;
use Livewire\Livewire;

it('filters standards by framework', function (): void {
    actingAsSuperAdmin();

    $frameworkA = QaFramework::factory()->create();
    $standardA = Standard::factory()->for($frameworkA, 'framework')->create();

    $frameworkB = QaFramework::factory()->create();
    $standardB = Standard::factory()->for($frameworkB, 'framework')->create();

    Livewire::test(ListStandards::class)
        ->filterTable('framework_id', $frameworkA->id)
        ->assertCanSeeTableRecords([$standardA])
        ->assertCanNotSeeTableRecords([$standardB]);
});
