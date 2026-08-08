<?php

namespace Database\Factories;

use App\Models\ApplicationSetting;
use Illuminate\Database\Eloquent\Factories\Factory;

class ApplicationSettingFactory extends Factory
{
    protected $model = ApplicationSetting::class;

    public function definition(): array
    {
        return ['key_name' => fake()->unique()->slug(), 'value_json' => ['enabled' => true], 'value_type' => 'json', 'is_secret' => false];
    }
}
