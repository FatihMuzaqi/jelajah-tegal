<?php

namespace Database\Factories;

use App\Models\Mitra;
use App\Models\MitraMember;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MitraMemberFactory extends Factory
{
    protected $model = MitraMember::class;

    public function definition(): array
    {
        return ['mitra_id' => Mitra::factory(), 'user_id' => User::factory(), 'status' => 'active', 'joined_at' => now()];
    }
}
