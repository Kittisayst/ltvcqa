<?php

use App\Filament\Resources\Documents\Pages\CreateDocument;
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

it('rejects a basis main that belongs to a different framework than the selected academic year', function (): void {
    $user = actingAsSuperAdmin();

    $academicYear = AcademicYear::factory()->create();
    $basisMainFromAnotherFramework = BasisMain::factory()->create();

    Livewire::test(CreateDocument::class)
        ->fillForm([
            'academic_year_id' => $academicYear->id,
            'user_id' => $user->id,
            'basis_main_id' => $basisMainFromAnotherFramework->id,
        ])
        ->call('create')
        ->assertHasFormErrors(['basis_main_id']);

    assertDatabaseCount(Document::class, 0);
});

it('accepts a basis main that belongs to the same framework as the selected academic year', function (): void {
    $user = actingAsSuperAdmin();

    $framework = QaFramework::factory()->create();
    $academicYear = AcademicYear::factory()->create(['framework_id' => $framework->id]);
    $standard = Standard::factory()->create(['framework_id' => $framework->id]);
    $indicator = Indicator::factory()->create(['standard_id' => $standard->id]);
    $basisMain = BasisMain::factory()->create(['indicator_id' => $indicator->id]);

    Livewire::test(CreateDocument::class)
        ->fillForm([
            'academic_year_id' => $academicYear->id,
            'user_id' => $user->id,
            'standard_id' => $standard->id,
            'indicator_id' => $indicator->id,
            'basis_main_id' => $basisMain->id,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    assertDatabaseCount(Document::class, 1);
});

it('forces the document user to the current user for non-super-admins regardless of submitted value', function (): void {
    $department = Department::factory()->create();
    $staff = actingAsDepartmentStaff($department);
    $otherUserInDepartment = User::factory()->for($department)->create();

    $framework = QaFramework::factory()->create();
    $academicYear = AcademicYear::factory()->create(['framework_id' => $framework->id]);
    $standard = Standard::factory()->create(['framework_id' => $framework->id]);
    $indicator = Indicator::factory()->create(['standard_id' => $standard->id]);
    $basisMain = BasisMain::factory()->create(['indicator_id' => $indicator->id]);

    Livewire::test(CreateDocument::class)
        ->fillForm([
            'academic_year_id' => $academicYear->id,
            'user_id' => $otherUserInDepartment->id,
            'standard_id' => $standard->id,
            'indicator_id' => $indicator->id,
            'basis_main_id' => $basisMain->id,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $document = Document::first();

    expect($document->user_id)->toBe($staff->id);
});
