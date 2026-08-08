<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Facility;
use App\Models\ServiceType;
use Illuminate\Database\Seeder;

class CulinaryReferenceSeeder extends Seeder
{
    public function run(): void
    {
        $service = ServiceType::where('code', 'culinary')->firstOrFail();
        foreach (['Restoran' => 'restoran', 'Kafe' => 'kafe', 'Jajanan Lokal' => 'jajanan-lokal'] as $name => $slug) {
            Category::firstOrCreate(['service_type_id' => $service->id, 'slug' => $slug], ['name' => $name, 'is_active' => true]);
        }foreach (['Parkir' => 'parkir', 'Wi-Fi' => 'wi-fi', 'Toilet' => 'toilet'] as $name => $slug) {
            Facility::firstOrCreate(['service_type_id' => $service->id, 'slug' => $slug], ['name' => $name, 'is_active' => true]);
        }
    }
}
