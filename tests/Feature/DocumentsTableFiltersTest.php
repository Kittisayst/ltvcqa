<?php

use App\Filament\Resources\Documents\Pages\ListDocuments;
use App\Models\BasisMain;
use App\Models\Document;
use App\Models\Indicator;
use App\Models\Standard;
use Livewire\Livewire;

it('filters documents by standard', function (): void {
    actingAsSuperAdmin();

    $standard = Standard::factory()->create();
    $indicator = Indicator::factory()->for($standard)->create();
    $basisMain = BasisMain::factory()->for($indicator)->create();
    $matching = Document::factory()->for($basisMain)->create();

    $other = Document::factory()->create();

    Livewire::test(ListDocuments::class)
        ->filterTable('standard_id', $standard->id)
        ->assertCanSeeTableRecords([$matching])
        ->assertCanNotSeeTableRecords([$other]);
});

it('filters documents by indicator', function (): void {
    actingAsSuperAdmin();

    $indicator = Indicator::factory()->create();
    $basisMain = BasisMain::factory()->for($indicator)->create();
    $matching = Document::factory()->for($basisMain)->create();

    $other = Document::factory()->create();

    Livewire::test(ListDocuments::class)
        ->filterTable('indicator_id', $indicator->id)
        ->assertCanSeeTableRecords([$matching])
        ->assertCanNotSeeTableRecords([$other]);
});

it('filters documents by basis main', function (): void {
    actingAsSuperAdmin();

    $basisMain = BasisMain::factory()->create();
    $matching = Document::factory()->for($basisMain)->create();

    $other = Document::factory()->create();

    Livewire::test(ListDocuments::class)
        ->filterTable('basis_main_id', $basisMain->id)
        ->assertCanSeeTableRecords([$matching])
        ->assertCanNotSeeTableRecords([$other]);
});
