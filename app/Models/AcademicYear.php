<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['framework_id', 'name', 'is_active'])]
class AcademicYear extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
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
