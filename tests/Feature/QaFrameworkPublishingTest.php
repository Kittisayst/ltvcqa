<?php

use App\Filament\Resources\QaFrameworks\Pages\CreateQaFramework;
use App\Filament\Resources\QaFrameworks\Pages\EditQaFramework;
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
