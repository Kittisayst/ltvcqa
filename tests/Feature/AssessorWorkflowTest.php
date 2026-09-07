<?php

use App\Filament\Resources\Reports\Pages\EditReport;
use App\Filament\Resources\Reports\Pages\ListReports;
use App\Models\AcademicYear;
use App\Models\Department;
use App\Models\Indicator;
use App\Models\QaFramework;
use App\Models\Report;
use App\Models\Standard;
use App\Models\User;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;

it('lets an assessor switch the console between any department', function (): void {
    AcademicYear::forgetActiveCache();
    actingAsAssessor();

    $framework = QaFramework::factory()->create();
    $year = AcademicYear::factory()->create(['framework_id' => $framework->id, 'is_active' => true]);
    $standard = Standard::factory()->create(['framework_id' => $framework->id]);
    $indicator = Indicator::factory()->create(['standard_id' => $standard->id]);

    $departmentA = Department::factory()->create();
    $departmentB = Department::factory()->create();

    Report::factory()->create(['indicator_id' => $indicator->id, 'department_id' => $departmentA->id, 'academic_year_id' => $year->id, 'status' => 'submitted']);
    Report::factory()->create(['indicator_id' => $indicator->id, 'department_id' => $departmentB->id, 'academic_year_id' => $year->id, 'status' => 'approved']);

    Livewire::test(ListReports::class)
        ->set('tableFilters.department_id.value', $departmentA->id)
        ->assertSee('ສົ່ງແລ້ວ')
        ->set('tableFilters.department_id.value', $departmentB->id)
        ->assertSee('ອະນຸມັດ');
});

it('can update evaluation fields but not reassign the report to a different indicator, department, or year', function (): void {
    $assessor = actingAsAssessor();

    $framework = QaFramework::factory()->create();
    $academicYear = AcademicYear::factory()->create(['framework_id' => $framework->id]);
    $standard = Standard::factory()->create(['framework_id' => $framework->id]);
    $indicator = Indicator::factory()->create(['standard_id' => $standard->id]);
    $department = Department::factory()->create();

    $report = Report::factory()->create([
        'indicator_id' => $indicator->id,
        'department_id' => $department->id,
        'academic_year_id' => $academicYear->id,
        'status' => 'submitted',
        'score' => null,
    ]);

    $anotherIndicator = Indicator::factory()->create(['standard_id' => $standard->id]);
    $anotherDepartment = Department::factory()->create();

    Livewire::test(EditReport::class, ['record' => $report->id])
        ->fillForm([
            'indicator_id' => $anotherIndicator->id,
            'department_id' => $anotherDepartment->id,
            'score' => 92,
            'status' => 'approved',
            'good_point' => 'ດີຫຼາຍ',
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $report->refresh();

    expect($report->indicator_id)->toBe($indicator->id)
        ->and($report->department_id)->toBe($department->id)
        ->and((float) $report->score)->toBe(92.0)
        ->and($report->status)->toBe('approved')
        ->and($report->good_point)->toBe('ດີຫຼາຍ')
        ->and($report->assessor_id)->toBe($assessor->id);
});

it('is always recorded as its own assessor regardless of the submitted assessor', function (): void {
    $assessor = actingAsAssessor();
    $otherAssessor = User::factory()->create();
    $otherAssessor->assignRole(Role::findOrCreate('assessor', 'web'));

    $framework = QaFramework::factory()->create();
    $academicYear = AcademicYear::factory()->create(['framework_id' => $framework->id]);
    $standard = Standard::factory()->create(['framework_id' => $framework->id]);
    $indicator = Indicator::factory()->create(['standard_id' => $standard->id]);

    $report = Report::factory()->create([
        'indicator_id' => $indicator->id,
        'academic_year_id' => $academicYear->id,
    ]);

    Livewire::test(EditReport::class, ['record' => $report->id])
        ->fillForm([
            'assessor_id' => $otherAssessor->id,
            'status' => 'submitted',
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($report->fresh()->assessor_id)->toBe($assessor->id);
});
