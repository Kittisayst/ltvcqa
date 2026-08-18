<?php

use App\Filament\Widgets\DepartmentCompletionChart;
use App\Models\AcademicYear;
use App\Models\BasisMain;
use App\Models\Department;
use App\Models\Document;
use App\Models\Indicator;
use App\Models\QaFramework;
use App\Models\Standard;
use App\Models\User;

it('is visible to super_admin and assessor but not department-staff', function (): void {
    actingAsAssessor();
    expect(DepartmentCompletionChart::canView())->toBeTrue();

    actingAsSuperAdmin();
    expect(DepartmentCompletionChart::canView())->toBeTrue();

    actingAsDepartmentStaff();
    expect(DepartmentCompletionChart::canView())->toBeFalse();
});

it('computes a completion percentage per department for the active academic year\'s framework', function (): void {
    actingAsSuperAdmin();

    $framework = QaFramework::factory()->create();
    $academicYear = AcademicYear::factory()->for($framework, 'framework')->create(['is_active' => true]);
    $standard = Standard::factory()->for($framework, 'framework')->create();
    $indicator = Indicator::factory()->for($standard)->create();
    $basisMains = BasisMain::factory()->for($indicator)->count(2)->create();

    $department = Department::factory()->create();
    $staff = User::factory()->for($department)->create();

    $document = Document::factory()
        ->for($staff, 'user')
        ->for($academicYear, 'academicYear')
        ->for($basisMains->first(), 'basisMain')
        ->create();
    $document->files()->create([
        'reference_no' => '001',
        'issued_date' => now(),
        'disk' => 'local',
        'path' => 'documents/test.pdf',
        'original_name' => 'test.pdf',
        'mime_type' => 'application/pdf',
        'size' => 100,
    ]);

    $emptyDepartment = Department::factory()->create();

    $data = getChartData(DepartmentCompletionChart::class);

    $departmentIndex = array_search($department->name, $data['labels'], true);
    $emptyDepartmentIndex = array_search($emptyDepartment->name, $data['labels'], true);

    expect($data['datasets'][0]['data'][$departmentIndex])->toBe(50)
        ->and($data['datasets'][0]['data'][$emptyDepartmentIndex])->toBe(0);
});

it('returns empty data when no academic year is active', function (): void {
    actingAsSuperAdmin();

    $data = getChartData(DepartmentCompletionChart::class);

    expect($data['labels'])->toBe([])
        ->and($data['datasets'])->toBe([]);
});
