<?php

use App\Models\Department;
use App\Models\Report;
use App\Models\User;
use Illuminate\Notifications\DatabaseNotification;
use Spatie\Permission\Models\Role;

it('notifies every assessor when a report is submitted', function (): void {
    $assessorRole = Role::findOrCreate('assessor', 'web');
    $assessorOne = User::factory()->create()->assignRole($assessorRole);
    $assessorTwo = User::factory()->create()->assignRole($assessorRole);
    $nonAssessor = User::factory()->create();

    $report = Report::factory()->create(['status' => 'draft']);

    $report->update(['status' => 'submitted']);

    expect(DatabaseNotification::where('notifiable_id', $assessorOne->id)->count())->toBe(1)
        ->and(DatabaseNotification::where('notifiable_id', $assessorTwo->id)->count())->toBe(1)
        ->and(DatabaseNotification::where('notifiable_id', $nonAssessor->id)->count())->toBe(0);
});

it('notifies department-staff in the report department when a report is approved', function (): void {
    $department = Department::factory()->create();
    $otherDepartment = Department::factory()->create();

    $staffRole = Role::findOrCreate('department-staff', 'web');
    $staffInDepartment = User::factory()->for($department)->create()->assignRole($staffRole);
    $staffInOtherDepartment = User::factory()->for($otherDepartment)->create()->assignRole($staffRole);

    $report = Report::factory()->create(['department_id' => $department->id, 'status' => 'submitted']);

    $report->update(['status' => 'approved']);

    expect(DatabaseNotification::where('notifiable_id', $staffInDepartment->id)->count())->toBe(1)
        ->and(DatabaseNotification::where('notifiable_id', $staffInOtherDepartment->id)->count())->toBe(0);
});

it('does not send a notification when the report is saved without a status change', function (): void {
    $report = Report::factory()->create(['status' => 'draft']);

    $report->update(['score' => 90]);

    expect(DatabaseNotification::count())->toBe(0);
});
