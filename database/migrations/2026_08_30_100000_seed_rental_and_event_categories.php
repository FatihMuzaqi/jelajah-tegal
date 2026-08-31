<?php

use App\Models\Category;
use App\Models\CatalogEntity;
use App\Models\ServiceType;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Tambahkan Kategori Rental Kendaraan
        $rentalService = ServiceType::where('code', 'rental')->first();
        if ($rentalService) {
            $rentalCategories = [
                'Mobil Keluarga & MPV' => 'rental-mpv',
                'City Car & Hatchback' => 'rental-city-car',
                'SUV & Crossover' => 'rental-suv',
                'Motor & Skuter Matic' => 'rental-motor',
                'Minibus & HiAce / Elf' => 'rental-minibus',
                'Mobil Niaga / Pick-Up' => 'rental-pickup',
            ];

            foreach ($rentalCategories as $name => $slug) {
                Category::firstOrCreate(
                    ['service_type_id' => $rentalService->id, 'slug' => $slug],
                    ['name' => $name, 'is_active' => true]
                );
            }

            // Update rental entities yang category_id nya masih null
            $defaultCat = Category::where('service_type_id', $rentalService->id)->first();
            if ($defaultCat) {
                CatalogEntity::where('service_type_id', $rentalService->id)
                    ->whereNull('category_id')
                    ->update(['category_id' => $defaultCat->id]);
            }
        }

        // 2. Tambahkan Kategori Event / Festival
        $eventService = ServiceType::where('code', 'event')->first();
        if ($eventService) {
            $eventCategories = [
                'Festival Seni & Budaya' => 'event-festival-budaya',
                'Konser Musik & Hiburan' => 'event-konser-musik',
                'Pameran & Expo UMKM' => 'event-pameran-expo',
                'Kompetisi & Olahraga' => 'event-kompetisi-olahraga',
                'Seminar & Workshop' => 'event-seminar-workshop',
            ];

            foreach ($eventCategories as $name => $slug) {
                Category::firstOrCreate(
                    ['service_type_id' => $eventService->id, 'slug' => $slug],
                    ['name' => $name, 'is_active' => true]
                );
            }

            // Update event entities yang category_id nya masih null
            $defaultEventCat = Category::where('service_type_id', $eventService->id)->first();
            if ($defaultEventCat) {
                CatalogEntity::where('service_type_id', $eventService->id)
                    ->whereNull('category_id')
                    ->update(['category_id' => $defaultEventCat->id]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Biarkan data kategori tetap aman jika rollback
    }
};
