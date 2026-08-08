<?php

namespace Database\Factories;

use App\Models\Mitra;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MitraFactory extends Factory
{
    protected $model = Mitra::class;

    public function definition(): array
    {
        return ['owner_user_id' => User::factory(), 'legal_name' => fake()->company(), 'display_name' => fake()->company(), 'slug' => fake()->unique()->slug(), 'status' => 'active'];
    }
}
