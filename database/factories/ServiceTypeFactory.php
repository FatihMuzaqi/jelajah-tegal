<?php

namespace Database\Factories;

use App\Models\ServiceType;
use Illuminate\Database\Eloquent\Factories\Factory;

class ServiceTypeFactory extends Factory
{
    protected $model = ServiceType::class;

    public function definition(): array
    {
        return ['code' => fake()->unique()->slug(2), 'name' => fake()->words(2, true), 'is_transactional' => true, 'sort_order' => 0];
    }
}
