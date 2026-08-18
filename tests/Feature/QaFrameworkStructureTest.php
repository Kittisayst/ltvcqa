<?php

use App\Filament\Resources\QaFrameworks\Pages\ManageStructure;
use App\Models\BasisMain;
use App\Models\Document;
use App\Models\Indicator;
use App\Models\QaFramework;
use App\Models\Standard;
use Livewire\Livewire;

use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertModelExists;

it('loads the structure page for a framework', function (): void {
    actingAsSuperAdmin();

    $framework = QaFramework::factory()->create();
    $standard = Standard::factory()->for($framework, 'framework')->create(['name' => 'ມາດຕະຖານ 1']);
    Indicator::factory()->for($standard)->create(['name' => 'ຕົວຊີ້ວັດ 1']);

    Livewire::test(ManageStructure::class, ['record' => $framework->getKey()])
        ->assertOk()
        ->assertSchemaStateSet([
            "standards.record-{$standard->id}.name" => 'ມາດຕະຖານ 1',
        ]);
});

it('adds a new standard with a nested indicator and basis main', function (): void {
    actingAsSuperAdmin();

    $framework = QaFramework::factory()->create();

    Livewire::test(ManageStructure::class, ['record' => $framework->getKey()])
        ->fillForm([
            'standards' => [
                'new-standard' => [
                    'name' => 'ມາດຕະຖານໃໝ່',
                    'indicators' => [
                        'new-indicator' => [
                            'name' => 'ຕົວຊີ້ວັດໃໝ່',
                            'basisMains' => [
                                'new-basis-main' => [
                                    'title' => 'ຫຼັກຖານໃໝ່',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    assertDatabaseHas(Standard::class, ['framework_id' => $framework->id, 'name' => 'ມາດຕະຖານໃໝ່']);

    $standard = Standard::where('framework_id', $framework->id)->sole();
    $indicator = $standard->indicators->sole();
    $basisMain = $indicator->basisMains->sole();

    expect($indicator->name)->toBe('ຕົວຊີ້ວັດໃໝ່')
        ->and($basisMain->title)->toBe('ຫຼັກຖານໃໝ່');
});

it('renames an existing standard', function (): void {
    actingAsSuperAdmin();

    $framework = QaFramework::factory()->create();
    $standard = Standard::factory()->for($framework, 'framework')->create(['name' => 'ຊື່ເກົ່າ']);

    Livewire::test(ManageStructure::class, ['record' => $framework->getKey()])
        ->fillForm([
            'standards' => [
                "record-{$standard->id}" => ['name' => 'ຊື່ໃໝ່', 'indicators' => []],
            ],
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($standard->fresh()->name)->toBe('ຊື່ໃໝ່');
});

it('deletes a basis main that has no documents', function (): void {
    actingAsSuperAdmin();

    $framework = QaFramework::factory()->create();
    $standard = Standard::factory()->for($framework, 'framework')->create();
    $indicator = Indicator::factory()->for($standard)->create();
    $basisMain = BasisMain::factory()->for($indicator)->create();

    Livewire::test(ManageStructure::class, ['record' => $framework->getKey()])
        ->fillForm([
            'standards' => [
                "record-{$standard->id}" => [
                    'name' => $standard->name,
                    'indicators' => [
                        "record-{$indicator->id}" => [
                            'name' => $indicator->name,
                            'basisMains' => [],
                        ],
                    ],
                ],
            ],
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    assertDatabaseCount(BasisMain::class, 0);
});

it('refuses to delete a basis main that already has a document, and notifies instead of crashing', function (): void {
    actingAsSuperAdmin();

    $framework = QaFramework::factory()->create();
    $standard = Standard::factory()->for($framework, 'framework')->create();
    $indicator = Indicator::factory()->for($standard)->create();
    $basisMain = BasisMain::factory()->for($indicator)->create();
    Document::factory()->for($basisMain)->create();

    Livewire::test(ManageStructure::class, ['record' => $framework->getKey()])
        ->fillForm([
            'standards' => [
                "record-{$standard->id}" => [
                    'name' => $standard->name,
                    'indicators' => [
                        "record-{$indicator->id}" => [
                            'name' => $indicator->name,
                            'basisMains' => [],
                        ],
                    ],
                ],
            ],
        ])
        ->call('save')
        ->assertNotified();

    assertModelExists($basisMain);
});
