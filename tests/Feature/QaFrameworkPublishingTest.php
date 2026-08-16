<?php

use App\Filament\Resources\QaFrameworks\Pages\ManageQaFrameworks;
use App\Models\QaFramework;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\Testing\TestAction;
use Livewire\Livewire;

use function Pest\Laravel\assertDatabaseCount;

it('rejects publishing a second framework while one is already published', function (): void {
    actingAsSuperAdmin();

    QaFramework::factory()->create(['status' => 'published']);

    Livewire::test(ManageQaFrameworks::class)
        ->callAction(TestAction::make(CreateAction::class), data: [
            'name' => 'ຊຸດມາດຕະຖານ 2026',
            'status' => 'published',
        ])
        ->assertHasActionErrors(['status']);

    assertDatabaseCount(QaFramework::class, 1);
});

it('allows creating a second framework as draft', function (): void {
    actingAsSuperAdmin();

    QaFramework::factory()->create(['status' => 'published']);

    Livewire::test(ManageQaFrameworks::class)
        ->callAction(TestAction::make(CreateAction::class), data: [
            'name' => 'ຊຸດມາດຕະຖານ 2026',
            'status' => 'draft',
        ])
        ->assertHasNoActionErrors();

    assertDatabaseCount(QaFramework::class, 2);
});

it('rejects switching a draft framework to published while another is already published', function (): void {
    actingAsSuperAdmin();

    QaFramework::factory()->create(['status' => 'published']);
    $draft = QaFramework::factory()->draft()->create();

    Livewire::test(ManageQaFrameworks::class)
        ->callAction(TestAction::make(EditAction::class)->table($draft), data: [
            'name' => $draft->name,
            'status' => 'published',
        ])
        ->assertHasActionErrors(['status']);

    expect($draft->fresh()->status)->toBe('draft');
});

it('allows re-saving the currently published framework as published', function (): void {
    actingAsSuperAdmin();

    $framework = QaFramework::factory()->create(['status' => 'published']);

    Livewire::test(ManageQaFrameworks::class)
        ->callAction(TestAction::make(EditAction::class)->table($framework), data: [
            'name' => $framework->name,
            'status' => 'published',
        ])
        ->assertHasNoActionErrors();
});
