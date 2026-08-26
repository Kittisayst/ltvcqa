<?php

use App\Filament\Resources\AcademicYears\Pages\ManageAcademicYears;
use App\Models\AcademicYear;
use App\Models\QaFramework;
use Livewire\Livewire;

it('activates a year with a published framework and deactivates the previously active one', function (): void {
    actingAsSuperAdmin();

    $oldFramework = QaFramework::factory()->create(['status' => 'draft']);
    $oldYear = AcademicYear::factory()->for($oldFramework, 'framework')->create(['is_active' => true]);

    $newFramework = QaFramework::factory()->create(['status' => 'published']);
    $newYear = AcademicYear::factory()->for($newFramework, 'framework')->create(['is_active' => false]);

    Livewire::test(ManageAcademicYears::class)
        ->callTableAction('activate', $newYear);

    expect($oldYear->fresh()->is_active)->toBeFalse()
        ->and($newYear->fresh()->is_active)->toBeTrue()
        ->and(AcademicYear::active()->id)->toBe($newYear->id);
});

it('refuses to activate a year whose framework is still a draft', function (): void {
    actingAsSuperAdmin();

    $framework = QaFramework::factory()->create(['status' => 'draft']);
    $year = AcademicYear::factory()->for($framework, 'framework')->create(['is_active' => false]);

    Livewire::test(ManageAcademicYears::class)
        ->callTableAction('activate', $year);

    expect($year->fresh()->is_active)->toBeFalse();
});

it('hides the activate action for the already active year', function (): void {
    actingAsSuperAdmin();

    $year = AcademicYear::factory()->create(['is_active' => true]);

    Livewire::test(ManageAcademicYears::class)
        ->assertTableActionHidden('activate', $year);
});
