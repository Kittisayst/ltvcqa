<?php

use App\Filament\Resources\Documents\DocumentResource;
use App\Filament\Resources\Documents\Pages\ListDocuments;
use App\Filament\Resources\Reports\Pages\EditReport;
use App\Filament\Resources\Reports\Pages\ListReports;
use App\Filament\Resources\Reports\ReportResource;
use App\Filament\Resources\Standards\StandardResource;
use App\Models\AcademicYear;
use App\Models\Department;
use App\Models\Document;
use App\Models\Indicator;
use App\Models\QaFramework;
use App\Models\Report;
use App\Models\Standard;
use App\Models\User;
use Livewire\Livewire;

it('sees documents from every department in the list, read-only', function (): void {
    $department = Department::factory()->create();
    $staff = actingAsDepartmentStaff($department);

    $ownUser = User::factory()->for($department)->create();
    $otherUser = User::factory()->for(Department::factory())->create();

    // The list defaults its academic year filter to the active year, so
    // every document here must share one to all show up by default.
    $academicYear = AcademicYear::factory()->create(['is_active' => true]);

    $ownDocuments = Document::factory()->count(2)->for($ownUser, 'user')->for($academicYear, 'academicYear')->create();
    $otherDocuments = Document::factory()->count(2)->for($otherUser, 'user')->for($academicYear, 'academicYear')->create();

    Livewire::test(ListDocuments::class)
        ->assertCanSeeTableRecords($ownDocuments)
        ->assertCanSeeTableRecords($otherDocuments);
});

it('can view another department\'s document', function (): void {
    actingAsDepartmentStaff();

    $otherUser = User::factory()->for(Department::factory())->create();
    $document = Document::factory()->for($otherUser, 'user')->create();

    $this->get(DocumentResource::getUrl('view', ['record' => $document]))
        ->assertSuccessful();
});

it('is forbidden from the standards master-data resource', function (): void {
    actingAsDepartmentStaff();

    $this->get(StandardResource::getUrl('index'))
        ->assertForbidden();
});

it('forces the report department back to its own when editing its own report', function (): void {
    $department = Department::factory()->create();
    actingAsDepartmentStaff($department);
    $otherDepartment = Department::factory()->create();

    $framework = QaFramework::factory()->create();
    $year = AcademicYear::factory()->create(['framework_id' => $framework->id]);
    $standard = Standard::factory()->create(['framework_id' => $framework->id]);
    $indicator = Indicator::factory()->create(['standard_id' => $standard->id]);

    $report = Report::factory()->create([
        'department_id' => $department->id,
        'academic_year_id' => $year->id,
        'indicator_id' => $indicator->id,
        'status' => 'draft',
    ]);

    Livewire::test(EditReport::class, ['record' => $report->id])
        ->fillForm(['department_id' => $otherDepartment->id])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($report->fresh()->department_id)->toBe($department->id);
});

it('scopes the assessment console to the department-staff user\'s own department', function (): void {
    AcademicYear::forgetActiveCache();

    $department = Department::factory()->create();
    actingAsDepartmentStaff($department);

    $framework = QaFramework::factory()->create();
    $year = AcademicYear::factory()->for($framework, 'framework')->create(['is_active' => true]);
    $standard = Standard::factory()->for($framework, 'framework')->create();
    $indicator = Indicator::factory()->for($standard)->create();

    // A report exists for another department only.
    Report::factory()->create([
        'indicator_id' => $indicator->id,
        'academic_year_id' => $year->id,
        'status' => 'approved',
    ]);

    // The console is locked to the staff member's own department, so the
    // indicator reads as not-assessed — the other department's Report does
    // not leak into this view.
    Livewire::test(ListReports::class)
        ->assertCanSeeTableRecords([$indicator])
        ->assertSee('ຍັງບໍ່ໄດ້ປະເມີນ');

    expect(Report::where('department_id', $department->id)->exists())->toBeFalse();
});

it('can view but not edit another department\'s report', function (): void {
    actingAsDepartmentStaff();

    $report = Report::factory()->create();

    $this->get(ReportResource::getUrl('view', ['record' => $report]))
        ->assertSuccessful();

    $this->get(ReportResource::getUrl('edit', ['record' => $report]))
        ->assertForbidden();
});
