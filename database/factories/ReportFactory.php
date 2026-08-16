<?php

namespace Database\Factories;

use App\Models\AcademicYear;
use App\Models\Department;
use App\Models\Indicator;
use App\Models\Report;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Report>
 */
class ReportFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'indicator_id' => Indicator::factory(),
            'department_id' => Department::factory(),
            'academic_year_id' => AcademicYear::factory(),
            'status' => 'draft',
        ];
    }
}
