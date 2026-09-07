<?php

use App\Filament\Resources\Reports\Pages\ListReports;
use App\Models\AcademicYear;
use App\Models\Department;
use App\Models\Indicator;
use App\Models\QaFramework;
use App\Models\Report;
use App\Models\Standard;
use Livewire\Livewire;

use function Pest\Laravel\assertDatabaseCount;

beforeEach(fn () => AcademicYear::forgetActiveCache());

/**
 * @return array{year: AcademicYear, department: Department, indicator: Indicator}
 */
function scopedConsole(): array
{
    $framework = QaFramework::factory()->create();
    $year = AcademicYear::factory()->for($framework, 'framework')->create(['is_active' => true]);
    $standard = Standard::factory()->for($framework, 'framework')->create();
    $indicator = Indicator::factory()->for($standard)->create();
    $department = Department::factory()->create();

    return ['year' => $year, 'department' => $department, 'indicator' => $indicator];
}

it('does not create a report merely by opening the evaluate modal', function (): void {
    actingAsSuperAdmin();
    ['department' => $department, 'indicator' => $indicator] = scopedConsole();

    Livewire::test(ListReports::class)
        ->set('tableFilters.department_id.value', $department->id)
        ->mountTableAction('evaluate', $indicator->id);

    assertDatabaseCount(Report::class, 0);
});

it('creates a framework-consistent report when the evaluate action is saved', function (): void {
    actingAsSuperAdmin();
    ['year' => $year, 'department' => $department, 'indicator' => $indicator] = scopedConsole();

    Livewire::test(ListReports::class)
        ->set('tableFilters.department_id.value', $department->id)
        ->callTableAction('evaluate', $indicator->id, ['status' => 'draft']);

    assertDatabaseCount(Report::class, 1);

    $report = Report::first();

    expect($report->indicator_id)->toBe($indicator->id)
        ->and($report->department_id)->toBe($department->id)
        ->and($report->academic_year_id)->toBe($year->id)
        ->and($report->indicator->standard->framework_id)->toBe($year->framework_id);
});

it('persists score and narrative fields through the evaluate action', function (): void {
    actingAsSuperAdmin();
    ['department' => $department, 'indicator' => $indicator] = scopedConsole();

    Livewire::test(ListReports::class)
        ->set('tableFilters.department_id.value', $department->id)
        ->callTableAction('evaluate', $indicator->id, [
            'status' => 'draft',
            'score' => 87.5,
            'good_point' => 'ດີຫຼາຍ',
            'remain_point' => 'ຍັງຂາດ',
            'proposal' => 'ຄວນປັບປຸງ',
        ])
        ->assertHasNoTableActionErrors();

    $report = Report::first();

    expect((float) $report->score)->toBe(87.5)
        ->and($report->good_point)->toBe('ດີຫຼາຍ')
        ->and($report->remain_point)->toBe('ຍັງຂາດ')
        ->and($report->proposal)->toBe('ຄວນປັບປຸງ');
});

it('rejects a submitted status with no score', function (): void {
    actingAsSuperAdmin();
    ['department' => $department, 'indicator' => $indicator] = scopedConsole();

    Livewire::test(ListReports::class)
        ->set('tableFilters.department_id.value', $department->id)
        ->callTableAction('evaluate', $indicator->id, [
            'status' => 'submitted',
            'score' => null,
        ])
        ->assertHasTableActionErrors(['score']);

    assertDatabaseCount(Report::class, 0);
});

it('never creates a second report for the same indicator, department and year', function (): void {
    actingAsSuperAdmin();
    ['department' => $department, 'indicator' => $indicator] = scopedConsole();

    Livewire::test(ListReports::class)
        ->set('tableFilters.department_id.value', $department->id)
        ->callTableAction('evaluate', $indicator->id, ['status' => 'draft'])
        ->callTableAction('evaluate', $indicator->id, ['status' => 'draft', 'score' => 40]);

    assertDatabaseCount(Report::class, 1);
});
