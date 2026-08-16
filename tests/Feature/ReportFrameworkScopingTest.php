<?php

use App\Filament\Resources\Reports\Pages\CreateReport;
use App\Models\AcademicYear;
use App\Models\Department;
use App\Models\Indicator;
use App\Models\QaFramework;
use App\Models\Report;
use App\Models\Standard;
use Livewire\Livewire;

use function Pest\Laravel\assertDatabaseCount;

it('rejects an indicator that belongs to a different framework than the selected academic year', function (): void {
    actingAsSuperAdmin();

    $academicYear = AcademicYear::factory()->create();
    $indicatorFromAnotherFramework = Indicator::factory()->create();
    $department = Department::factory()->create();

    Livewire::test(CreateReport::class)
        ->fillForm([
            'academic_year_id' => $academicYear->id,
            'department_id' => $department->id,
            'indicator_id' => $indicatorFromAnotherFramework->id,
            'status' => 'draft',
        ])
        ->call('create')
        ->assertHasFormErrors(['indicator_id']);

    assertDatabaseCount(Report::class, 0);
});

it('accepts an indicator that belongs to the same framework as the selected academic year', function (): void {
    actingAsSuperAdmin();

    $framework = QaFramework::factory()->create();
    $academicYear = AcademicYear::factory()->create(['framework_id' => $framework->id]);
    $standard = Standard::factory()->create(['framework_id' => $framework->id]);
    $indicator = Indicator::factory()->create(['standard_id' => $standard->id]);
    $department = Department::factory()->create();

    Livewire::test(CreateReport::class)
        ->fillForm([
            'academic_year_id' => $academicYear->id,
            'department_id' => $department->id,
            'indicator_id' => $indicator->id,
            'status' => 'draft',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    assertDatabaseCount(Report::class, 1);
});

it('rejects a duplicate report for the same indicator, department and academic year', function (): void {
    actingAsSuperAdmin();

    $framework = QaFramework::factory()->create();
    $academicYear = AcademicYear::factory()->create(['framework_id' => $framework->id]);
    $standard = Standard::factory()->create(['framework_id' => $framework->id]);
    $indicator = Indicator::factory()->create(['standard_id' => $standard->id]);
    $department = Department::factory()->create();

    Report::factory()->create([
        'indicator_id' => $indicator->id,
        'department_id' => $department->id,
        'academic_year_id' => $academicYear->id,
    ]);

    Livewire::test(CreateReport::class)
        ->fillForm([
            'academic_year_id' => $academicYear->id,
            'department_id' => $department->id,
            'indicator_id' => $indicator->id,
            'status' => 'draft',
        ])
        ->call('create')
        ->assertHasFormErrors(['indicator_id']);

    assertDatabaseCount(Report::class, 1);
});
