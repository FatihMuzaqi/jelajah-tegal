<?php

namespace Database\Factories;

use App\Models\Mitra;
use App\Models\MitraFeatureRequest;
use App\Models\ServiceType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MitraFeatureRequestFactory extends Factory
{
    protected $model = MitraFeatureRequest::class;

    public function definition(): array
    {
        return ['mitra_id' => Mitra::factory(), 'service_type_id' => ServiceType::factory(), 'requested_by' => User::factory(), 'status' => 'requested', 'reason' => fake()->sentence()];
    }
}
