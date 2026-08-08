<?php

namespace Database\Factories;

use App\Models\Mitra;
use App\Models\MitraBankAccount;
use Illuminate\Database\Eloquent\Factories\Factory;

class MitraBankAccountFactory extends Factory
{
    protected $model = MitraBankAccount::class;

    public function definition(): array
    {
        $number = fake()->unique()->numerify('##########');

        return ['mitra_id' => Mitra::factory(), 'bank_code' => 'TEST', 'account_name_encrypted' => fake()->name(), 'account_number_encrypted' => $number, 'account_fingerprint' => hash('sha256', $number), 'status' => 'pending', 'is_primary' => false];
    }
}
