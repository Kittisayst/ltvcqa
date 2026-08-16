<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'status'])]
class QaFramework extends Model
{
    use HasFactory;

    public function standards(): HasMany
    {
        return $this->hasMany(Standard::class, 'framework_id');
    }

    public function academicYears(): HasMany
    {
        return $this->hasMany(AcademicYear::class, 'framework_id');
    }
}
