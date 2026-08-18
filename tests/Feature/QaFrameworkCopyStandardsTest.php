<?php

use App\Filament\Resources\QaFrameworks\Pages\ListQaFrameworks;
use App\Filament\Resources\Standards\Pages\ManageStandards;
use App\Models\BasisMain;
use App\Models\Indicator;
use App\Models\QaFramework;
use App\Models\Standard;
use Filament\Actions\CreateAction;
use Filament\Actions\Testing\TestAction;
use Livewire\Livewire;

use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertDatabaseHas;

it('copies a standard with its indicators and basis mains into the target draft framework', function (): void {
    actingAsSuperAdmin();

    $source = QaFramework::factory()->create(['status' => 'published']);
    $standard = Standard::factory()->for($source, 'framework')->create(['name' => 'ມາດຕະຖານ 1']);
    $indicator = Indicator::factory()->for($standard)->create(['name' => 'ຕົວຊີ້ວັດ 1', 'order' => 1]);
    BasisMain::factory()->for($indicator)->create([
        'title' => 'ຫຼັກຖານ 1',
        'order' => 1,
    ]);

    $target = QaFramework::factory()->draft()->create();

    Livewire::test(ListQaFrameworks::class)
        ->callAction(TestAction::make('copyFrom')->table($target), data: [
            'source_framework_id' => $source->id,
            'standard_ids' => [$standard->id],
        ])
        ->assertHasNoActionErrors();

    assertDatabaseCount(Standard::class, 2);
    assertDatabaseHas(Standard::class, [
        'framework_id' => $target->id,
        'name' => 'ມາດຕະຖານ 1',
    ]);

    $copiedStandard = Standard::where('framework_id', $target->id)->sole();

    expect($copiedStandard->id)->not->toBe($standard->id)
        ->and($copiedStandard->indicators)->toHaveCount(1)
        ->and($copiedStandard->indicators->first()->name)->toBe('ຕົວຊີ້ວັດ 1')
        ->and($copiedStandard->indicators->first()->basisMains)->toHaveCount(1)
        ->and($copiedStandard->indicators->first()->basisMains->first()->title)->toBe('ຫຼັກຖານ 1');

    // Source untouched.
    expect($standard->fresh()->framework_id)->toBe($source->id);
});

it('hides the copy action for a published target framework', function (): void {
    actingAsSuperAdmin();

    QaFramework::factory()->create(['status' => 'published']);
    $target = QaFramework::factory()->create(['status' => 'published']);

    Livewire::test(ListQaFrameworks::class)
        ->assertTableActionHidden('copyFrom', $target);
});

it('rejects a submitted standard whose name already exists in the target framework, since it is excluded from the options', function (): void {
    actingAsSuperAdmin();

    $source = QaFramework::factory()->create(['status' => 'published']);
    $duplicate = Standard::factory()->for($source, 'framework')->create(['name' => 'ມາດຕະຖານ ຊ້ຳ']);

    $target = QaFramework::factory()->draft()->create();
    Standard::factory()->for($target, 'framework')->create(['name' => 'ມາດຕະຖານ ຊ້ຳ']);

    Livewire::test(ListQaFrameworks::class)
        ->callAction(TestAction::make('copyFrom')->table($target), data: [
            'source_framework_id' => $source->id,
            'standard_ids' => [$duplicate->id],
        ])
        ->assertHasActionErrors(['standard_ids.0']);

    // Still just the one pre-existing "ມາດຕະຖານ ຊ້ຳ" in the target framework.
    assertDatabaseCount(Standard::class, 2);
});

it('copies only the selected standards that are still eligible when multiple are submitted', function (): void {
    actingAsSuperAdmin();

    $source = QaFramework::factory()->create(['status' => 'published']);
    Standard::factory()->for($source, 'framework')->create(['name' => 'ມາດຕະຖານ ຊ້ຳ']);
    $unique = Standard::factory()->for($source, 'framework')->create(['name' => 'ມາດຕະຖານ ໃໝ່']);

    $target = QaFramework::factory()->draft()->create();
    Standard::factory()->for($target, 'framework')->create(['name' => 'ມາດຕະຖານ ຊ້ຳ']);

    Livewire::test(ListQaFrameworks::class)
        ->callAction(TestAction::make('copyFrom')->table($target), data: [
            'source_framework_id' => $source->id,
            'standard_ids' => [$unique->id],
        ])
        ->assertHasNoActionErrors();

    assertDatabaseCount(Standard::class, 4);
    expect(Standard::where('framework_id', $target->id)->where('name', 'ມາດຕະຖານ ໃໝ່')->exists())->toBeTrue();
});

it('rejects creating a standard with a name that already exists in the same framework', function (): void {
    actingAsSuperAdmin();

    $framework = QaFramework::factory()->create();
    Standard::factory()->for($framework, 'framework')->create(['name' => 'ມາດຕະຖານ 1']);

    Livewire::test(ManageStandards::class)
        ->callAction(TestAction::make(CreateAction::class), data: [
            'framework_id' => $framework->id,
            'name' => 'ມາດຕະຖານ 1',
        ])
        ->assertHasActionErrors(['name']);

    assertDatabaseCount(Standard::class, 1);
});

it('allows the same standard name across different frameworks', function (): void {
    actingAsSuperAdmin();

    $frameworkA = QaFramework::factory()->create();
    $frameworkB = QaFramework::factory()->draft()->create();
    Standard::factory()->for($frameworkA, 'framework')->create(['name' => 'ມາດຕະຖານ 1']);

    Livewire::test(ManageStandards::class)
        ->callAction(TestAction::make(CreateAction::class), data: [
            'framework_id' => $frameworkB->id,
            'name' => 'ມາດຕະຖານ 1',
        ])
        ->assertHasNoActionErrors();

    assertDatabaseCount(Standard::class, 2);
});
