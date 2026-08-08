<?php

namespace Database\Factories;

use App\Models\DatabaseNotification;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class DatabaseNotificationFactory extends Factory
{
    protected $model = DatabaseNotification::class;

    public function definition(): array
    {
        return ['type' => 'foundation.test', 'user_id' => User::factory(), 'data' => ['message' => 'Test notification']];
    }
}
