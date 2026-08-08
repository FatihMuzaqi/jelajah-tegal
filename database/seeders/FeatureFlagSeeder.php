<?php

namespace Database\Seeders;

use App\Models\FeatureFlag;
use Illuminate\Database\Seeder;

class FeatureFlagSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            ['google-oauth', 'Google OAuth', 'disabled'],
            ['admin-mfa', 'Admin MFA', 'enabled'],
            ['public-mitra-directory', 'Direktori Mitra publik', 'enabled'],
            ['public-ai-planner', 'CTA AI Planner publik', 'disabled'],
            ['public-newsletter', 'Newsletter publik', 'disabled'],
            ['public-tourism', 'Katalog wisata publik', 'enabled'],
            ['public-accommodation', 'Katalog penginapan publik', 'enabled'],
            ['public-culinary', 'Katalog kuliner publik', 'enabled'],
            ['public-event', 'Katalog event publik', 'enabled'],
            ['public-rental', 'Katalog rental publik', 'enabled'],
        ] as [$key, $name, $status]) {
            FeatureFlag::updateOrCreate(['key_name' => $key], ['description' => $name, 'status' => $status, 'rollout_percentage' => 100]);
        }
    }
}
