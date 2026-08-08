<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\ServiceType;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition(): array
    {
        $name = fake()->words(2, true);

        return ['service_type_id' => ServiceType::factory(), 'name' => $name, 'slug' => str($name)->slug(), 'is_active' => true];
    }
}
