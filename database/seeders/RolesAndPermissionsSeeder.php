<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * department-staff only ever handles evidence submissions and their own
     * department's evaluation reports — no access to master data (standards,
     * indicators, frameworks, other users) or destructive actions.
     */
    private const DEPARTMENT_STAFF_PERMISSIONS = [
        'ViewAny:Document', 'View:Document', 'Create:Document', 'Update:Document',
        'ViewAny:Report', 'View:Report', 'Create:Report', 'Update:Report',
    ];

    /**
     * assessor evaluates reports for every department (not scoped) and may
     * review submitted evidence, but never creates/deletes either — the
     * department owns the submission, assessor only owns the evaluation.
     */
    private const ASSESSOR_PERMISSIONS = [
        'ViewAny:Report', 'View:Report', 'Update:Report',
        'ViewAny:Document', 'View:Document',
    ];

    public function run(): void
    {
        Artisan::call('shield:install', ['panel' => 'admin', '--no-interaction' => true]);
        Artisan::call('shield:generate', [
            '--all' => true,
            '--panel' => 'admin',
            '--ignore-existing-policies' => true,
            '--no-interaction' => true,
        ]);

        $departmentStaff = Role::firstOrCreate(['name' => 'department-staff', 'guard_name' => 'web']);

        $departmentStaff->syncPermissions(
            Permission::whereIn('name', self::DEPARTMENT_STAFF_PERMISSIONS)->get()
        );

        $assessor = Role::firstOrCreate(['name' => 'assessor', 'guard_name' => 'web']);

        $assessor->syncPermissions(
            Permission::whereIn('name', self::ASSESSOR_PERMISSIONS)->get()
        );

        User::where('username', 'admin')->first()?->assignRole('super_admin');

        User::where('username', '!=', 'admin')
            ->whereNotNull('department_id')
            ->get()
            ->each(fn (User $user) => $user->assignRole('department-staff'));
    }
}
