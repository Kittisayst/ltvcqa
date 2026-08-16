<?php

namespace Database\Factories;

use App\Models\Document;
use App\Models\DocumentFile;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DocumentFile>
 */
class DocumentFileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'document_id' => Document::factory(),
            'reference_no' => fake()->bothify('##/??-###'),
            'issued_date' => fake()->date(),
            'disk' => 'local',
            'path' => 'documents/'.fake()->uuid().'.pdf',
            'original_name' => fake()->word().'.pdf',
            'mime_type' => 'application/pdf',
            'size' => fake()->numberBetween(1000, 500000),
        ];
    }
}
