<?php

namespace Database\Factories;

use App\Enums\SchoolType;
use App\Enums\TenantStatus;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Tenant>
 */
class TenantFactory extends Factory
{
    protected $model = Tenant::class;

    public function definition(): array
    {
        $name = 'Sekolah ' . fake()->unique()->lastName();

        return [
            'name' => $name,
            'slug' => Str::slug($name) . '-' . Str::lower(Str::random(5)),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->numerify('08##########'),
            'city' => fake()->city(),
            'province' => 'DKI Jakarta',
            'school_type' => SchoolType::cases()[0],
            'status' => TenantStatus::ACTIVE,
            'currency' => 'IDR',
        ];
    }
}
