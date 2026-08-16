<?php

namespace Database\Factories;

use App\Models\QaFramework;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<QaFramework>
 */
class QaFrameworkFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->sentence(3),
            'status' => 'published',
        ];
    }

    public function draft(): static
    {
        return $this->state(['status' => 'draft']);
    }
}
