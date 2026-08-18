<?php

use App\Models\Department;
use App\Models\User;
use Filament\Widgets\ChartWidget;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "pest()" function to bind different classes or traits.
|
*/

pest()->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->in('Feature');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

/**
 * ChartWidget::getData() is protected (Filament calls it internally when
 * rendering) — reflection is the only way to assert on it directly in a
 * test without rendering the actual chart.js output.
 *
 * @param  class-string<ChartWidget>  $widget
 * @return array<string, mixed>
 */
function getChartData(string $widget): array
{
    $instance = Livewire::test($widget)->instance();

    $method = new ReflectionMethod($instance, 'getData');
    $method->setAccessible(true);

    return $method->invoke($instance);
}

function grantPermissionsTo(Role $role, array $names): void
{
    $role->givePermissionTo(
        collect($names)->map(fn (string $name) => Permission::findOrCreate($name, 'web'))
    );
}

/**
 * All permissions across every policy-backed resource this app has —
 * mirrors what `shield:generate --all` would sync onto `super_admin`.
 */
function actingAsSuperAdmin(): User
{
    $user = User::factory()->create();

    $role = Role::findOrCreate('super_admin', 'web');

    $models = ['Document', 'Report', 'Standard', 'Indicator', 'BasisMain', 'Department', 'AcademicYear', 'QaFramework', 'User', 'Role'];
    $abilities = ['ViewAny', 'View', 'Create', 'Update', 'Delete', 'DeleteAny', 'Restore', 'RestoreAny', 'ForceDelete', 'ForceDeleteAny', 'Replicate', 'Reorder'];

    grantPermissionsTo($role, collect($models)
        ->flatMap(fn (string $model) => collect($abilities)->map(fn (string $ability) => "{$ability}:{$model}"))
        ->all());

    $user->assignRole($role);

    test()->actingAs($user);

    return $user;
}

/**
 * Mirrors the `department-staff` role defined in RolesAndPermissionsSeeder:
 * Document + Report only, no destructive actions.
 */
function actingAsDepartmentStaff(?Department $department = null): User
{
    $department ??= Department::factory()->create();

    $user = User::factory()->for($department)->create();

    $role = Role::findOrCreate('department-staff', 'web');

    grantPermissionsTo($role, collect(['ViewAny', 'View', 'Create', 'Update'])
        ->flatMap(fn (string $ability) => collect(['Document', 'Report'])->map(fn (string $model) => "{$ability}:{$model}"))
        ->all());

    $user->assignRole($role);

    test()->actingAs($user);

    return $user;
}

/**
 * Mirrors the `assessor` role defined in RolesAndPermissionsSeeder: can view
 * and update reports/documents across every department, but never creates
 * or deletes either.
 */
function actingAsAssessor(): User
{
    $user = User::factory()->create();

    $role = Role::findOrCreate('assessor', 'web');

    grantPermissionsTo($role, [
        'ViewAny:Report', 'View:Report', 'Update:Report',
        'ViewAny:Document', 'View:Document',
    ]);

    $user->assignRole($role);

    test()->actingAs($user);

    return $user;
}
