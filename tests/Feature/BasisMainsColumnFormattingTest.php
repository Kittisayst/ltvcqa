<?php

use App\Filament\Resources\BasisMains\Pages\ListBasisMains;
use App\Models\BasisMain;
use App\Models\Indicator;
use App\Models\Standard;
use Livewire\Livewire;

it('prefixes the standard and indicator columns with a label and their order', function (): void {
    actingAsSuperAdmin();

    $standard = Standard::factory()->create(['name' => 'ວິໄສທັດ', 'order' => 2]);
    $indicator = Indicator::factory()->for($standard)->create(['name' => 'ຕົວຊີ້ວັດຫຼັກ', 'order' => 3]);
    BasisMain::factory()->for($indicator)->create();

    Livewire::test(ListBasisMains::class)
        ->assertSee('ມາດຕະຖານ 2. ວິໄສທັດ')
        ->assertSee('ຕົວຊີ້ວັດ 3. ຕົວຊີ້ວັດຫຼັກ');
});
