<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\ServiceType;
use Illuminate\Database\Seeder;

class AdditionalCategoriesSeeder extends Seeder
{
    public function run(): void
    {
        $eventService = ServiceType::where('code', 'event')->first();
        if ($eventService) {
            $eventCategories = [
                ['name' => 'Festival Budaya & Seni Tradisi', 'slug' => 'ev-budaya'],
                ['name' => 'Pameran & Bazar UMKM Lokal', 'slug' => 'ev-bazar'],
                ['name' => 'Konser, Musik & Hiburan', 'slug' => 'ev-musik'],
                ['name' => 'Workshop, Seminar & Edukasi', 'slug' => 'ev-workshop'],
                ['name' => 'Lomba & Kompetisi Daerah', 'slug' => 'ev-lomba'],
            ];

            foreach ($eventCategories as $cat) {
                Category::firstOrCreate(
                    ['service_type_id' => $eventService->id, 'slug' => $cat['slug']],
                    ['name' => $cat['name'], 'is_active' => true]
                );
            }
        }

        $rentalService = ServiceType::where('code', 'rental')->first();
        if ($rentalService) {
            $rentalCategories = [
                ['name' => 'Rental Mobil Lepas Kunci / Driver', 'slug' => 'rn-mobil'],
                ['name' => 'Rental Motor & Skuter Matik', 'slug' => 'rn-motor'],
                ['name' => 'Sewa Bus Pariwisata & Elf', 'slug' => 'rn-bus'],
                ['name' => 'Sewa Perlengkapan Camping & Outdoor', 'slug' => 'rn-camping'],
                ['name' => 'Sewa Busana Tradisional & Adat', 'slug' => 'rn-busana'],
            ];

            foreach ($rentalCategories as $cat) {
                Category::firstOrCreate(
                    ['service_type_id' => $rentalService->id, 'slug' => $cat['slug']],
                    ['name' => $cat['name'], 'is_active' => true]
                );
            }
        }
    }
}
