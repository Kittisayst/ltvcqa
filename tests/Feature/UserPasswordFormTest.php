<?php

use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;

it('requires a matching password confirmation when creating a user', function (): void {
    actingAsSuperAdmin();

    Livewire::test(CreateUser::class)
        ->fillForm([
            'name' => 'ນາງ ວິໄລ',
            'username' => 'wilai',
            'password' => 'password',
            'password_confirmation' => 'not-the-same',
        ])
        ->call('create')
        ->assertHasFormErrors(['password' => 'confirmed']);
});

it('creates a user with a hashed password when confirmation matches', function (): void {
    actingAsSuperAdmin();

    Livewire::test(CreateUser::class)
        ->fillForm([
            'name' => 'ນາງ ວິໄລ',
            'username' => 'wilai',
            'password' => 'password',
            'password_confirmation' => 'password',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    expect(Hash::check('password', User::where('username', 'wilai')->firstOrFail()->password))->toBeTrue();
});

it('keeps the existing password when the field is left blank on edit', function (): void {
    actingAsSuperAdmin();

    $user = User::factory()->create(['password' => Hash::make('original-password')]);
    $originalHash = $user->password;

    Livewire::test(EditUser::class, ['record' => $user->getKey()])
        ->fillForm([
            'name' => $user->name,
            'username' => $user->username,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($user->fresh()->password)->toBe($originalHash);
});

it('updates the password on edit when a new confirmed password is provided', function (): void {
    actingAsSuperAdmin();

    $user = User::factory()->create(['password' => Hash::make('original-password')]);

    Livewire::test(EditUser::class, ['record' => $user->getKey()])
        ->fillForm([
            'name' => $user->name,
            'username' => $user->username,
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    expect(Hash::check('new-password', $user->fresh()->password))->toBeTrue();
});
