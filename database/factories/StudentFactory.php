<?php

namespace Database\Factories;

use App\Enums\Gender;
use App\Enums\Religion;
use App\Enums\StudentStatus;
use App\Models\Student;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Student>
 */
class StudentFactory extends Factory
{
    protected $model = Student::class;

    public function definition(): array
    {
        return [
            'tenant_id' => Tenant::factory(),
            'nis' => fake()->unique()->numerify('2025####'),
            'nisn' => fake()->unique()->numerify('00########'),
            'full_name' => fake()->name(),
            'gender' => fake()->randomElement(Gender::cases()),
            'birth_place' => fake()->city(),
            'birth_date' => fake()->dateTimeBetween('-15 years', '-12 years'),
            'religion' => Religion::cases()[0],
            'status' => StudentStatus::ACTIVE,
            'entry_year' => (int) date('Y'),
        ];
    }
}
