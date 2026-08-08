<?php

namespace Database\Factories;

use App\Models\MfaRecoveryCode;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MfaRecoveryCodeFactory extends Factory
{
    protected $model = MfaRecoveryCode::class;

    public function definition(): array
    {
        return ['user_id' => User::factory(), 'code_hash' => bcrypt(fake()->unique()->uuid()), 'created_at' => now()];
    }
}
