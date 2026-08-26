<?php

use App\Models\Indicator;
use App\Models\Standard;

it('builds the ordered name for a standard from its order and name', function (): void {
    $standard = Standard::factory()->make(['order' => 2, 'name' => 'ວິໄສທັດ']);

    expect($standard->ordered_name)->toBe('2. ວິໄສທັດ');
});

it('builds the ordered name for an indicator from its order and name', function (): void {
    $indicator = Indicator::factory()->make(['order' => 3, 'name' => 'ຕົວຊີ້ວັດຫຼັກ']);

    expect($indicator->ordered_name)->toBe('3. ຕົວຊີ້ວັດຫຼັກ');
});
