<?php

namespace App\Models;

use App\Observers\AcademicYearObserver;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

#[Fillable(['framework_id', 'name', 'is_active'])]
#[ObservedBy(AcademicYearObserver::class)]
class AcademicYear extends Model
{
    use HasFactory;

    private const ACTIVE_CACHE_KEY = 'active-academic-year';

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /**
     * The single active AcademicYear, cached — looked up on nearly every
     * page (dashboard widgets, upload/report forms) and changes only when
     * an admin explicitly switches which year is active.
     *
     * Caches the raw attributes, not the Eloquent model itself: serializing
     * drivers (file/database) unserialize a cached model as
     * __PHP_Incomplete_Class, since the model's internal state doesn't
     * round-trip cleanly. A plain attribute array always does.
     */
    public static function active(): ?self
    {
        $attributes = Cache::rememberForever(
            self::ACTIVE_CACHE_KEY,
            fn () => static::where('is_active', true)->first()?->getAttributes(),
        );

        return $attributes ? (new static)->newFromBuilder($attributes) : null;
    }

    public static function forgetActiveCache(): void
    {
        Cache::forget(self::ACTIVE_CACHE_KEY);
    }

    public function framework(): BelongsTo
    {
        return $this->belongsTo(QaFramework::class, 'framework_id');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class);
    }
}
