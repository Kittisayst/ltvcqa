<?php

use App\Filament\Widgets\PendingReportReviews;
use App\Models\Report;
use Livewire\Livewire;

it('is visible to assessor and super_admin but not department-staff', function (): void {
    actingAsAssessor();
    expect(PendingReportReviews::canView())->toBeTrue();

    actingAsSuperAdmin();
    expect(PendingReportReviews::canView())->toBeTrue();

    actingAsDepartmentStaff();
    expect(PendingReportReviews::canView())->toBeFalse();
});

it('lists only reports with a submitted status', function (): void {
    actingAsAssessor();

    $submitted = Report::factory()->create(['status' => 'submitted']);
    $draft = Report::factory()->create(['status' => 'draft']);
    $approved = Report::factory()->create(['status' => 'approved']);

    Livewire::test(PendingReportReviews::class)
        ->assertCanSeeTableRecords([$submitted])
        ->assertCanNotSeeTableRecords([$draft, $approved]);
});
