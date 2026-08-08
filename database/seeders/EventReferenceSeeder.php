<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Facility;
use App\Models\ServiceType;
use Illuminate\Database\Seeder;

class EventReferenceSeeder extends Seeder
{
    public function run(): void
    {
        $service = ServiceType::where('code', 'event')->firstOrFail();
        foreach (['Festival' => 'festival', 'Pameran' => 'pameran', 'Pertunjukan' => 'pertunjukan'] as $name => $slug) {
            Category::firstOrCreate(['service_type_id' => $service->id, 'slug' => $slug], ['name' => $name, 'is_active' => true]);
        }foreach (['Parkir' => 'parkir', 'Toilet' => 'toilet', 'Akses Disabilitas' => 'akses-disabilitas'] as $name => $slug) {
            Facility::firstOrCreate(['service_type_id' => $service->id, 'slug' => $slug], ['name' => $name, 'is_active' => true]);
        }
    }
}
