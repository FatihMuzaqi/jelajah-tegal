<?php

namespace Database\Factories;

use App\Models\FeatureFlag;
use Illuminate\Database\Eloquent\Factories\Factory;

class FeatureFlagFactory extends Factory
{
    protected $model = FeatureFlag::class;

    public function definition(): array
    {
        return ['key_name' => fake()->unique()->slug(), 'description' => fake()->sentence(), 'status' => 'disabled', 'rollout_percentage' => 0, 'rules' => []];
    }
}
