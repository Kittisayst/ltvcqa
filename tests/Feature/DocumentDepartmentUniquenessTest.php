<?php

use App\Filament\Resources\Documents\Pages\CreateDocument;
use App\Filament\Resources\Documents\Pages\EditDocument;
use App\Models\AcademicYear;
use App\Models\BasisMain;
use App\Models\Department;
use App\Models\Document;
use App\Models\Indicator;
use App\Models\QaFramework;
use App\Models\Standard;
use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\assertDatabaseCount;

it('rejects a second document for the same department, basis main, and academic year', function (): void {
    $framework = QaFramework::factory()->create();
    $academicYear = AcademicYear::factory()->create(['framework_id' => $framework->id]);
    $standard = Standard::factory()->create(['framework_id' => $framework->id]);
    $indicator = Indicator::factory()->create(['standard_id' => $standard->id]);
    $basisMain = BasisMain::factory()->create(['indicator_id' => $indicator->id]);

    $department = Department::factory()->create();
    $firstStaff = User::factory()->for($department)->create();
    $secondStaff = actingAsDepartmentStaff($department);

    Document::factory()->create([
        'user_id' => $firstStaff->id,
        'basis_main_id' => $basisMain->id,
        'academic_year_id' => $academicYear->id,
    ]);

    Livewire::test(CreateDocument::class)
        ->fillForm([
            'academic_year_id' => $academicYear->id,
            'user_id' => $secondStaff->id,
            'standard_id' => $standard->id,
            'indicator_id' => $indicator->id,
            'basis_main_id' => $basisMain->id,
        ])
        ->call('create')
        ->assertHasFormErrors(['basis_main_id']);

    assertDatabaseCount(Document::class, 1);
});

it('allows a different department to submit the same basis main for the same academic year', function (): void {
    $framework = QaFramework::factory()->create();
    $academicYear = AcademicYear::factory()->create(['framework_id' => $framework->id]);
    $standard = Standard::factory()->create(['framework_id' => $framework->id]);
    $indicator = Indicator::factory()->create(['standard_id' => $standard->id]);
    $basisMain = BasisMain::factory()->create(['indicator_id' => $indicator->id]);

    $otherDepartment = Department::factory()->create();
    Document::factory()->create([
        'user_id' => User::factory()->for($otherDepartment)->create()->id,
        'basis_main_id' => $basisMain->id,
        'academic_year_id' => $academicYear->id,
    ]);

    $staff = actingAsDepartmentStaff();

    Livewire::test(CreateDocument::class)
        ->fillForm([
            'academic_year_id' => $academicYear->id,
            'user_id' => $staff->id,
            'standard_id' => $standard->id,
            'indicator_id' => $indicator->id,
            'basis_main_id' => $basisMain->id,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    assertDatabaseCount(Document::class, 2);
});

it('allows re-saving the same document without triggering its own duplicate check', function (): void {
    $framework = QaFramework::factory()->create();
    $academicYear = AcademicYear::factory()->create(['framework_id' => $framework->id]);
    $standard = Standard::factory()->create(['framework_id' => $framework->id]);
    $indicator = Indicator::factory()->create(['standard_id' => $standard->id]);
    $basisMain = BasisMain::factory()->create(['indicator_id' => $indicator->id]);

    $department = Department::factory()->create();
    $staff = actingAsDepartmentStaff($department);

    $document = Document::factory()->create([
        'user_id' => $staff->id,
        'basis_main_id' => $basisMain->id,
        'academic_year_id' => $academicYear->id,
    ]);

    Livewire::test(EditDocument::class, ['record' => $document->id])
        ->fillForm([
            'academic_year_id' => $academicYear->id,
            'standard_id' => $standard->id,
            'indicator_id' => $indicator->id,
            'basis_main_id' => $basisMain->id,
        ])
        ->call('save')
        ->assertHasNoFormErrors();
});
