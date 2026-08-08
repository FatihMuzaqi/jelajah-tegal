<?php

namespace Database\Factories;

use App\Models\Mitra;
use App\Models\MitraInvitation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MitraInvitationFactory extends Factory
{
    protected $model = MitraInvitation::class;

    public function definition(): array
    {
        return ['mitra_id' => Mitra::factory(), 'email' => fake()->unique()->safeEmail(), 'token_hash' => hash('sha256', fake()->uuid()), 'invited_by' => User::factory(), 'expires_at' => now()->addDays(3)];
    }
}
