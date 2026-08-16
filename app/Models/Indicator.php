<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['standard_id', 'name', 'order'])]
class Indicator extends Model
{
    use HasFactory;

    public function standard(): BelongsTo
    {
        return $this->belongsTo(Standard::class);
    }

    public function basisMains(): HasMany
    {
        return $this->hasMany(BasisMain::class);
    }

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class);
    }
}
