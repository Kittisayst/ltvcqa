<?php

use App\Filament\Resources\Documents\Pages\ListDocuments;
use App\Models\AcademicYear;
use App\Models\Indicator;
use App\Models\QaFramework;
use App\Models\Standard;
use Livewire\Livewire;

it('defaults the academic year filter to the active academic year', function (): void {
    actingAsSuperAdmin();

    $activeYear = AcademicYear::factory()->create(['is_active' => true]);

    Livewire::test(ListDocuments::class)
        ->assertSet('tableFilters.academic_year_id.value', $activeYear->id);
});

it('scopes the standard filter options to the selected academic year\'s framework', function (): void {
    actingAsSuperAdmin();

    $framework = QaFramework::factory()->create();
    $year = AcademicYear::factory()->for($framework, 'framework')->create();
    Standard::factory()->for($framework, 'framework')->create(['name' => 'ໃນ FRAMEWORK']);

    $otherFramework = QaFramework::factory()->create();
    Standard::factory()->for($otherFramework, 'framework')->create(['name' => 'ນອກ FRAMEWORK']);

    $html = Livewire::test(ListDocuments::class)
        ->set('tableFilters.academic_year_id.value', $year->id)
        ->html();

    expect($html)->toContain('ໃນ FRAMEWORK')
        ->not->toContain('ນອກ FRAMEWORK');
});

it('scopes the indicator filter options to the selected standard', function (): void {
    actingAsSuperAdmin();

    $standard = Standard::factory()->create();
    Indicator::factory()->for($standard)->create(['name' => 'ຕົວຊີ້ວັດໃນມາດຕະຖານ']);

    $otherStandard = Standard::factory()->create();
    Indicator::factory()->for($otherStandard)->create(['name' => 'ຕົວຊີ້ວັດອື່ນ']);

    $html = Livewire::test(ListDocuments::class)
        ->set('tableFilters.standard_id.value', $standard->id)
        ->html();

    expect($html)->toContain('ຕົວຊີ້ວັດໃນມາດຕະຖານ')
        ->not->toContain('ຕົວຊີ້ວັດອື່ນ');
});
