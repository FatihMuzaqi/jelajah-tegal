<?php

namespace Database\Factories;

use App\Models\MediaAsset;
use App\Models\Mitra;
use App\Models\MitraKycDocument;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MitraKycDocumentFactory extends Factory
{
    protected $model = MitraKycDocument::class;

    public function definition(): array
    {
        return ['mitra_id' => Mitra::factory(), 'media_asset_id' => MediaAsset::factory(), 'document_type' => 'business_license', 'version' => 1, 'status' => 'submitted', 'submitted_by' => User::factory()];
    }

    public function forMitra(Mitra $mitra): static
    {
        return $this->state(['mitra_id' => $mitra->id, 'media_asset_id' => MediaAsset::factory()->forMitra($mitra)]);
    }
}
