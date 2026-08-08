<?php

namespace Database\Factories;

use App\Models\MediaAsset;
use App\Models\Mitra;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MediaAssetFactory extends Factory
{
    protected $model = MediaAsset::class;

    public function definition(): array
    {
        return ['owner_user_id' => User::factory(), 'is_platform_owned' => false, 'disk' => 'local', 'object_key' => 'tests/'.fake()->unique()->uuid(), 'mime_type' => 'image/jpeg', 'size_bytes' => 1024, 'checksum_sha256' => hash('sha256', fake()->uuid()), 'visibility' => 'private', 'purpose' => 'test', 'status' => 'ready', 'metadata' => [], 'uploaded_at' => now()];
    }

    public function forMitra(Mitra $mitra): static
    {
        return $this->state(['owner_user_id' => null, 'mitra_id' => $mitra->id, 'is_platform_owned' => false]);
    }

    public function platform(): static
    {
        return $this->state(['owner_user_id' => null, 'mitra_id' => null, 'is_platform_owned' => true]);
    }
}
