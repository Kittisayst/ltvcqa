<?php

use App\Filament\Widgets\LatestDocuments;
use App\Models\Document;
use Livewire\Livewire;

it('shows only the 10 most recently created documents', function (): void {
    actingAsSuperAdmin();

    $older = Document::factory()->count(5)->create(['created_at' => now()->subDays(2)]);
    $newest = Document::factory()->count(10)->create(['created_at' => now()]);

    Livewire::test(LatestDocuments::class)
        ->assertCanSeeTableRecords($newest)
        ->assertCanNotSeeTableRecords($older);
});
