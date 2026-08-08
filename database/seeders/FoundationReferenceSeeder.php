<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class FoundationReferenceSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            ServiceTypeSeeder::class,
            RolesAndPermissionsSeeder::class,
            FeatureFlagSeeder::class,
        ]);
    }
}
