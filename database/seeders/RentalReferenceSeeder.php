<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\ServiceType;
use Illuminate\Database\Seeder;

class RentalReferenceSeeder extends Seeder
{
    public function run(): void
    {
        $service = ServiceType::where('code', 'rental')->firstOrFail();
        foreach (['Mobil' => 'mobil', 'Motor' => 'motor', 'Minibus' => 'minibus'] as $name => $slug) {
            Category::firstOrCreate(['service_type_id' => $service->id, 'slug' => $slug], ['name' => $name, 'is_active' => true]);
        }
    }
}
