<?php

namespace Database\Factories;

use App\Models\AcademicYear;
use App\Models\QaFramework;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AcademicYear>
 */
class AcademicYearFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'framework_id' => QaFramework::factory(),
            'name' => fake()->unique()->numerify('####-####'),
            'is_active' => true,
        ];
    }
}
