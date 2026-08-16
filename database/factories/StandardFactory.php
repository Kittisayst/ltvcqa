<?php

namespace Database\Factories;

use App\Models\QaFramework;
use App\Models\Standard;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Standard>
 */
class StandardFactory extends Factory
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
            'name' => fake()->sentence(3),
            'order' => fake()->numberBetween(1, 20),
        ];
    }
}
