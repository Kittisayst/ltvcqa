<?php

use App\Filament\Resources\Reports\Pages\CreateReport;
use App\Models\User;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;

it('preloads and only lists users with the assessor role in the assessor_id select', function (): void {
    actingAsSuperAdmin();

    $assessor = User::factory()->create();
    $assessor->assignRole(Role::findOrCreate('assessor', 'web'));

    $departmentStaff = User::factory()->create();

    $field = Livewire::test(CreateReport::class)
        ->instance()
        ->getSchema('form')
        ->getComponent('assessor_id');

    expect($field->isPreloaded())->toBeTrue();

    $options = $field->getOptions();

    expect($options)->toHaveKey($assessor->id)
        ->and($options)->not->toHaveKey($departmentStaff->id);
});
