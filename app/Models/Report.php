<?php

namespace App\Models;

use App\Observers\ReportObserver;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['indicator_id', 'department_id', 'academic_year_id', 'assessor_id', 'score', 'good_point', 'remain_point', 'proposal', 'status'])]
#[ObservedBy(ReportObserver::class)]
class Report extends Model
{
    use HasFactory;

    public function indicator(): BelongsTo
    {
        return $this->belongsTo(Indicator::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function assessor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assessor_id');
    }
}
