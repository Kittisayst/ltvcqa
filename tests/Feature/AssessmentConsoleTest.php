<?php

use App\Filament\Resources\Reports\Pages\ListReports;
use App\Models\AcademicYear;
use App\Models\BasisMain;
use App\Models\Department;
use App\Models\Document;
use App\Models\DocumentFile;
use App\Models\Indicator;
use App\Models\QaFramework;
use App\Models\Report;
use App\Models\Standard;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;

beforeEach(fn () => AcademicYear::forgetActiveCache());

/**
 * Builds a published framework with two standards, two indicators each,
 * plus an active academic year pointing at it.
 *
 * @return array{year: AcademicYear, framework: QaFramework, indicators: Collection<int, Indicator>}
 */
function assessmentFixture(): array
{
    $framework = QaFramework::factory()->create();
    $year = AcademicYear::factory()->for($framework, 'framework')->create(['is_active' => true, 'name' => '2024-2025']);

    $standardA = Standard::factory()->for($framework, 'framework')->create(['name' => 'ມາດຕະຖານ ກ', 'order' => 1]);
    $standardB = Standard::factory()->for($framework, 'framework')->create(['name' => 'ມາດຕະຖານ ຂ', 'order' => 2]);

    $indicators = collect([
        Indicator::factory()->for($standardA)->create(['name' => 'ຕົວຊີ້ວັດ ກ1', 'order' => 1]),
        Indicator::factory()->for($standardA)->create(['name' => 'ຕົວຊີ້ວັດ ກ2', 'order' => 2]),
        Indicator::factory()->for($standardB)->create(['name' => 'ຕົວຊີ້ວັດ ຂ1', 'order' => 1]),
        Indicator::factory()->for($standardB)->create(['name' => 'ຕົວຊີ້ວັດ ຂ2', 'order' => 2]),
    ]);

    return ['year' => $year, 'framework' => $framework, 'indicators' => $indicators];
}

it('shows no rows until both academic year and department are chosen', function (): void {
    actingAsAssessor();
    ['indicators' => $indicators] = assessmentFixture();

    // Year is auto-selected (active), but department is not.
    Livewire::test(ListReports::class)
        ->assertCanNotSeeTableRecords($indicators);
});

it('lists every indicator of the selected year framework once a department is picked', function (): void {
    actingAsAssessor();
    ['indicators' => $indicators, 'year' => $year] = assessmentFixture();
    $department = Department::factory()->create();

    Livewire::test(ListReports::class)
        ->set('tableFilters.department_id.value', $department->id)
        ->assertCanSeeTableRecords($indicators)
        ->assertSee('ຕົວຊີ້ວັດ ກ1')
        ->assertSee('ມາດຕະຖານ ກ');
});

it('does not list indicators from another framework', function (): void {
    actingAsAssessor();
    ['indicators' => $indicators] = assessmentFixture();
    $department = Department::factory()->create();

    $otherFrameworkIndicator = Indicator::factory()
        ->for(Standard::factory()->for(QaFramework::factory(), 'framework'))
        ->create();

    Livewire::test(ListReports::class)
        ->set('tableFilters.department_id.value', $department->id)
        ->assertCanSeeTableRecords($indicators)
        ->assertCanNotSeeTableRecords([$otherFrameworkIndicator]);
});

it('shows a not-assessed badge for an indicator with no report and the report status otherwise', function (): void {
    actingAsAssessor();
    ['indicators' => $indicators, 'year' => $year] = assessmentFixture();
    $department = Department::factory()->create();

    Report::factory()->create([
        'indicator_id' => $indicators->first()->id,
        'department_id' => $department->id,
        'academic_year_id' => $year->id,
        'status' => 'submitted',
    ]);

    Livewire::test(ListReports::class)
        ->set('tableFilters.department_id.value', $department->id)
        ->assertSee('ຍັງບໍ່ໄດ້ປະເມີນ')
        ->assertSee('ສົ່ງແລ້ວ');
});

it('locks a department-staff user to their own department', function (): void {
    $department = Department::factory()->create();
    actingAsDepartmentStaff($department);
    ['indicators' => $indicators, 'year' => $year] = assessmentFixture();

    $ownReport = Report::factory()->create([
        'indicator_id' => $indicators->first()->id,
        'department_id' => $department->id,
        'academic_year_id' => $year->id,
        'status' => 'approved',
    ]);

    // No department filter set — a department-staff user is auto-scoped.
    Livewire::test(ListReports::class)
        ->assertCanSeeTableRecords($indicators)
        ->assertSee('ອະນຸມັດ');
});

it('shows evidence progress per indicator scoped to the selected department and year', function (): void {
    actingAsAssessor();
    ['indicators' => $indicators, 'year' => $year] = assessmentFixture();
    $department = Department::factory()->create();
    $indicator = $indicators->first();

    $basisMainA = BasisMain::factory()->for($indicator)->create();
    $basisMainB = BasisMain::factory()->for($indicator)->create();

    $ownStaff = User::factory()->for($department)->create();
    Document::factory()->for($ownStaff, 'user')->for($year, 'academicYear')->for($basisMainA, 'basisMain')->create();

    // Another department's document on basisMainB must not count.
    $otherStaff = User::factory()->for(Department::factory())->create();
    Document::factory()->for($otherStaff, 'user')->for($year, 'academicYear')->for($basisMainB, 'basisMain')->create();

    Livewire::test(ListReports::class)
        ->set('tableFilters.department_id.value', $department->id)
        ->assertSee('1 / 2 ຫຼັກຖານ');
});

it('renders the console without an N+1 as indicators grow', function (): void {
    actingAsAssessor();
    $department = Department::factory()->create();

    $framework = QaFramework::factory()->create();
    AcademicYear::factory()->for($framework, 'framework')->create(['is_active' => true]);
    $standard = Standard::factory()->for($framework, 'framework')->create();

    $countQueries = function () use ($department): int {
        AcademicYear::forgetActiveCache();
        DB::flushQueryLog();
        DB::enableQueryLog();
        Livewire::test(ListReports::class)->set('tableFilters.department_id.value', $department->id);
        $count = count(DB::getQueryLog());
        DB::disableQueryLog();

        return $count;
    };

    Indicator::factory()->for($standard)->hasBasisMains(2)->create();
    $withOne = $countQueries();

    Indicator::factory()->for($standard)->count(5)->hasBasisMains(2)->create();
    $withSix = $countQueries();

    expect($withSix - $withOne)->toBeLessThanOrEqual(2);
});

it('stamps the current assessor when they save the evaluate modal', function (): void {
    $assessor = actingAsAssessor();
    ['indicators' => $indicators] = assessmentFixture();
    $department = Department::factory()->create();

    Livewire::test(ListReports::class)
        ->set('tableFilters.department_id.value', $department->id)
        ->callTableAction('evaluate', $indicators->first()->id, [
            'status' => 'submitted',
            'score' => 70,
        ])
        ->assertHasNoTableActionErrors();

    expect(Report::first()->assessor_id)->toBe($assessor->id);
});

it('lists the department evidence for the indicator inside the evaluate modal', function (): void {
    actingAsAssessor();
    ['indicators' => $indicators, 'year' => $year] = assessmentFixture();
    $department = Department::factory()->create();
    $indicator = $indicators->first();

    $withEvidence = BasisMain::factory()->for($indicator)->create(['title' => 'ມີວິໄສທັດ', 'order' => 1]);
    BasisMain::factory()->for($indicator)->create(['title' => 'ມີແຜນຍຸດທະສາດ', 'order' => 2]);

    $staff = User::factory()->for($department)->create();
    $document = Document::factory()->for($staff, 'user')->for($year, 'academicYear')->for($withEvidence, 'basisMain')->create();
    DocumentFile::factory()->for($document)->create(['original_name' => 'vision.pdf']);

    $schema = Livewire::test(ListReports::class)
        ->set('tableFilters.department_id.value', $department->id)
        ->mountTableAction('evaluate', $indicator->id)
        ->instance()
        ->getMountedTableActionForm();

    $evidence = (string) $schema->getComponent('evidence')->getContent();

    expect($evidence)
        ->toContain('ມີວິໄສທັດ')
        ->toContain('vision.pdf')
        ->toContain('ມີແຜນຍຸດທະສາດ')
        ->toContain('ຍັງບໍ່ມີຫຼັກຖານ');
});

it('gives department staff a read-only evaluate modal that cannot persist changes', function (): void {
    $department = Department::factory()->create();
    actingAsDepartmentStaff($department);
    ['indicators' => $indicators, 'year' => $year] = assessmentFixture();
    $indicator = $indicators->first();

    $report = Report::factory()->create([
        'indicator_id' => $indicator->id,
        'department_id' => $department->id,
        'academic_year_id' => $year->id,
        'status' => 'draft',
        'score' => 55,
    ]);

    Livewire::test(ListReports::class)
        ->assertTableActionVisible('evaluate', $indicator)
        ->callTableAction('evaluate', $indicator->id, ['status' => 'submitted', 'score' => 99]);

    expect($report->fresh()->score)->toEqual(55)
        ->and($report->fresh()->status)->toBe('draft');
});

it('shows a scope summary line and per-standard averages', function (): void {
    actingAsAssessor();
    ['indicators' => $indicators, 'year' => $year] = assessmentFixture();
    $department = Department::factory()->create();

    Report::factory()->create([
        'indicator_id' => $indicators[0]->id, 'department_id' => $department->id,
        'academic_year_id' => $year->id, 'status' => 'approved', 'score' => 80,
    ]);
    Report::factory()->create([
        'indicator_id' => $indicators[1]->id, 'department_id' => $department->id,
        'academic_year_id' => $year->id, 'status' => 'draft', 'score' => 60,
    ]);

    Livewire::test(ListReports::class)
        ->set('tableFilters.department_id.value', $department->id)
        ->assertSee('ປະເມີນແລ້ວ 2/4 ຕົວຊີ້ວັດ')
        ->assertSee('ອະນຸມັດ 1')
        ->assertSee('ຄະແນນສະເລ່ຍ 70.0');
});

it('filters rows by assessment state', function (): void {
    actingAsAssessor();
    ['indicators' => $indicators, 'year' => $year] = assessmentFixture();
    $department = Department::factory()->create();

    Report::factory()->create([
        'indicator_id' => $indicators[0]->id, 'department_id' => $department->id,
        'academic_year_id' => $year->id, 'status' => 'approved', 'score' => 80,
    ]);

    Livewire::test(ListReports::class)
        ->set('tableFilters.department_id.value', $department->id)
        ->set('tableFilters.assessment_state.value', 'approved')
        ->assertCanSeeTableRecords([$indicators[0]])
        ->assertCanNotSeeTableRecords([$indicators[1], $indicators[2], $indicators[3]])
        ->set('tableFilters.assessment_state.value', 'not_assessed')
        ->assertCanNotSeeTableRecords([$indicators[0]])
        ->assertCanSeeTableRecords([$indicators[1], $indicators[2], $indicators[3]]);
});

it('shows the approve action only to super_admin on submitted reports and it approves', function (): void {
    $superAdmin = actingAsSuperAdmin();
    ['indicators' => $indicators, 'year' => $year] = assessmentFixture();
    $department = Department::factory()->create();
    $indicator = $indicators->first();

    $submitted = Report::factory()->create([
        'indicator_id' => $indicator->id,
        'department_id' => $department->id,
        'academic_year_id' => $year->id,
        'status' => 'submitted',
        'assessor_id' => User::factory()->create()->id,
    ]);
    $assessorId = $submitted->assessor_id;

    Livewire::test(ListReports::class)
        ->set('tableFilters.department_id.value', $department->id)
        ->assertTableActionVisible('approve', $indicator)
        ->callTableAction('approve', $indicator->id);

    expect($submitted->fresh()->status)->toBe('approved')
        ->and($submitted->fresh()->assessor_id)->toBe($assessorId);
});

it('hides the approve action from assessors and on non-submitted reports', function (): void {
    actingAsAssessor();
    ['indicators' => $indicators, 'year' => $year] = assessmentFixture();
    $department = Department::factory()->create();

    Report::factory()->create([
        'indicator_id' => $indicators->first()->id,
        'department_id' => $department->id,
        'academic_year_id' => $year->id,
        'status' => 'submitted',
    ]);

    Livewire::test(ListReports::class)
        ->set('tableFilters.department_id.value', $department->id)
        ->assertTableActionHidden('approve', $indicators->first());
});

it('shows an empty state prompt when no academic year is resolved', function (): void {
    actingAsAssessor();
    // No academic year at all.

    Livewire::test(ListReports::class)
        ->assertSee('ເລືອກປີການສຶກສາ');
});

it('shows an empty state when the framework has no standards', function (): void {
    actingAsAssessor();
    $framework = QaFramework::factory()->create();
    AcademicYear::factory()->for($framework, 'framework')->create(['is_active' => true]);
    $department = Department::factory()->create();

    Livewire::test(ListReports::class)
        ->set('tableFilters.department_id.value', $department->id)
        ->assertSee('ຊຸດມາດຕະຖານຍັງບໍ່ມີໂຄງສ້າງ');
});
