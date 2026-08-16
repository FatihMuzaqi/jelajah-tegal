<?php

namespace Database\Seeders;

use App\Models\CatalogEntity;
use App\Models\CatalogOffer;
use App\Models\Category;
use App\Models\CulinaryVenue;
use App\Models\Event;
use App\Models\EventTicketType;
use App\Models\MediaAsset;
use App\Models\Mitra;
use App\Models\Region;
use App\Models\RentalVehicle;
use App\Models\ServiceType;
use App\Models\User;
use Illuminate\Database\Seeder;

class TourAssistantDummySeeder extends Seeder
{
    public function run(): void
    {
        $faker = \Faker\Factory::create('id_ID');

        // Prepare basic data
        $owner = User::where('email', 'owner@example.test')->first() ?? User::first();
        $admin = User::where('email', 'admin@example.test')->first() ?? User::first();
        
        $mitra = Mitra::where('slug', 'mitra-utama-tegal')->first();
        if (!$mitra) {
            $this->command->error("Mitra 'mitra-utama-tegal' not found. Please run ComprehensiveTestingSeeder first.");
            return;
        }

        $region = Region::first();

        $serviceTypes = [
            'culinary' => ServiceType::where('code', 'culinary')->first(),
            'event' => ServiceType::where('code', 'event')->first(),
            'rental' => ServiceType::where('code', 'rental')->first(),
        ];

        // Create Media Assets
        $mediaAssets = [];
        $types = ['culinary', 'event', 'rental'];
        foreach ($types as $type) {
            $mediaAssets[$type] = MediaAsset::firstOrCreate(
                [
                    'disk' => 'local',
                    'object_key' => 'media/dummy_' . $type . '.jpg',
                ],
                [
                    'owner_user_id' => $owner->id,
                    'original_name' => 'dummy_' . $type . '.jpg',
                    'mime_type' => 'image/jpeg',
                    'size_bytes' => 100000,
                    'checksum_sha256' => hash('sha256', 'dummy_' . $type),
                    'visibility' => 'public',
                    'purpose' => 'catalog_cover',
                    'status' => 'attached',
                    'uploaded_at' => now(),
                ]
            );
        }

        $this->command->info("Seeding Culinary Data...");
        $culinaryCategory = Category::firstOrCreate([
            'service_type_id' => $serviceTypes['culinary']->id,
            'name' => 'Restoran & Cafe',
            'slug' => 'restoran-cafe',
        ]);
        
        for ($i = 1; $i <= 10; $i++) {
            $entity = CatalogEntity::create([
                'mitra_id' => $mitra->id,
                'service_type_id' => $serviceTypes['culinary']->id,
                'category_id' => $culinaryCategory->id,
                'region_id' => $region->id,
                'name' => $faker->company . ' Resto',
                'slug' => str($faker->company . ' Resto ' . uniqid())->slug(),
                'description' => $faker->paragraph,
                'address' => $faker->address,
                'status' => 'published',
                'is_featured' => $faker->boolean(30),
                'rating_average' => $faker->randomFloat(1, 3.5, 5.0),
                'rating_count' => $faker->numberBetween(10, 500),
                'published_at' => now(),
            ]);
            
            $entity->media()->syncWithoutDetaching([$mediaAssets['culinary']->id => ['role' => 'cover', 'sort_order' => 1]]);

            CulinaryVenue::create([
                'catalog_entity_id' => $entity->id,
                'venue_type' => 'restaurant',
                'accepts_reservations' => true,
                'phone' => $faker->phoneNumber,
                'reservation_notes' => 'Harap datang 15 menit sebelum waktu reservasi.',
            ]);

            CatalogOffer::create([
                'catalog_entity_id' => $entity->id,
                'mitra_id' => $mitra->id,
                'offer_type' => 'reservation', // Assuming reservation/voucher for culinary in Tour AI
                'name' => 'Voucher Makan ' . $entity->name,
                'sku' => 'CUL-VOUCHER-' . $i . '-' . uniqid(),
                'currency' => 'IDR',
                'price' => $faker->randomElement([50000, 75000, 100000, 150000]),
                'status' => 'published',
            ]);
        }

        $this->command->info("Seeding Event Data...");
        $eventCategory = Category::firstOrCreate([
            'service_type_id' => $serviceTypes['event']->id,
            'name' => 'Festival & Konser',
            'slug' => 'festival-konser',
        ]);

        for ($i = 1; $i <= 10; $i++) {
            $eventName = ucwords($faker->words(2, true));
            $entity = CatalogEntity::create([
                'mitra_id' => $mitra->id,
                'service_type_id' => $serviceTypes['event']->id,
                'category_id' => $eventCategory->id,
                'region_id' => $region->id,
                'name' => 'Event ' . $eventName,
                'slug' => str('Event ' . $eventName . ' ' . uniqid())->slug(),
                'description' => $faker->paragraph,
                'address' => $faker->address,
                'status' => 'published',
                'is_featured' => $faker->boolean(30),
                'rating_average' => $faker->randomFloat(1, 3.5, 5.0),
                'rating_count' => $faker->numberBetween(10, 500),
                'published_at' => now(),
            ]);
            
            $entity->media()->syncWithoutDetaching([$mediaAssets['event']->id => ['role' => 'cover', 'sort_order' => 1]]);

            $event = Event::create([
                'catalog_entity_id' => $entity->id,
                'event_type' => 'concert',
                'venue_name' => $faker->company,
                'starts_at' => now()->addDays($faker->numberBetween(1, 30)),
                'ends_at' => now()->addDays($faker->numberBetween(31, 60)),
            ]);

            $offer = CatalogOffer::create([
                'catalog_entity_id' => $entity->id,
                'mitra_id' => $mitra->id,
                'offer_type' => 'ticket',
                'name' => 'Tiket Masuk ' . $entity->name,
                'sku' => 'EVT-TICKET-' . $i . '-' . uniqid(),
                'currency' => 'IDR',
                'price' => $faker->randomElement([50000, 100000, 150000, 200000]),
                'status' => 'published',
            ]);

            EventTicketType::create([
                'event_id' => $event->id,
                'catalog_offer_id' => $offer->id,
                'name' => 'Regular Ticket',
                'quota' => 500,
            ]);
        }

        $this->command->info("Seeding Rental Data...");
        $rentalCategory = Category::firstOrCreate([
            'service_type_id' => $serviceTypes['rental']->id,
            'name' => 'Rental Mobil',
            'slug' => 'rental-mobil',
        ]);

        for ($i = 1; $i <= 10; $i++) {
            $brand = $faker->randomElement(['Toyota', 'Honda', 'Daihatsu', 'Suzuki']);
            $model = $faker->randomElement(['Avanza', 'Xenia', 'Innova', 'Brio', 'Ertiga']);
            $entityName = "Sewa $brand $model - " . $faker->company;

            $entity = CatalogEntity::create([
                'mitra_id' => $mitra->id,
                'service_type_id' => $serviceTypes['rental']->id,
                'category_id' => $rentalCategory->id,
                'region_id' => $region->id,
                'name' => $entityName,
                'slug' => str($entityName . ' ' . uniqid())->slug(),
                'description' => $faker->paragraph,
                'address' => $faker->address,
                'status' => 'published',
                'is_featured' => $faker->boolean(30),
                'rating_average' => $faker->randomFloat(1, 3.5, 5.0),
                'rating_count' => $faker->numberBetween(10, 500),
                'published_at' => now(),
            ]);
            
            $entity->media()->syncWithoutDetaching([$mediaAssets['rental']->id => ['role' => 'cover', 'sort_order' => 1]]);

            RentalVehicle::create([
                'catalog_entity_id' => $entity->id,
                'vehicle_type' => 'car',
                'brand' => $brand,
                'model' => $model,
                'transmission' => $faker->randomElement(['manual', 'automatic']),
                'year' => $faker->numberBetween(2018, 2024),
                'seats' => $faker->randomElement([5, 7]),
                'self_drive_available' => true,
                'plate_number' => 'G ' . $faker->numberBetween(1000, 9999) . ' ' . $faker->lexify('??'),
            ]);

            CatalogOffer::create([
                'catalog_entity_id' => $entity->id,
                'mitra_id' => $mitra->id,
                'offer_type' => 'rental_day',
                'name' => 'Sewa Harian ' . $brand . ' ' . $model,
                'sku' => 'RNT-DAY-' . $i . '-' . uniqid(),
                'currency' => 'IDR',
                'price' => $faker->randomElement([300000, 400000, 500000, 600000]),
                'status' => 'published',
            ]);
        }

        $this->command->info("Dummy data generated successfully!");
    }
}
