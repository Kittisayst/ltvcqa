<?php

use App\Filament\Resources\Documents\DocumentResource;
use App\Filament\Resources\Documents\Pages\ListDocuments;
use App\Filament\Resources\Reports\Pages\CreateReport;
use App\Filament\Resources\Reports\Pages\ListReports;
use App\Filament\Resources\Reports\ReportResource;
use App\Filament\Resources\Standards\StandardResource;
use App\Models\AcademicYear;
use App\Models\Department;
use App\Models\Document;
use App\Models\Indicator;
use App\Models\Report;
use App\Models\Standard;
use App\Models\User;
use Livewire\Livewire;

it('sees documents from every department in the list, read-only', function (): void {
    $department = Department::factory()->create();
    $staff = actingAsDepartmentStaff($department);

    $ownUser = User::factory()->for($department)->create();
    $otherUser = User::factory()->for(Department::factory())->create();

    $ownDocuments = Document::factory()->count(2)->for($ownUser, 'user')->create();
    $otherDocuments = Document::factory()->count(2)->for($otherUser, 'user')->create();

    Livewire::test(ListDocuments::class)
        ->assertCanSeeTableRecords($ownDocuments)
        ->assertCanSeeTableRecords($otherDocuments);
});

it('can view but not edit another department\'s document', function (): void {
    actingAsDepartmentStaff();

    $otherUser = User::factory()->for(Department::factory())->create();
    $document = Document::factory()->for($otherUser, 'user')->create();

    $this->get(DocumentResource::getUrl('view', ['record' => $document]))
        ->assertSuccessful();

    $this->get(DocumentResource::getUrl('edit', ['record' => $document]))
        ->assertForbidden();
});

it('is forbidden from the standards master-data resource', function (): void {
    actingAsDepartmentStaff();

    $this->get(StandardResource::getUrl('index'))
        ->assertForbidden();
});

it('forces the report department to its own department regardless of submitted value', function (): void {
    $department = Department::factory()->create();
    $staff = actingAsDepartmentStaff($department);
    $otherDepartment = Department::factory()->create();

    $standard = Standard::factory()->create();
    $indicator = Indicator::factory()->create(['standard_id' => $standard->id]);
    $academicYear = AcademicYear::factory()->create(['framework_id' => $standard->framework_id]);

    Livewire::test(CreateReport::class)
        ->fillForm([
            'academic_year_id' => $academicYear->id,
            'department_id' => $otherDepartment->id,
            'indicator_id' => $indicator->id,
            'status' => 'draft',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $report = Report::first();

    expect($report->department_id)->toBe($department->id);
});

it('sees reports from every department in the list, read-only', function (): void {
    $department = Department::factory()->create();
    actingAsDepartmentStaff($department);

    $ownReports = Report::factory()->count(2)->create(['department_id' => $department->id]);
    $otherReports = Report::factory()->count(2)->create();

    Livewire::test(ListReports::class)
        ->assertCanSeeTableRecords($ownReports)
        ->assertCanSeeTableRecords($otherReports);
});

it('can view but not edit another department\'s report', function (): void {
    actingAsDepartmentStaff();

    $report = Report::factory()->create();

    $this->get(ReportResource::getUrl('view', ['record' => $report]))
        ->assertSuccessful();

    $this->get(ReportResource::getUrl('edit', ['record' => $report]))
        ->assertForbidden();
});
