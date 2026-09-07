<?php

use App\Models\AcademicYear;
use App\Models\BasisMain;
use App\Models\Indicator;
use App\Models\QaFramework;
use App\Models\Standard;

beforeEach(fn () => AcademicYear::forgetActiveCache());

it('shows the active academic year, its framework, and the framework structure summary', function (): void {
    $framework = QaFramework::factory()->create(['name' => 'ຊຸດມາດຕະຖານ ກ.ຊ.ສ 2024']);
    AcademicYear::factory()->for($framework, 'framework')->create([
        'name' => '2024-2025',
        'is_active' => true,
    ]);

    $standardA = Standard::factory()->for($framework, 'framework')->create(['name' => 'ການບໍລິຫານຈັດການ', 'order' => 1]);
    $standardB = Standard::factory()->for($framework, 'framework')->create(['name' => 'ຫຼັກສູດ ແລະ ການຮຽນການສອນ', 'order' => 2]);

    $indicatorA = Indicator::factory()->for($standardA)->create();
    $indicatorB = Indicator::factory()->for($standardB)->create();
    $indicatorC = Indicator::factory()->for($standardB)->create();

    BasisMain::factory()->for($indicatorA)->count(2)->create();
    BasisMain::factory()->for($indicatorB)->create();
    BasisMain::factory()->for($indicatorC)->count(3)->create();

    $this->get('/')
        ->assertOk()
        ->assertSee('ວິທະຍາໄລ ເຕັກນິກ-ວິຊາຊີບ ຫຼວງພະບາງ')
        ->assertSee('images/ltvc_logo.png') // logo + favicon
        ->assertSee('ສົກຮຽນ 2024-2025')
        ->assertSee('ຊຸດມາດຕະຖານ ກ.ຊ.ສ 2024')
        ->assertSee('ກຳລັງໃຊ້ງານ')
        // per-standard counts come from the Standard::basisMains() has-many-through
        ->assertSeeText('1 ຕົວຊີ້ວັດ')
        ->assertSeeText('2 ຫຼັກຖານ')
        ->assertSeeText('ການບໍລິຫານຈັດການ')
        ->assertSeeText('2 ຕົວຊີ້ວັດ')
        ->assertSeeText('4 ຫຼັກຖານ')
        ->assertSeeText('ຫຼັກສູດ ແລະ ການຮຽນການສອນ')
        // totals row: 2 standards / 3 indicators / 6 basis mains
        ->assertSeeTextInOrder(['2', 'ມາດຕະຖານ', '3', 'ຕົວຊີ້ວັດ', '6', 'ຫຼັກຖານ', 'ລາຍການມາດຕະຖານ'])
        ->assertSee(route('filament.admin.auth.login'));
});

it('shows a draft framework badge when the active year still uses a draft framework', function (): void {
    $framework = QaFramework::factory()->draft()->create();
    AcademicYear::factory()->for($framework, 'framework')->create(['is_active' => true]);

    $this->get('/')
        ->assertOk()
        ->assertSee('ຮ່າງ')
        ->assertDontSee('ກຳລັງໃຊ້ງານ');
});

it('falls back to a login prompt when no academic year is active', function (): void {
    $this->get('/')
        ->assertOk()
        ->assertSee('ວິທະຍາໄລ ເຕັກນິກ-ວິຊາຊີບ ຫຼວງພະບາງ')
        ->assertSee('ຍັງບໍ່ໄດ້ກຳນົດສົກຮຽນທີ່ໃຊ້ງານ')
        ->assertSee(route('filament.admin.auth.login'));
});
