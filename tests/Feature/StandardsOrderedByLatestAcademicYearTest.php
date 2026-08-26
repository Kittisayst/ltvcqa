<?php

use App\Filament\Resources\Standards\Pages\ListStandards;
use App\Models\AcademicYear;
use App\Models\QaFramework;
use App\Models\Standard;
use Livewire\Livewire;

it('lists standards with the framework used by the most recently created academic year first', function (): void {
    actingAsSuperAdmin();

    $oldFramework = QaFramework::factory()->create();
    $oldStandard = Standard::factory()->for($oldFramework, 'framework')->create();
    AcademicYear::factory()->for($oldFramework, 'framework')->create(['created_at' => now()->subYear()]);

    $newFramework = QaFramework::factory()->create();
    $newStandard = Standard::factory()->for($newFramework, 'framework')->create();
    AcademicYear::factory()->for($newFramework, 'framework')->create(['created_at' => now()]);

    $unusedFramework = QaFramework::factory()->create();
    $unusedStandard = Standard::factory()->for($unusedFramework, 'framework')->create();

    Livewire::test(ListStandards::class)
        ->filterTable('framework_id', null)
        ->assertSeeInOrder([$newStandard->name, $oldStandard->name, $unusedStandard->name]);
});
