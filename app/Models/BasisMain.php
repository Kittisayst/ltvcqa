<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['indicator_id', 'title', 'order'])]
class BasisMain extends Model
{
    use HasFactory;

    public function indicator(): BelongsTo
    {
        return $this->belongsTo(Indicator::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }
}
