<?php

use App\Filament\Resources\Users\Pages\EditUser;
use App\Models\User;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;

it('lets an admin assign a role to a user from the edit form', function (): void {
    actingAsSuperAdmin();

    $user = User::factory()->create();
    $role = Role::findOrCreate('assessor', 'web');

    Livewire::test(EditUser::class, ['record' => $user->getKey()])
        ->fillForm([
            'name' => $user->name,
            'username' => $user->username,
            'roles' => [$role->id],
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($user->fresh()->hasRole('assessor'))->toBeTrue();
});

it('lets an admin remove a role from a user', function (): void {
    actingAsSuperAdmin();

    $user = User::factory()->create();
    $role = Role::findOrCreate('assessor', 'web');
    $user->assignRole($role);

    Livewire::test(EditUser::class, ['record' => $user->getKey()])
        ->fillForm([
            'name' => $user->name,
            'username' => $user->username,
            'roles' => [],
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($user->fresh()->hasRole('assessor'))->toBeFalse();
});
