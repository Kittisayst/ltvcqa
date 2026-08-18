<?php

namespace Database\Factories;

use App\Models\BasisMain;
use App\Models\Indicator;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BasisMain>
 */
class BasisMainFactory extends Factory
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
            'title' => fake()->sentence(5),
            'order' => fake()->numberBetween(1, 20),
        ];
    }
}
