<?php

namespace Database\Factories;

use App\Models\Region;
use Illuminate\Database\Eloquent\Factories\Factory;

class RegionFactory extends Factory
{
    protected $model = Region::class;

    public function definition(): array
    {
        return ['level' => 'city', 'code' => fake()->unique()->bothify('RG-####'), 'name' => fake()->city()];
    }
}
