<?php

use App\Models\AcademicYear;
use App\Models\QaFramework;
use Illuminate\Support\Facades\Config;

it('survives a real serializing cache driver, not just the in-memory array driver used by default in tests', function (): void {
    // Regression test: caching the Eloquent model itself (not its plain
    // attributes) unserializes as __PHP_Incomplete_Class on the database
    // cache driver, since a model's internal state doesn't round-trip
    // through serialize()/unserialize() cleanly. The `array` driver never
    // truly serializes, so it can't catch this class of bug.
    Config::set('cache.default', 'database');

    $year = AcademicYear::factory()->create(['is_active' => true]);

    $active = AcademicYear::active();

    expect($active)->toBeInstanceOf(AcademicYear::class)
        ->and($active->id)->toBe($year->id)
        ->and($active->name)->toBe($year->name);
});

it('returns null when no academic year is active', function (): void {
    expect(AcademicYear::active())->toBeNull();
});

it('caches the active academic year and invalidates it when the active year changes', function (): void {
    $framework = QaFramework::factory()->create();

    $yearA = AcademicYear::factory()->for($framework, 'framework')->create(['is_active' => true]);

    expect(AcademicYear::active()->id)->toBe($yearA->id);

    $yearA->update(['is_active' => false]);
    $yearB = AcademicYear::factory()->for($framework, 'framework')->create(['is_active' => true]);

    expect(AcademicYear::active()->id)->toBe($yearB->id);
});

it('invalidates the cache when the active academic year is deleted', function (): void {
    $year = AcademicYear::factory()->create(['is_active' => true]);

    expect(AcademicYear::active()->id)->toBe($year->id);

    $year->delete();

    expect(AcademicYear::active())->toBeNull();
});
