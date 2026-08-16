<?php

use App\Filament\Resources\Documents\Pages\EditDocument;
use App\Filament\Resources\Documents\RelationManagers\FilesRelationManager;
use App\Models\Department;
use App\Models\Document;
use App\Models\DocumentFile;
use App\Models\User;
use Filament\Actions\CreateAction;
use Filament\Actions\Testing\TestAction;
use Illuminate\Http\UploadedFile;
use Livewire\Livewire;

use function Pest\Laravel\assertDatabaseCount;

it('rejects a disallowed file type', function (): void {
    actingAsSuperAdmin();

    $document = Document::factory()->create();

    Livewire::test(FilesRelationManager::class, [
        'ownerRecord' => $document,
        'pageClass' => EditDocument::class,
    ])
        ->callAction(TestAction::make(CreateAction::class)->table(), data: [
            'reference_no' => '001/ຫລບ',
            'issued_date' => now()->toDateString(),
            'path' => UploadedFile::fake()->create('evidence.exe', 100, 'application/x-msdownload'),
        ])
        ->assertHasActionErrors(['path']);

    assertDatabaseCount(DocumentFile::class, 0);
});

it('rejects a file larger than the configured limit', function (): void {
    actingAsSuperAdmin();

    $document = Document::factory()->create();

    Livewire::test(FilesRelationManager::class, [
        'ownerRecord' => $document,
        'pageClass' => EditDocument::class,
    ])
        ->callAction(TestAction::make(CreateAction::class)->table(), data: [
            'reference_no' => '001/ຫລບ',
            'issued_date' => now()->toDateString(),
            'path' => UploadedFile::fake()->create('too-big.pdf', 30_000, 'application/pdf'),
        ])
        ->assertHasActionErrors(['path']);

    assertDatabaseCount(DocumentFile::class, 0);
});

it('requires a reference number and issue date for every file', function (): void {
    actingAsSuperAdmin();

    $document = Document::factory()->create();

    Livewire::test(FilesRelationManager::class, [
        'ownerRecord' => $document,
        'pageClass' => EditDocument::class,
    ])
        ->callAction(TestAction::make(CreateAction::class)->table(), data: [
            'path' => UploadedFile::fake()->create('evidence.pdf', 500, 'application/pdf'),
        ])
        ->assertHasActionErrors(['reference_no', 'issued_date']);

    assertDatabaseCount(DocumentFile::class, 0);
});

it('accepts a valid pdf within the size limit and captures its metadata', function (): void {
    actingAsSuperAdmin();

    $document = Document::factory()->create();

    Livewire::test(FilesRelationManager::class, [
        'ownerRecord' => $document,
        'pageClass' => EditDocument::class,
    ])
        ->callAction(TestAction::make(CreateAction::class)->table(), data: [
            'reference_no' => '001/ຫລບ',
            'issued_date' => now()->toDateString(),
            'path' => UploadedFile::fake()->create('evidence.pdf', 500, 'application/pdf'),
        ])
        ->assertHasNoActionErrors();

    assertDatabaseCount(DocumentFile::class, 1);

    $file = DocumentFile::first();

    // Fake uploads don't carry real PDF bytes, so the mime type gets sniffed
    // generically rather than reported as application/pdf — the metadata
    // capture itself (not exact sniffing) is what this test verifies.
    expect($file->document_id)->toBe($document->id)
        ->and($file->reference_no)->toBe('001/ຫລບ')
        ->and($file->original_name)->toBe('evidence.pdf')
        ->and($file->mime_type)->not->toBeEmpty()
        ->and($file->size)->toBeGreaterThan(0);
});

it('hides upload and delete for a department-staff viewing another department\'s document', function (): void {
    actingAsDepartmentStaff();

    $otherUser = User::factory()->for(Department::factory())->create();
    $document = Document::factory()->for($otherUser, 'user')->create();
    $file = DocumentFile::factory()->create(['document_id' => $document->id]);

    Livewire::test(FilesRelationManager::class, [
        'ownerRecord' => $document,
        'pageClass' => EditDocument::class,
    ])
        ->assertActionHidden(TestAction::make(CreateAction::class)->table())
        ->assertTableActionHidden('delete', record: $file);
});

it('keeps upload and delete visible for a department-staff viewing its own document', function (): void {
    $staff = actingAsDepartmentStaff();

    $document = Document::factory()->for($staff, 'user')->create();
    $file = DocumentFile::factory()->create(['document_id' => $document->id]);

    Livewire::test(FilesRelationManager::class, [
        'ownerRecord' => $document,
        'pageClass' => EditDocument::class,
    ])
        ->assertActionVisible(TestAction::make(CreateAction::class)->table())
        ->assertTableActionVisible('delete', record: $file);
});
