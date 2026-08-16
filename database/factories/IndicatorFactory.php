<?php

namespace Database\Factories;

use App\Models\Indicator;
use App\Models\Standard;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Indicator>
 */
class IndicatorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'standard_id' => Standard::factory(),
            'name' => fake()->sentence(4),
            'order' => fake()->numberBetween(1, 20),
        ];
    }
}
