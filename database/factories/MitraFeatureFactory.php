<?php

namespace Database\Factories;

use App\Models\Mitra;
use App\Models\MitraFeature;
use App\Models\ServiceType;
use Illuminate\Database\Eloquent\Factories\Factory;

class MitraFeatureFactory extends Factory
{
    protected $model = MitraFeature::class;

    public function definition(): array
    {
        return ['mitra_id' => Mitra::factory(), 'service_type_id' => ServiceType::factory(), 'status' => 'enabled', 'enabled_at' => now()];
    }
}
