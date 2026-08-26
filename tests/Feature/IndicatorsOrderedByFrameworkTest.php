<?php

use App\Filament\Resources\Indicators\Pages\ListIndicators;
use App\Models\AcademicYear;
use App\Models\Indicator;
use App\Models\QaFramework;
use App\Models\Standard;
use Livewire\Livewire;

it('lists indicators with the framework used by the most recently created academic year first', function (): void {
    actingAsSuperAdmin();

    $oldFramework = QaFramework::factory()->create();
    $oldStandard = Standard::factory()->for($oldFramework, 'framework')->create();
    $oldIndicator = Indicator::factory()->for($oldStandard)->create();
    AcademicYear::factory()->for($oldFramework, 'framework')->create(['created_at' => now()->subYear()]);

    $newFramework = QaFramework::factory()->create();
    $newStandard = Standard::factory()->for($newFramework, 'framework')->create();
    $newIndicator = Indicator::factory()->for($newStandard)->create();
    AcademicYear::factory()->for($newFramework, 'framework')->create(['created_at' => now()]);

    Livewire::test(ListIndicators::class)
        ->filterTable('framework_id', null)
        ->assertSeeInOrder([$newIndicator->name, $oldIndicator->name]);
});
