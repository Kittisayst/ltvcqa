<?php

use App\Filament\Resources\Documents\Pages\EditDocument;
use App\Filament\Resources\Documents\RelationManagers\FilesRelationManager;
use App\Models\Document;
use App\Models\DocumentFile;
use Filament\Actions\CreateAction;
use Filament\Actions\Testing\TestAction;
use Illuminate\Http\UploadedFile;
use Livewire\Livewire;

use function Pest\Laravel\assertDatabaseCount;

it('auto-labels an uploaded image as ຮູບພາບ without requiring a reference number or issue date', function (): void {
    actingAsSuperAdmin();

    $document = Document::factory()->create();

    Livewire::test(FilesRelationManager::class, [
        'ownerRecord' => $document,
        'pageClass' => EditDocument::class,
    ])
        ->callAction(TestAction::make(CreateAction::class)->table(), data: [
            'path' => UploadedFile::fake()->image('photo.jpg'),
        ])
        ->assertHasNoActionErrors();

    assertDatabaseCount(DocumentFile::class, 1);

    $file = DocumentFile::first();

    expect($file->reference_no)->toBe('ຮູບພາບ')
        ->and($file->issued_date)->toBeNull();
});

it('requires a reference number for a non-image file when the has-reference-no toggle is on', function (): void {
    actingAsSuperAdmin();

    $document = Document::factory()->create();

    Livewire::test(FilesRelationManager::class, [
        'ownerRecord' => $document,
        'pageClass' => EditDocument::class,
    ])
        ->callAction(TestAction::make(CreateAction::class)->table(), data: [
            'has_reference_no' => true,
            'issued_date' => now()->toDateString(),
            'path' => UploadedFile::fake()->create('evidence.pdf', 500, 'application/pdf'),
        ])
        ->assertHasActionErrors(['reference_no']);

    assertDatabaseCount(DocumentFile::class, 0);
});

it('allows a non-image file with no reference number, as long as it still has an issue date', function (): void {
    actingAsSuperAdmin();

    $document = Document::factory()->create();

    Livewire::test(FilesRelationManager::class, [
        'ownerRecord' => $document,
        'pageClass' => EditDocument::class,
    ])
        ->callAction(TestAction::make(CreateAction::class)->table(), data: [
            'has_reference_no' => false,
            'issued_date' => now()->toDateString(),
            'path' => UploadedFile::fake()->create('evidence.pdf', 500, 'application/pdf'),
        ])
        ->assertHasNoActionErrors();

    assertDatabaseCount(DocumentFile::class, 1);

    $file = DocumentFile::first();

    expect($file->reference_no)->toBeNull()
        ->and($file->issued_date)->not->toBeNull();
});

it('still requires an issue date for a non-image file even without a reference number', function (): void {
    actingAsSuperAdmin();

    $document = Document::factory()->create();

    Livewire::test(FilesRelationManager::class, [
        'ownerRecord' => $document,
        'pageClass' => EditDocument::class,
    ])
        ->callAction(TestAction::make(CreateAction::class)->table(), data: [
            'has_reference_no' => false,
            'path' => UploadedFile::fake()->create('evidence.pdf', 500, 'application/pdf'),
        ])
        ->assertHasActionErrors(['issued_date']);

    assertDatabaseCount(DocumentFile::class, 0);
});
