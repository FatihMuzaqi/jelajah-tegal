<?php

namespace Database\Factories;

use App\Models\Mitra;
use App\Models\MitraOperatingHour;
use Illuminate\Database\Eloquent\Factories\Factory;

class MitraOperatingHourFactory extends Factory
{
    protected $model = MitraOperatingHour::class;

    public function definition(): array
    {
        return ['mitra_id' => Mitra::factory(), 'day_of_week' => fake()->numberBetween(0, 6), 'opens_at' => '09:00', 'closes_at' => '17:00', 'is_closed' => false];
    }
}
