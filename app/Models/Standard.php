<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

#[Fillable(['framework_id', 'name', 'order'])]
class Standard extends Model
{
    use HasFactory;

    public function framework(): BelongsTo
    {
        return $this->belongsTo(QaFramework::class, 'framework_id');
    }

    public function indicators(): HasMany
    {
        return $this->hasMany(Indicator::class);
    }

    public function basisMains(): HasManyThrough
    {
        return $this->hasManyThrough(BasisMain::class, Indicator::class, 'standard_id', 'indicator_id');
    }

    protected function orderedName(): Attribute
    {
        return Attribute::make(
            get: fn (): string => "{$this->order}. {$this->name}",
        );
    }
}
