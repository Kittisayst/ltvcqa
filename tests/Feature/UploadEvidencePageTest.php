<?php

use App\Filament\Pages\UploadEvidence;
use App\Models\AcademicYear;
use App\Models\BasisMain;
use App\Models\Department;
use App\Models\Document;
use App\Models\DocumentFile;
use App\Models\Indicator;
use App\Models\QaFramework;
use App\Models\Standard;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Livewire\Livewire;

use function Pest\Laravel\assertDatabaseCount;

it('is accessible to department-staff and super_admin, but not assessor', function (): void {
    actingAsDepartmentStaff();
    expect(UploadEvidence::canAccess())->toBeTrue();

    actingAsSuperAdmin();
    expect(UploadEvidence::canAccess())->toBeTrue();

    actingAsAssessor();
    expect(UploadEvidence::canAccess())->toBeFalse();
});

it('lists basis mains for the active academic year with their upload status', function (): void {
    $framework = QaFramework::factory()->create();
    $academicYear = AcademicYear::factory()->create(['framework_id' => $framework->id, 'is_active' => true]);
    $standard = Standard::factory()->create(['framework_id' => $framework->id]);
    $indicator = Indicator::factory()->create(['standard_id' => $standard->id]);
    $uploadedBasisMain = BasisMain::factory()->create(['indicator_id' => $indicator->id]);
    $missingBasisMain = BasisMain::factory()->create(['indicator_id' => $indicator->id]);

    $department = Department::factory()->create();
    $staff = actingAsDepartmentStaff($department);

    $document = Document::factory()->create([
        'user_id' => $staff->id,
        'basis_main_id' => $uploadedBasisMain->id,
        'academic_year_id' => $academicYear->id,
    ]);
    DocumentFile::factory()->create(['document_id' => $document->id]);

    Livewire::test(UploadEvidence::class)
        ->assertCanSeeTableRecords([$uploadedBasisMain, $missingBasisMain])
        ->assertTableColumnStateSet('status', 'ອັບໂຫຼດແລ້ວ', record: $uploadedBasisMain)
        ->assertTableColumnStateSet('status', 'ຍັງບໍ່ໄດ້ອັບໂຫຼດ', record: $missingBasisMain);
});

it('creates a new document and file on first upload for a basis main', function (): void {
    $framework = QaFramework::factory()->create();
    $academicYear = AcademicYear::factory()->create(['framework_id' => $framework->id, 'is_active' => true]);
    $standard = Standard::factory()->create(['framework_id' => $framework->id]);
    $indicator = Indicator::factory()->create(['standard_id' => $standard->id]);
    $basisMain = BasisMain::factory()->create(['indicator_id' => $indicator->id]);

    $department = Department::factory()->create();
    $staff = actingAsDepartmentStaff($department);

    Livewire::test(UploadEvidence::class)
        ->callTableAction('upload', $basisMain, data: [
            'reference_no' => '001/ຫລບ',
            'issued_date' => now()->toDateString(),
            'path' => UploadedFile::fake()->create('evidence.pdf', 500, 'application/pdf'),
        ])
        ->assertHasNoActionErrors();

    assertDatabaseCount(Document::class, 1);
    assertDatabaseCount(DocumentFile::class, 1);

    $document = Document::first();
    expect($document->user_id)->toBe($staff->id)
        ->and($document->basis_main_id)->toBe($basisMain->id)
        ->and($document->academic_year_id)->toBe($academicYear->id);
});

it('reuses the existing department document when a colleague already created one', function (): void {
    $framework = QaFramework::factory()->create();
    $academicYear = AcademicYear::factory()->create(['framework_id' => $framework->id, 'is_active' => true]);
    $standard = Standard::factory()->create(['framework_id' => $framework->id]);
    $indicator = Indicator::factory()->create(['standard_id' => $standard->id]);
    $basisMain = BasisMain::factory()->create(['indicator_id' => $indicator->id]);

    $department = Department::factory()->create();
    $colleague = User::factory()->for($department)->create();
    $existingDocument = Document::factory()->create([
        'user_id' => $colleague->id,
        'basis_main_id' => $basisMain->id,
        'academic_year_id' => $academicYear->id,
    ]);

    actingAsDepartmentStaff($department);

    Livewire::test(UploadEvidence::class)
        ->callTableAction('upload', $basisMain, data: [
            'reference_no' => '002/ຫລບ',
            'issued_date' => now()->toDateString(),
            'path' => UploadedFile::fake()->create('evidence.pdf', 500, 'application/pdf'),
        ])
        ->assertHasNoActionErrors();

    assertDatabaseCount(Document::class, 1);
    assertDatabaseCount(DocumentFile::class, 1);

    expect(DocumentFile::first()->document_id)->toBe($existingDocument->id);
});

it('shows the view-document action only once a document exists', function (): void {
    $framework = QaFramework::factory()->create();
    $academicYear = AcademicYear::factory()->create(['framework_id' => $framework->id, 'is_active' => true]);
    $standard = Standard::factory()->create(['framework_id' => $framework->id]);
    $indicator = Indicator::factory()->create(['standard_id' => $standard->id]);
    $basisMain = BasisMain::factory()->create(['indicator_id' => $indicator->id]);

    $department = Department::factory()->create();
    $staff = actingAsDepartmentStaff($department);

    Livewire::test(UploadEvidence::class)
        ->assertTableActionHidden('view', record: $basisMain);

    Document::factory()->create([
        'user_id' => $staff->id,
        'basis_main_id' => $basisMain->id,
        'academic_year_id' => $academicYear->id,
    ]);

    Livewire::test(UploadEvidence::class)
        ->assertTableActionVisible('view', record: $basisMain);
});

it('lets super_admin pick a department and attributes the document to a user in it', function (): void {
    $framework = QaFramework::factory()->create();
    $academicYear = AcademicYear::factory()->create(['framework_id' => $framework->id, 'is_active' => true]);
    $standard = Standard::factory()->create(['framework_id' => $framework->id]);
    $indicator = Indicator::factory()->create(['standard_id' => $standard->id]);
    $basisMain = BasisMain::factory()->create(['indicator_id' => $indicator->id]);

    $department = Department::factory()->create();
    $staffInDepartment = User::factory()->for($department)->create();

    actingAsSuperAdmin();

    Livewire::test(UploadEvidence::class)
        ->filterTable('department_id', $department->id)
        ->callTableAction('upload', $basisMain, data: [
            'reference_no' => '003/ຫລບ',
            'issued_date' => now()->toDateString(),
            'path' => UploadedFile::fake()->create('evidence.pdf', 500, 'application/pdf'),
        ])
        ->assertHasNoActionErrors();

    assertDatabaseCount(Document::class, 1);

    $document = Document::first();
    expect($document->user_id)->toBe($staffInDepartment->id)
        ->and($document->academic_year_id)->toBe($academicYear->id);
});

it('blocks super_admin from creating a document for a department with no users', function (): void {
    $framework = QaFramework::factory()->create();
    AcademicYear::factory()->create(['framework_id' => $framework->id, 'is_active' => true]);
    $standard = Standard::factory()->create(['framework_id' => $framework->id]);
    $indicator = Indicator::factory()->create(['standard_id' => $standard->id]);
    $basisMain = BasisMain::factory()->create(['indicator_id' => $indicator->id]);

    $emptyDepartment = Department::factory()->create();

    actingAsSuperAdmin();

    Livewire::test(UploadEvidence::class)
        ->filterTable('department_id', $emptyDepartment->id)
        ->callTableAction('upload', $basisMain, data: [
            'reference_no' => '004/ຫລບ',
            'issued_date' => now()->toDateString(),
            'path' => UploadedFile::fake()->create('evidence.pdf', 500, 'application/pdf'),
        ]);

    assertDatabaseCount(Document::class, 0);
});
