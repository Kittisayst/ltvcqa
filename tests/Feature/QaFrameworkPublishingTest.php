<?php

use App\Filament\Resources\QaFrameworks\Pages\CreateQaFramework;
use App\Filament\Resources\QaFrameworks\Pages\EditQaFramework;
use App\Filament\Resources\QaFrameworks\Pages\ListQaFrameworks;
use App\Models\AcademicYear;
use App\Models\QaFramework;
use Livewire\Livewire;

use function Pest\Laravel\assertDatabaseCount;

it('rejects publishing a second framework while one is already published', function (): void {
    actingAsSuperAdmin();

    QaFramework::factory()->create(['status' => 'published']);

    Livewire::test(CreateQaFramework::class)
        ->fillForm([
            'name' => 'ຊຸດມາດຕະຖານ 2026',
            'status' => 'published',
        ])
        ->call('create')
        ->assertHasFormErrors(['status']);

    assertDatabaseCount(QaFramework::class, 1);
});

it('allows creating a second framework as draft', function (): void {
    actingAsSuperAdmin();

    QaFramework::factory()->create(['status' => 'published']);

    Livewire::test(CreateQaFramework::class)
        ->fillForm([
            'name' => 'ຊຸດມາດຕະຖານ 2026',
            'status' => 'draft',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    assertDatabaseCount(QaFramework::class, 2);
});

it('rejects switching a draft framework to published while another is already published', function (): void {
    actingAsSuperAdmin();

    QaFramework::factory()->create(['status' => 'published']);
    $draft = QaFramework::factory()->draft()->create();

    Livewire::test(EditQaFramework::class, ['record' => $draft->getKey()])
        ->fillForm([
            'name' => $draft->name,
            'status' => 'published',
        ])
        ->call('save')
        ->assertHasFormErrors(['status']);

    expect($draft->fresh()->status)->toBe('draft');
});

it('allows re-saving the currently published framework as published', function (): void {
    actingAsSuperAdmin();

    $framework = QaFramework::factory()->create(['status' => 'published']);

    Livewire::test(EditQaFramework::class, ['record' => $framework->getKey()])
        ->fillForm([
            'name' => $framework->name,
            'status' => 'published',
        ])
        ->call('save')
        ->assertHasNoFormErrors();
});

it('rejects switching the published framework used by the active academic year back to draft', function (): void {
    actingAsSuperAdmin();

    $framework = QaFramework::factory()->create(['status' => 'published']);
    AcademicYear::factory()->create(['framework_id' => $framework->id, 'is_active' => true]);

    Livewire::test(EditQaFramework::class, ['record' => $framework->getKey()])
        ->fillForm([
            'name' => $framework->name,
            'status' => 'draft',
        ])
        ->call('save')
        ->assertHasFormErrors(['status']);

    expect($framework->fresh()->status)->toBe('published');
});

it('allows switching a published framework back to draft when no active academic year uses it', function (): void {
    actingAsSuperAdmin();

    $framework = QaFramework::factory()->create(['status' => 'published']);
    AcademicYear::factory()->create(['framework_id' => $framework->id, 'is_active' => false]);

    Livewire::test(EditQaFramework::class, ['record' => $framework->getKey()])
        ->fillForm([
            'name' => $framework->name,
            'status' => 'draft',
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($framework->fresh()->status)->toBe('draft');
});

it('rejects creating a framework with a name that already exists', function (): void {
    actingAsSuperAdmin();

    QaFramework::factory()->create(['name' => 'ຊຸດມາດຕະຖານ 2025']);

    Livewire::test(CreateQaFramework::class)
        ->fillForm([
            'name' => 'ຊຸດມາດຕະຖານ 2025',
            'status' => 'draft',
        ])
        ->call('create')
        ->assertHasFormErrors(['name']);

    assertDatabaseCount(QaFramework::class, 1);
});

it('rejects renaming a framework to a name that already exists', function (): void {
    actingAsSuperAdmin();

    QaFramework::factory()->create(['name' => 'ຊຸດມາດຕະຖານ 2025']);
    $other = QaFramework::factory()->draft()->create(['name' => 'ຊຸດມາດຕະຖານ 2026']);

    Livewire::test(EditQaFramework::class, ['record' => $other->getKey()])
        ->fillForm([
            'name' => 'ຊຸດມາດຕະຖານ 2025',
            'status' => 'draft',
        ])
        ->call('save')
        ->assertHasFormErrors(['name']);

    expect($other->fresh()->name)->toBe('ຊຸດມາດຕະຖານ 2026');
});

it('publishes a draft framework from the table action when none is currently published', function (): void {
    actingAsSuperAdmin();

    $draft = QaFramework::factory()->draft()->create();

    Livewire::test(ListQaFrameworks::class)
        ->callTableAction('publish', $draft);

    expect($draft->fresh()->status)->toBe('published');
});

it('publishing a framework from the table action drafts the previously published one and deactivates its academic year', function (): void {
    actingAsSuperAdmin();

    $oldFramework = QaFramework::factory()->create(['status' => 'published']);
    $oldYear = AcademicYear::factory()->for($oldFramework, 'framework')->create(['is_active' => true]);

    $newFramework = QaFramework::factory()->draft()->create();

    Livewire::test(ListQaFrameworks::class)
        ->callTableAction('publish', $newFramework);

    expect($newFramework->fresh()->status)->toBe('published')
        ->and($oldFramework->fresh()->status)->toBe('draft')
        ->and($oldYear->fresh()->is_active)->toBeFalse();
});

it('hides the publish table action for an already published framework', function (): void {
    actingAsSuperAdmin();

    $framework = QaFramework::factory()->create(['status' => 'published']);

    Livewire::test(ListQaFrameworks::class)
        ->assertTableActionHidden('publish', $framework);
});

it('allows re-saving a framework with its own unchanged name', function (): void {
    actingAsSuperAdmin();

    $framework = QaFramework::factory()->draft()->create(['name' => 'ຊຸດມາດຕະຖານ 2025']);

    Livewire::test(EditQaFramework::class, ['record' => $framework->getKey()])
        ->fillForm([
            'name' => 'ຊຸດມາດຕະຖານ 2025',
            'status' => 'draft',
        ])
        ->call('save')
        ->assertHasNoFormErrors();
});
