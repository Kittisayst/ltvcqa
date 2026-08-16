<?php

use App\Filament\Widgets\DepartmentCompletionOverview;
use App\Filament\Widgets\MissingEvidenceTable;
use App\Models\AcademicYear;
use App\Models\BasisMain;
use App\Models\Department;
use App\Models\Document;
use App\Models\Indicator;
use App\Models\QaFramework;
use App\Models\Standard;
use App\Models\User;
use Livewire\Livewire;

it('shows the department completion overview only to super_admin and assessor', function (): void {
    actingAsDepartmentStaff();
    expect(DepartmentCompletionOverview::canView())->toBeFalse();

    actingAsAssessor();
    expect(DepartmentCompletionOverview::canView())->toBeTrue();

    actingAsSuperAdmin();
    expect(DepartmentCompletionOverview::canView())->toBeTrue();
});

it('shows the missing evidence table only to department-scoped roles', function (): void {
    actingAsSuperAdmin();
    expect(MissingEvidenceTable::canView())->toBeFalse();

    actingAsAssessor();
    expect(MissingEvidenceTable::canView())->toBeFalse();

    actingAsDepartmentStaff();
    expect(MissingEvidenceTable::canView())->toBeTrue();
});

it('computes department completion percentages against the active academic year', function (): void {
    $framework = QaFramework::factory()->create();
    $academicYear = AcademicYear::factory()->create(['framework_id' => $framework->id, 'is_active' => true]);
    $standard = Standard::factory()->create(['framework_id' => $framework->id]);
    $indicator = Indicator::factory()->create(['standard_id' => $standard->id]);
    $submittedBasisMain = BasisMain::factory()->create(['indicator_id' => $indicator->id]);
    $missingBasisMain = BasisMain::factory()->create(['indicator_id' => $indicator->id]);

    $department = Department::factory()->create();
    $staff = User::factory()->for($department)->create();

    Document::factory()->create([
        'user_id' => $staff->id,
        'basis_main_id' => $submittedBasisMain->id,
        'academic_year_id' => $academicYear->id,
    ]);

    actingAsSuperAdmin();

    Livewire::test(DepartmentCompletionOverview::class)
        ->assertTableColumnStateSet('percent', 50, record: $department->id)
        ->assertTableColumnStateSet('completed', '1 / 2', record: $department->id);
});

it('lists only the basis mains without a document for the current department and academic year', function (): void {
    $framework = QaFramework::factory()->create();
    $academicYear = AcademicYear::factory()->create(['framework_id' => $framework->id, 'is_active' => true]);
    $standard = Standard::factory()->create(['framework_id' => $framework->id]);
    $indicator = Indicator::factory()->create(['standard_id' => $standard->id]);
    $submittedBasisMain = BasisMain::factory()->create(['indicator_id' => $indicator->id]);
    $missingBasisMain = BasisMain::factory()->create(['indicator_id' => $indicator->id]);

    $department = Department::factory()->create();
    $staff = actingAsDepartmentStaff($department);

    Document::factory()->create([
        'user_id' => $staff->id,
        'basis_main_id' => $submittedBasisMain->id,
        'academic_year_id' => $academicYear->id,
    ]);

    Livewire::test(MissingEvidenceTable::class)
        ->assertCanSeeTableRecords([$missingBasisMain])
        ->assertCanNotSeeTableRecords([$submittedBasisMain]);
});
