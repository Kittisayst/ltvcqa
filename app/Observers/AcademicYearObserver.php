<?php

namespace App\Observers;

use App\Models\AcademicYear;

class AcademicYearObserver
{
    public function saved(AcademicYear $academicYear): void
    {
        AcademicYear::forgetActiveCache();
    }

    public function deleted(AcademicYear $academicYear): void
    {
        AcademicYear::forgetActiveCache();
    }
}
