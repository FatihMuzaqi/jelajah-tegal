<?php

namespace Database\Seeders;

use App\Models\Accommodation;
use App\Models\AccommodationRoom;
use App\Models\CatalogEntity;
use App\Models\CatalogOffer;
use App\Models\Category;
use App\Models\MediaAsset;
use App\Models\Mitra;
use App\Models\MitraFeature;
use App\Models\Region;
use App\Models\ServiceType;
use App\Models\User;
use Illuminate\Database\Seeder;

class AccommodationSeeder extends Seeder
{
    public function run(): void
    {
        $ownerUser = User::where('email', 'owner@example.test')->first() ?? User::first();
        $accommType = ServiceType::where('code', 'accommodation')->first();

        if (! $accommType) {
            $accommType = ServiceType::create([
                'name' => 'Penginapan',
                'code' => 'accommodation',
                'description' => 'Akomodasi hotel, villa, resort, dan homestay',
                'is_active' => true,
            ]);
        }

        // Regions
        $regionDefault = Region::first();
        $regionGuci = Region::where('code', 'TGL-KAB-GUCI')->first() ?? $regionDefault;
        $regionSlawi = Region::where('code', 'TGL-KAB-SLAWI')->first() ?? $regionDefault;

        // Categories
        $catHotel = Category::firstOrCreate(
            ['slug' => 'hotel-resort', 'service_type_id' => $accommType->id],
            ['name' => 'Hotel & Resort', 'service_type_id' => $accommType->id]
        );
        $catVilla = Category::firstOrCreate(
            ['slug' => 'villa-glamping', 'service_type_id' => $accommType->id],
            ['name' => 'Vila & Glamping', 'service_type_id' => $accommType->id]
        );
        $catHomestay = Category::firstOrCreate(
            ['slug' => 'homestay-guesthouse', 'service_type_id' => $accommType->id],
            ['name' => 'Homestay & Guesthouse', 'service_type_id' => $accommType->id]
        );

        $accommodationsData = [
            [
                'mitra' => [
                    'slug' => 'guci-pine-resort',
                    'legal_name' => 'PT Guci Pine Resort Indonesia',
                    'display_name' => 'PT Guci Pine Resort Indonesia',
                    'contact_email' => 'pineresort@example.test',
                    'contact_phone' => '081234567890',
                    'address' => 'Jl. Raya Guci Km 5, Bumijawa, Tegal',
                    'region_id' => $regionGuci->id,
                ],
                'media' => [
                    'object_key' => 'media/guci_pine_resort.jpg',
                    'original_name' => 'guci_pine_resort.jpg',
                ],
                'entity' => [
                    'name' => "d'Pine Resort & Villa Guci",
                    'slug' => 'dpine-resort-villa-guci',
                    'category_id' => $catVilla->id,
                    'region_id' => $regionGuci->id,
                    'description' => 'Resort mewah bernuansa hutan pinus di lereng Gunung Slamet dengan pemandian air panas pribadi, udara sejuk pegunungan, dan fasilitas villa eksklusif.',
                    'address' => 'Kawasan Wisata Hutan Pinus Guci, Bumijawa, Kabupaten Tegal',
                    'rating_average' => 4.8,
                    'rating_count' => 142,
                    'is_featured' => true,
                ],
                'accomm' => [
                    'property_type' => 'resort',
                    'star_rating' => 4,
                    'check_in_time' => '14:00:00',
                    'check_out_time' => '12:00:00',
                ],
                'rooms' => [
                    [
                        'name' => 'Executive Pine Chalet',
                        'sku' => 'PINE-CHALET',
                        'price' => 750000,
                        'room_type' => 'chalet',
                        'total_units' => 8,
                        'capacity_adults' => 2,
                        'capacity_children' => 1,
                        'description' => 'Kamar nuansa kayu alami dengan pemandangan langsung ke hutan pinus Guci.',
                    ],
                    [
                        'name' => 'Luxury Pine Villa 2-Bedroom',
                        'sku' => 'PINE-VILLA2B',
                        'price' => 1350000,
                        'room_type' => 'villa',
                        'total_units' => 4,
                        'capacity_adults' => 4,
                        'capacity_children' => 2,
                        'description' => 'Villa 2 kamar tidur lengkap dengan private warm pool dan balkon luas.',
                    ],
                ],
            ],
            [
                'mitra' => [
                    'slug' => 'diana-hospitality-tegal',
                    'legal_name' => 'PT Diana Hospitality Tegal',
                    'display_name' => 'PT Diana Hospitality Tegal',
                    'contact_email' => 'granddiana@example.test',
                    'contact_phone' => '081398765432',
                    'address' => 'Jl. A. Yani No. 12, Slawi, Tegal',
                    'region_id' => $regionSlawi->id,
                ],
                'media' => [
                    'object_key' => 'media/grand_diana_hotel.jpg',
                    'original_name' => 'grand_diana_hotel.jpg',
                ],
                'entity' => [
                    'name' => 'Hotel Grand Diana Slawi',
                    'slug' => 'hotel-grand-diana-slawi',
                    'category_id' => $catHotel->id,
                    'region_id' => $regionSlawi->id,
                    'description' => 'Hotel modern bintang 4 terbaik di pusat Kota Slawi dengan fasilitas infinity pool, ballroom, restoran internasional, dan akses mudah ke pusat pemerintahan Tegal.',
                    'address' => 'Jl. A. Yani No. 12, Procot, Slawi, Kabupaten Tegal',
                    'rating_average' => 4.7,
                    'rating_count' => 218,
                    'is_featured' => true,
                ],
                'accomm' => [
                    'property_type' => 'hotel',
                    'star_rating' => 4,
                    'check_in_time' => '14:00:00',
                    'check_out_time' => '12:00:00',
                ],
                'rooms' => [
                    [
                        'name' => 'Superior Twin Room',
                        'sku' => 'DIANA-SUPERIOR',
                        'price' => 550000,
                        'room_type' => 'superior',
                        'total_units' => 15,
                        'capacity_adults' => 2,
                        'capacity_children' => 1,
                        'description' => 'Kamar modern dengan 2 tempat tidur single, smart TV, dan AC dingin.',
                    ],
                    [
                        'name' => 'Grand Executive Suite City View',
                        'sku' => 'DIANA-SUITE',
                        'price' => 880000,
                        'room_type' => 'suite',
                        'total_units' => 6,
                        'capacity_adults' => 2,
                        'capacity_children' => 2,
                        'description' => 'Suite mewah dengan ruang tamu terpisah dan pemandangan keindahan lanskap Kota Slawi.',
                    ],
                ],
            ],
            [
                'mitra' => [
                    'slug' => 'kancil-hill-tegal',
                    'legal_name' => 'CV Pesona Kancil Hill Tegal',
                    'display_name' => 'CV Pesona Kancil Hill Tegal',
                    'contact_email' => 'kancilhill@example.test',
                    'contact_phone' => '081512344321',
                    'address' => 'Desa Tuwel, Bumijawa, Tegal',
                    'region_id' => $regionGuci->id,
                ],
                'media' => [
                    'object_key' => 'media/kancil_hill_glamping.jpg',
                    'original_name' => 'kancil_hill_glamping.jpg',
                ],
                'entity' => [
                    'name' => 'Kancil Hill Cottage & Glamping',
                    'slug' => 'kancil-hill-glamping-guci',
                    'category_id' => $catVilla->id,
                    'region_id' => $regionGuci->id,
                    'description' => 'Penginapan konsep Glamping Geodesic Dome dan Wooden Cottage instagramable berlatar bukit hijau Guci, cocok untuk pemuda dan keluarga.',
                    'address' => 'Bukit Kancil, Tuwel, Bumijawa, Kabupaten Tegal',
                    'rating_average' => 4.9,
                    'rating_count' => 96,
                    'is_featured' => true,
                ],
                'accomm' => [
                    'property_type' => 'glamping',
                    'star_rating' => 3,
                    'check_in_time' => '14:00:00',
                    'check_out_time' => '11:00:00',
                ],
                'rooms' => [
                    [
                        'name' => 'Luxury Geodesic Dome Glamping',
                        'sku' => 'KANCIL-DOME',
                        'price' => 650000,
                        'room_type' => 'glamping',
                        'total_units' => 10,
                        'capacity_adults' => 2,
                        'capacity_children' => 1,
                        'description' => 'Tenda kubah transparan futuristik ber-AC lengkap dengan private bathroom.',
                    ],
                    [
                        'name' => 'Family Wooden Cottage',
                        'sku' => 'KANCIL-COTTAGE',
                        'price' => 950000,
                        'room_type' => 'cottage',
                        'total_units' => 5,
                        'capacity_adults' => 4,
                        'capacity_children' => 2,
                        'description' => 'Kabin kayu keluarga 2 lantai dengan fasilitas perlengkapan BBQ dan tempat perapian.',
                    ],
                ],
            ],
            [
                'mitra' => [
                    'slug' => 'carlita-homestay',
                    'legal_name' => 'Pokdarwis Carlita Homestay Slawi',
                    'display_name' => 'Pokdarwis Carlita Homestay Slawi',
                    'contact_email' => 'carlitahomestay@example.test',
                    'contact_phone' => '085711223344',
                    'address' => 'Jl. Dr. Soetomo No. 45, Slawi, Tegal',
                    'region_id' => $regionSlawi->id,
                ],
                'media' => [
                    'object_key' => 'media/carlita_homestay.jpg',
                    'original_name' => 'carlita_homestay.jpg',
                ],
                'entity' => [
                    'name' => 'Homestay Syariah Carlita Slawi',
                    'slug' => 'homestay-syariah-carlita-slawi',
                    'category_id' => $catHomestay->id,
                    'region_id' => $regionSlawi->id,
                    'description' => 'Homestay bersih, bersahabat, dan nyaman berkonsep syariah. Pilihan ideal dan ekonomis bagi wisatawan rombongan maupun perjalan dinas.',
                    'address' => 'Jl. Dr. Soetomo No. 45, Kalisapu, Slawi, Kabupaten Tegal',
                    'rating_average' => 4.6,
                    'rating_count' => 75,
                    'is_featured' => false,
                ],
                'accomm' => [
                    'property_type' => 'homestay',
                    'star_rating' => 2,
                    'check_in_time' => '13:00:00',
                    'check_out_time' => '12:00:00',
                ],
                'rooms' => [
                    [
                        'name' => 'Standard Double Room',
                        'sku' => 'CARLITA-STD',
                        'price' => 250000,
                        'room_type' => 'standard',
                        'total_units' => 8,
                        'capacity_adults' => 2,
                        'capacity_children' => 1,
                        'description' => 'Kamar bersih dengan kasur springbed queen, Wi-Fi gratis, dan sarapan tegalan.',
                    ],
                    [
                        'name' => 'Family Room 3 Bed',
                        'sku' => 'CARLITA-FAM',
                        'price' => 420000,
                        'room_type' => 'family',
                        'total_units' => 4,
                        'capacity_adults' => 4,
                        'capacity_children' => 2,
                        'description' => 'Kamar keluarga dengan 3 bed nyaman dan kamar mandi dalam.',
                    ],
                ],
            ],
        ];

        foreach ($accommodationsData as $data) {
            // 1. Create / Update Mitra
            $mitra = Mitra::updateOrCreate(
                ['slug' => $data['mitra']['slug']],
                [
                    'owner_user_id' => $ownerUser->id,
                    'region_id' => $data['mitra']['region_id'],
                    'legal_name' => $data['mitra']['legal_name'],
                    'display_name' => $data['mitra']['display_name'],
                    'status' => 'active',
                    'approved_at' => now(),
                    'contact_email' => $data['mitra']['contact_email'],
                    'contact_phone' => $data['mitra']['contact_phone'],
                    'address' => $data['mitra']['address'],
                ]
            );

            // Enable feature
            MitraFeature::updateOrCreate(
                ['mitra_id' => $mitra->id, 'service_type_id' => $accommType->id],
                ['status' => 'enabled', 'enabled_at' => now()]
            );

            // 2. Media Asset
            $media = MediaAsset::firstOrCreate(
                ['object_key' => $data['media']['object_key']],
                [
                    'owner_user_id' => $ownerUser->id,
                    'disk' => 'local',
                    'original_name' => $data['media']['original_name'],
                    'mime_type' => 'image/jpeg',
                    'size_bytes' => 120000,
                    'checksum_sha256' => hash('sha256', $data['media']['object_key']),
                    'visibility' => 'public',
                    'purpose' => 'catalog_cover',
                    'status' => 'attached',
                    'uploaded_at' => now(),
                ]
            );

            // 3. Catalog Entity
            $entity = CatalogEntity::updateOrCreate(
                ['slug' => $data['entity']['slug']],
                [
                    'mitra_id' => $mitra->id,
                    'service_type_id' => $accommType->id,
                    'category_id' => $data['entity']['category_id'],
                    'region_id' => $data['entity']['region_id'],
                    'name' => $data['entity']['name'],
                    'description' => $data['entity']['description'],
                    'address' => $data['entity']['address'],
                    'status' => 'published',
                    'is_featured' => $data['entity']['is_featured'],
                    'rating_average' => $data['entity']['rating_average'],
                    'rating_count' => $data['entity']['rating_count'],
                    'published_at' => now(),
                ]
            );

            $entity->media()->syncWithoutDetaching([$media->id => ['role' => 'cover', 'sort_order' => 1]]);

            // 4. Accommodation Property
            $accomm = Accommodation::updateOrCreate(
                ['catalog_entity_id' => $entity->id],
                [
                    'property_type' => $data['accomm']['property_type'],
                    'star_rating' => $data['accomm']['star_rating'],
                    'check_in_time' => $data['accomm']['check_in_time'],
                    'check_out_time' => $data['accomm']['check_out_time'],
                ]
            );

            // 5. Offers & Rooms
            foreach ($data['rooms'] as $roomData) {
                $offer = CatalogOffer::updateOrCreate(
                    ['catalog_entity_id' => $entity->id, 'sku' => $roomData['sku']],
                    [
                        'mitra_id' => $mitra->id,
                        'offer_type' => 'room',
                        'name' => $roomData['name'],
                        'currency' => 'IDR',
                        'price' => $roomData['price'],
                        'status' => 'active',
                    ]
                );

                AccommodationRoom::updateOrCreate(
                    ['accommodation_id' => $accomm->id, 'catalog_offer_id' => $offer->id],
                    [
                        'name' => $roomData['name'],
                        'description' => $roomData['description'],
                        'room_type' => $roomData['room_type'],
                        'total_units' => $roomData['total_units'],
                        'capacity_adults' => $roomData['capacity_adults'],
                        'capacity_children' => $roomData['capacity_children'],
                        'min_stay_nights' => 1,
                        'max_stay_nights' => 14,
                        'status' => 'active',
                    ]
                );
            }
        }
    }
}
