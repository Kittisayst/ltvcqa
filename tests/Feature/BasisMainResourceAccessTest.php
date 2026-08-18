<?php

use App\Filament\Resources\BasisMains\Pages\ListBasisMains;
use App\Models\BasisMain;
use Livewire\Livewire;

it('allows super_admin to view the basis mains list and see records', function (): void {
    actingAsSuperAdmin();

    $basisMain = BasisMain::factory()->create();

    Livewire::test(ListBasisMains::class)
        ->assertSuccessful()
        ->assertCanSeeTableRecords([$basisMain]);
});

it('super_admin has the ViewAny:BasisMain permission required for the navigation item to appear', function (): void {
    actingAsSuperAdmin();

    expect(auth()->user()->can('viewAny', BasisMain::class))->toBeTrue();
});
