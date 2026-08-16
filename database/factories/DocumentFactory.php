<?php

namespace Database\Factories;

use App\Models\AcademicYear;
use App\Models\BasisMain;
use App\Models\Document;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Document>
 */
class DocumentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'basis_main_id' => BasisMain::factory(),
            'academic_year_id' => AcademicYear::factory(),
        ];
    }
}
