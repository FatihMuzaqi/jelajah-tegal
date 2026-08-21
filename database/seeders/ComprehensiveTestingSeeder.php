<?php

namespace Database\Seeders;

use App\Enums\CulinaryReservationStatus;
use App\Enums\RenterDocumentStatus;
use App\Models\Accommodation;
use App\Models\AccommodationRoom;
use App\Models\AuditLog;
use App\Models\CatalogEntity;
use App\Models\CatalogOffer;
use App\Models\Category;
use App\Models\CulinaryMenuCategory;
use App\Models\CulinaryMenuItem;
use App\Models\CulinaryReservation;
use App\Models\CulinaryTableSlot;
use App\Models\CulinaryVenue;
use App\Models\Event;
use App\Models\EventSchedule;
use App\Models\EventTicket;
use App\Models\EventTicketType;
use App\Models\EventTicketValidationLog;
use App\Models\Facility;
use App\Models\FeatureFlag;
use App\Models\GatekeeperAssignment;
use App\Models\IdempotencyKey;
use App\Models\LedgerAccount;
use App\Models\LedgerJournal;
use App\Models\LedgerLine;
use App\Models\MediaAsset;
use App\Models\Mitra;
use App\Models\MitraBalance;
use App\Models\MitraBankAccount;
use App\Models\MitraFeature;
use App\Models\MitraFeatureRequest;
use App\Models\MitraKycDocument;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Rental;
use App\Models\RentalBooking;
use App\Models\RentalVehicle;
use App\Models\RenterDocument;
use App\Models\Review;
use App\Models\ServiceType;
use App\Models\Ticket;
use App\Models\TourismDestination;
use App\Models\TourismTicketPackage;
use App\Models\User;
use App\Models\Voucher;
use App\Models\VoucherClaim;
use App\Models\VoucherUsage;
use App\Models\WithdrawalClaim;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ComprehensiveTestingSeeder extends Seeder
{
    public function run(): void
    {
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        // 1. Service Types & Permissions Seeders
        $this->call([
            ServiceTypeSeeder::class,
            RolesAndPermissionsSeeder::class,
            FeatureFlagSeeder::class,
        ]);

        $tourismType = ServiceType::where('code', 'tourism')->first();
        $accommType = ServiceType::where('code', 'accommodation')->first();
        $culinaryType = ServiceType::where('code', 'culinary')->first();
        $eventType = ServiceType::where('code', 'event')->first();
        $rentalType = ServiceType::where('code', 'rental')->first();

        // 2. Akun Pengguna Utama Testing (Password: 'password')
        setPermissionsTeamId(null);
        $accounts = [
            ['email' => 'admin@example.test', 'name' => 'Administrator Platform', 'role' => 'admin'],
            ['email' => 'superadmin@example.test', 'name' => 'Super Admin System', 'role' => 'super-admin'],
            ['email' => 'supervisor.dinas@tegalkab.go.id', 'name' => 'Dr. H. Ahmad Rasyid, M.Si (Kepala Bidang Pariwisata)', 'role' => 'dinas-supervisor'],
            ['email' => 'admin1@jelajahtegal.com', 'name' => 'Admin 1 (Budi Santoso)', 'role' => 'admin'],
            ['email' => 'admin2@jelajahtegal.com', 'name' => 'Admin 2 (Siti Rahmawati)', 'role' => 'admin'],
            ['email' => 'owner@example.test', 'name' => 'Budi Susanto (Owner Utama)', 'role' => 'mitra-owner'],
            ['email' => 'staff@example.test', 'name' => 'Siti Rahma (Staff Mitra)', 'role' => 'mitra-staff'],
            ['email' => 'gatekeeper@example.test', 'name' => 'Joko Gatekeeper (Petugas QR)', 'role' => 'gatekeeper'],
            ['email' => 'consumer@example.test', 'name' => 'Ahmad Pelanggan (Consumer)', 'role' => 'consumer'],
            ['email' => 'consumer2@example.test', 'name' => 'Rina Wisatawan (Consumer 2)', 'role' => 'consumer'],
        ];

        $users = [];
        foreach ($accounts as $acc) {
            $user = User::updateOrCreate(
                ['email' => $acc['email']],
                ['name' => $acc['name'], 'status' => 'active', 'email_verified_at' => now()]
            );

            $user->credential()->updateOrCreate(
                ['user_id' => $user->id],
                ['password_hash' => Hash::make('password')]
            );

            $user->profile()->updateOrCreate(
                ['user_id' => $user->id],
                ['notification_preferences' => ['in_app' => true, 'email' => true]]
            );

            if (! in_array($acc['role'], ['mitra-owner', 'mitra-staff'])) {
                $user->syncRoles([$acc['role']]);
            }
            $users[$acc['email']] = $user;
        }

        $adminUser = $users['admin@example.test'];
        $ownerUser = $users['owner@example.test'];
        $staffUser = $users['staff@example.test'];
        $gatekeeperUser = $users['gatekeeper@example.test'];
        $consumerUser = $users['consumer@example.test'];

        // 2.5 Master Data 21 Wilayah / Lokasi Kabupaten dan Kota Tegal
        $tegalRegions = [
            ['code' => 'TGL-KAB-SLAWI', 'name' => 'Slawi (Kabupaten Tegal)', 'level' => 'district'],
            ['code' => 'TGL-KAB-GUCI', 'name' => 'Guci / Bumijawa (Kabupaten Tegal)', 'level' => 'district'],
            ['code' => 'TGL-KAB-ADIWERNA', 'name' => 'Adiwerna (Kabupaten Tegal)', 'level' => 'district'],
            ['code' => 'TGL-KAB-LEBAKSIU', 'name' => 'Lebaksiu (Kabupaten Tegal)', 'level' => 'district'],
            ['code' => 'TGL-KAB-PANGKAH', 'name' => 'Pangkah / Cacaban (Kabupaten Tegal)', 'level' => 'district'],
            ['code' => 'TGL-KAB-KRAMAT', 'name' => 'Kramat / Purin (Kabupaten Tegal)', 'level' => 'district'],
            ['code' => 'TGL-KAB-BOJONG', 'name' => 'Bojong / Prabalaba (Kabupaten Tegal)', 'level' => 'district'],
            ['code' => 'TGL-KAB-BALAPULANG', 'name' => 'Balapulang (Kabupaten Tegal)', 'level' => 'district'],
            ['code' => 'TGL-KAB-DUKUHTURI', 'name' => 'Dukuhturi (Kabupaten Tegal)', 'level' => 'district'],
            ['code' => 'TGL-KAB-DUKUHWARU', 'name' => 'Dukuhwaru (Kabupaten Tegal)', 'level' => 'district'],
            ['code' => 'TGL-KAB-TALANG', 'name' => 'Talang (Kabupaten Tegal)', 'level' => 'district'],
            ['code' => 'TGL-KAB-TARUB', 'name' => 'Tarub (Kabupaten Tegal)', 'level' => 'district'],
            ['code' => 'TGL-KAB-SURADADI', 'name' => 'Suradadi (Kabupaten Tegal)', 'level' => 'district'],
            ['code' => 'TGL-KAB-WARUREJA', 'name' => 'Warureja (Kabupaten Tegal)', 'level' => 'district'],
            ['code' => 'TGL-KAB-MARGASARI', 'name' => 'Margasari (Kabupaten Tegal)', 'level' => 'district'],
            ['code' => 'TGL-KAB-JATINEGARA', 'name' => 'Jatinegara (Kabupaten Tegal)', 'level' => 'district'],
            ['code' => 'TGL-KAB-KEDUNGBANTENG', 'name' => 'Kedungbanteng (Kabupaten Tegal)', 'level' => 'district'],
            ['code' => 'TGL-KOTA-TIMUR', 'name' => 'Tegal Timur (Alun-Alun & Pusat Kota Tegal)', 'level' => 'district'],
            ['code' => 'TGL-KOTA-BARAT', 'name' => 'Tegal Barat / Muarareja (Kota Tegal)', 'level' => 'district'],
            ['code' => 'TGL-KOTA-SELATAN', 'name' => 'Tegal Selatan / Kalinyamat (Kota Tegal)', 'level' => 'district'],
            ['code' => 'TGL-KOTA-MARGADANA', 'name' => 'Margadana / Komodo (Kota Tegal)', 'level' => 'district'],
        ];

        $firstRegion = null;
        foreach ($tegalRegions as $reg) {
            $r = \App\Models\Region::updateOrCreate(
                ['code' => $reg['code']],
                ['name' => $reg['name'], 'level' => $reg['level'], 'deleted_at' => null]
            );
            $firstRegion ??= $r;
        }

        // 3. Tenant Mitra Utama Tegal & Memberships
        $mitra = Mitra::updateOrCreate(
            ['slug' => 'mitra-utama-tegal'],
            [
                'owner_user_id' => $ownerUser->id,
                'region_id' => $firstRegion?->id,
                'legal_name' => 'PT Lokantara Utama Tegal',
                'display_name' => 'Mitra Wisata Utama Tegal',
                'status' => 'active',
                'approved_at' => now(),
                'contact_email' => 'owner@example.test',
                'contact_phone' => '081234567890',
                'address' => 'Jl. Pancasila No. 1, Kota Tegal',
            ]
        );

        $mitra->members()->updateOrCreate(['user_id' => $ownerUser->id], ['status' => 'active', 'joined_at' => now()]);
        $mitra->members()->updateOrCreate(['user_id' => $staffUser->id], ['status' => 'active', 'joined_at' => now()]);
        $gkMember = $mitra->members()->updateOrCreate(['user_id' => $gatekeeperUser->id], ['status' => 'active', 'joined_at' => now()]);

        setPermissionsTeamId($mitra->id);
        $ownerUser->syncRoles(['mitra-owner']);
        $staffUser->syncRoles(['mitra-staff']);
        $gatekeeperUser->syncRoles(['gatekeeper']);
        setPermissionsTeamId(null);

        GatekeeperAssignment::updateOrCreate(
            ['mitra_id' => $mitra->id, 'member_id' => $gkMember->id],
            [
                'scope_type' => 'mitra',
                'valid_from' => now()->subDay(),
                'valid_until' => now()->addYear(),
                'assigned_by' => $ownerUser->id,
                'revoked_at' => null,
            ]
        );

        // 4. Media Assets Dummy
        $mediaPai = MediaAsset::updateOrCreate(
            ['disk' => 'local', 'object_key' => 'media/pai_cover.jpg'],
            [
                'owner_user_id' => $ownerUser->id,
                'original_name' => 'pai_cover.jpg',
                'mime_type' => 'image/jpeg',
                'size_bytes' => 256000,
                'checksum_sha256' => hash('sha256', 'media_pai_content'),
                'visibility' => 'public',
                'purpose' => 'catalog_cover',
                'status' => 'attached',
                'uploaded_at' => now(),
            ]
        );

        $mediaGuci = MediaAsset::updateOrCreate(
            ['disk' => 'local', 'object_key' => 'media/guci_cover.jpg'],
            [
                'owner_user_id' => $ownerUser->id,
                'original_name' => 'guci_cover.jpg',
                'mime_type' => 'image/jpeg',
                'size_bytes' => 312000,
                'checksum_sha256' => hash('sha256', 'media_guci_content'),
                'visibility' => 'public',
                'purpose' => 'catalog_cover',
                'status' => 'attached',
                'uploaded_at' => now(),
            ]
        );

        $mediaCurug = MediaAsset::updateOrCreate(
            ['disk' => 'local', 'object_key' => 'media/curug_putri_cover.jpg'],
            [
                'owner_user_id' => $ownerUser->id,
                'original_name' => 'curug_putri_cover.jpg',
                'mime_type' => 'image/jpeg',
                'size_bytes' => 312000,
                'checksum_sha256' => hash('sha256', 'media_curug_content'),
                'visibility' => 'public',
                'purpose' => 'catalog_cover',
                'status' => 'attached',
                'uploaded_at' => now(),
            ]
        );

        $mediaBeko = MediaAsset::updateOrCreate(
            ['disk' => 'local', 'object_key' => 'media/danau_beko_cover.jpg'],
            [
                'owner_user_id' => $ownerUser->id,
                'original_name' => 'danau_beko_cover.jpg',
                'mime_type' => 'image/jpeg',
                'size_bytes' => 312000,
                'checksum_sha256' => hash('sha256', 'media_beko_content'),
                'visibility' => 'public',
                'purpose' => 'catalog_cover',
                'status' => 'attached',
                'uploaded_at' => now(),
            ]
        );

        $mediaBahari = MediaAsset::updateOrCreate(
            ['disk' => 'local', 'object_key' => 'media/bahari_inn_cover.jpg'],
            [
                'owner_user_id' => $ownerUser->id,
                'original_name' => 'bahari_inn_cover.jpg',
                'mime_type' => 'image/jpeg',
                'size_bytes' => 312000,
                'checksum_sha256' => hash('sha256', 'media_bahari_content'),
                'visibility' => 'public',
                'purpose' => 'catalog_cover',
                'status' => 'attached',
                'uploaded_at' => now(),
            ]
        );

        $mediaSate = MediaAsset::updateOrCreate(
            ['disk' => 'local', 'object_key' => 'media/sate_wendy_cover.jpg'],
            [
                'owner_user_id' => $ownerUser->id,
                'original_name' => 'sate_wendy_cover.jpg',
                'mime_type' => 'image/jpeg',
                'size_bytes' => 312000,
                'checksum_sha256' => hash('sha256', 'media_sate_content'),
                'visibility' => 'public',
                'purpose' => 'catalog_cover',
                'status' => 'attached',
                'uploaded_at' => now(),
            ]
        );

        // 5. KYC & Rekening Bank Mitra
        $mitra->kycDocuments()->updateOrCreate(
            ['document_type' => 'ktp'],
            [
                'media_asset_id' => $mediaPai->id,
                'submitted_by' => $ownerUser->id,
                'status' => 'approved',
                'reviewed_at' => now(),
                'reviewed_by' => $adminUser->id,
            ]
        );

        $mitra->bankAccounts()->updateOrCreate(
            ['account_fingerprint' => hash('sha256', '1234567890')],
            [
                'bank_code' => 'BCA',
                'account_number_encrypted' => encrypt('1234567890'),
                'account_name_encrypted' => encrypt('PT Lokantara Utama Tegal'),
                'is_primary' => true,
                'status' => 'verified',
                'verified_by' => $adminUser->id,
                'verified_at' => now(),
            ]
        );

        // Fitur Bisnis Mitra
        foreach ([$tourismType, $accommType, $culinaryType, $eventType, $rentalType] as $type) {
            if ($type) {
                MitraFeature::updateOrCreate(['mitra_id' => $mitra->id, 'service_type_id' => $type->id], ['status' => 'enabled', 'enabled_at' => now()]);
            }
        }

        MitraFeatureRequest::updateOrCreate(
            ['mitra_id' => $mitra->id, 'service_type_id' => $tourismType->id],
            [
                'requested_by' => $ownerUser->id,
                'status' => 'approved',
                'reason' => 'Pengajuan fitur wisata disetujui',
                'reviewed_at' => now(),
                'reviewed_by' => $adminUser->id,
            ]
        );

        // Mitra 2 (Non-Dinas): Mitra Usaha Achmad Fatich Muzaqi
        $fatihUser = User::updateOrCreate(
            ['email' => 'fatih.muzaqi123@gmail.com'],
            ['name' => 'Achmad Fatich Muzaqi', 'status' => 'active', 'email_verified_at' => now()]
        );
        $fatihUser->credential()->updateOrCreate(['user_id' => $fatihUser->id], ['password_hash' => Hash::make('password')]);
        $fatihUser->profile()->updateOrCreate(['user_id' => $fatihUser->id], ['notification_preferences' => ['in_app' => true, 'email' => true]]);

        $mitraFatih = Mitra::updateOrCreate(
            ['slug' => 'achmad-fatich-muzaqi'],
            [
                'category' => 'non_dinas',
                'owner_user_id' => $fatihUser->id,
                'region_id' => $firstRegion?->id,
                'legal_name' => 'PT Muzaqi Berkah Wisata',
                'display_name' => 'Mitra Usaha Achmad Fatich Muzaqi',
                'status' => 'active',
                'approved_at' => now(),
                'contact_email' => 'fatih.muzaqi123@gmail.com',
                'contact_phone' => '081234567804',
                'address' => 'Jl. Pemuda No. 10, Tegal',
            ]
        );

        $mitraFatih->members()->updateOrCreate(['user_id' => $fatihUser->id], ['status' => 'active', 'joined_at' => now()]);
        setPermissionsTeamId($mitraFatih->id);
        $fatihUser->syncRoles(['mitra-owner']);
        setPermissionsTeamId(null);

        foreach ([$tourismType, $accommType, $culinaryType, $eventType, $rentalType] as $type) {
            if ($type) {
                MitraFeature::updateOrCreate(['mitra_id' => $mitraFatih->id, 'service_type_id' => $type->id], ['status' => 'enabled', 'enabled_at' => now()]);
            }
        }

        // 6. DOMAIN TOURISM (Katalog Destinasi Wisata)
        $categoryTourism = Category::firstOrCreate([
            'service_type_id' => $tourismType->id,
            'slug' => 'wisata-alam-pantai',
        ], [
            'name' => 'Wisata Alam & Pantai',
        ]);

        $regionGuci = \App\Models\Region::where('code', 'TGL-KAB-GUCI')->first() ?? $firstRegion;

        // 6a. Guci Hot Spring
        $entityGuci = CatalogEntity::create([
            'mitra_id' => $mitra->id,
            'service_type_id' => $tourismType->id,
            'category_id' => $categoryTourism->id,
            'region_id' => $regionGuci->id,
            'name' => 'Guci Hot Spring',
            'slug' => 'guci-hot-spring',
            'description' => 'Pemandian air panas alami dengan pemandangan indah dan udara sejuk pegunungan.',
            'address' => 'Kawasan Wisata Guci, Bumijawa, Tegal',
            'status' => 'published',
            'is_featured' => true,
            'rating_average' => 4.8,
            'rating_count' => 128,
            'published_at' => now(),
        ]);
        $entityGuci->media()->syncWithoutDetaching([$mediaGuci->id => ['role' => 'cover', 'sort_order' => 1]]);
        
        $destGuci = TourismDestination::create([
            'catalog_entity_id' => $entityGuci->id,
            'destination_type' => 'hot_spring',
            'visit_duration_minutes' => 180,
            'badge' => 'Terfavorit',
            'is_hidden_gem' => false,
        ]);
        $offerGuci = CatalogOffer::create([
            'catalog_entity_id' => $entityGuci->id,
            'mitra_id' => $mitra->id,
            'offer_type' => 'ticket',
            'name' => 'Tiket Masuk Utama Guci',
            'sku' => 'GUC-REGULER',
            'currency' => 'IDR',
            'price' => 25000,
            'status' => 'published',
        ]);
        TourismTicketPackage::create([
            'tourism_destination_id' => $destGuci->id,
            'catalog_offer_id' => $offerGuci->id,
            'name' => 'Tiket Reguler Pemandian Guci',
            'quota_per_day' => 1000,
        ]);

        // 6b. Pantai Alam Indah Tegal
        $entityTourism = CatalogEntity::create([
            'mitra_id' => $mitra->id,
            'service_type_id' => $tourismType->id,
            'category_id' => $categoryTourism->id,
            'region_id' => $firstRegion->id,
            'name' => 'Pantai Alam Indah',
            'slug' => 'pantai-alam-indah-tegal',
            'description' => 'Pantai indah dengan pasir luas dan berbagai wahana menarik di pesisir Kota Tegal.',
            'address' => 'Jl. Sangir, Mintaragen, Kota Tegal',
            'status' => 'published',
            'is_featured' => true,
            'rating_average' => 4.6,
            'rating_count' => 95,
            'published_at' => now(),
        ]);
        $entityTourism->media()->syncWithoutDetaching([$mediaPai->id => ['role' => 'cover', 'sort_order' => 1]]);

        $destination = TourismDestination::create([
            'catalog_entity_id' => $entityTourism->id,
            'destination_type' => 'beach',
            'visit_duration_minutes' => 120,
            'badge' => 'Terpopuler',
            'is_hidden_gem' => false,
        ]);
        $offerTourism = CatalogOffer::create([
            'catalog_entity_id' => $entityTourism->id,
            'mitra_id' => $mitra->id,
            'offer_type' => 'ticket',
            'name' => 'Tiket Masuk Reguler PAI',
            'sku' => 'PAI-REGULER',
            'currency' => 'IDR',
            'price' => 15000,
            'status' => 'published',
        ]);
        TourismTicketPackage::create([
            'tourism_destination_id' => $destination->id,
            'catalog_offer_id' => $offerTourism->id,
            'name' => 'Tiket Reguler Masuk PAI',
            'quota_per_day' => 500,
        ]);

        // 6c. Curug Putri Bumijawa
        $entityCurug = CatalogEntity::create([
            'mitra_id' => $mitra->id,
            'service_type_id' => $tourismType->id,
            'category_id' => $categoryTourism->id,
            'region_id' => $regionGuci->id,
            'name' => 'Curug Putri',
            'slug' => 'curug-putri-bumijawa',
            'description' => 'Air terjun yang menawan dengan suasana alami yang asri di kawasan lereng pegunungan.',
            'address' => 'Dukuh Dukuhtengah, Bumijawa, Tegal',
            'status' => 'published',
            'is_featured' => true,
            'rating_average' => 4.7,
            'rating_count' => 84,
            'published_at' => now(),
        ]);
        $entityCurug->media()->syncWithoutDetaching([$mediaCurug->id => ['role' => 'cover', 'sort_order' => 1]]);
        
        $destCurug = TourismDestination::create([
            'catalog_entity_id' => $entityCurug->id,
            'destination_type' => 'waterfall',
            'visit_duration_minutes' => 90,
            'badge' => 'Asri & Alami',
            'is_hidden_gem' => true,
        ]);
        $offerCurug = CatalogOffer::create([
            'catalog_entity_id' => $entityCurug->id,
            'mitra_id' => $mitra->id,
            'offer_type' => 'ticket',
            'name' => 'Tiket Masuk Curug Putri',
            'sku' => 'CRG-PUTRI',
            'currency' => 'IDR',
            'price' => 10000,
            'status' => 'published',
        ]);
        TourismTicketPackage::create([
            'tourism_destination_id' => $destCurug->id,
            'catalog_offer_id' => $offerCurug->id,
            'name' => 'Tiket Reguler Curug Putri',
            'quota_per_day' => 300,
        ]);

        // 6d. Danau Beko Margasari
        $entityBeko = CatalogEntity::create([
            'mitra_id' => $mitra->id,
            'service_type_id' => $tourismType->id,
            'category_id' => $categoryTourism->id,
            'region_id' => $firstRegion->id,
            'name' => 'Danau Beko',
            'slug' => 'danau-beko-margasari',
            'description' => 'Tempat rekreasi keluarga dengan danau buatan tebing kapur yang unik dan menyenangkan.',
            'address' => 'Desa Jatilaba, Margasari, Tegal',
            'status' => 'published',
            'is_featured' => true,
            'rating_average' => 4.5,
            'rating_count' => 62,
            'published_at' => now(),
        ]);
        $entityBeko->media()->syncWithoutDetaching([$mediaBeko->id => ['role' => 'cover', 'sort_order' => 1]]);

        $destBeko = TourismDestination::create([
            'catalog_entity_id' => $entityBeko->id,
            'destination_type' => 'lake',
            'visit_duration_minutes' => 90,
            'badge' => 'Rekreasi',
            'is_hidden_gem' => false,
        ]);
        $offerBeko = CatalogOffer::create([
            'catalog_entity_id' => $entityBeko->id,
            'mitra_id' => $mitra->id,
            'offer_type' => 'ticket',
            'name' => 'Tiket Masuk Danau Beko',
            'sku' => 'BKO-REGULER',
            'currency' => 'IDR',
            'price' => 10000,
            'status' => 'published',
        ]);
        TourismTicketPackage::create([
            'tourism_destination_id' => $destBeko->id,
            'catalog_offer_id' => $offerBeko->id,
            'name' => 'Tiket Reguler Danau Beko',
            'quota_per_day' => 400,
        ]);

        // 7. DOMAIN ACCOMMODATION (Hotel Bahari Inn Tegal)
        $entityAccomm = CatalogEntity::create([
            'mitra_id' => $mitra->id,
            'service_type_id' => $accommType->id,
            'name' => 'Hotel Bahari Inn Tegal',
            'slug' => 'hotel-bahari-inn-tegal',
            'description' => 'Akomodasi nyaman bintang 3 di pusat Kota Tegal.',
            'address' => 'Jl. Dr. Wahidin Sudirohusodo No. 1, Kota Tegal',
            'status' => 'published',
            'is_featured' => true,
            'rating_average' => 4.5,
            'published_at' => now(),
        ]);
        $entityAccomm->media()->syncWithoutDetaching([$mediaBahari->id => ['role' => 'cover', 'sort_order' => 1]]);

        $accomm = Accommodation::create([
            'catalog_entity_id' => $entityAccomm->id,
            'property_type' => 'hotel',
            'star_rating' => 3,
            'check_in_time' => '14:00:00',
            'check_out_time' => '12:00:00',
        ]);

        $offerRoom = CatalogOffer::create([
            'catalog_entity_id' => $entityAccomm->id,
            'mitra_id' => $mitra->id,
            'offer_type' => 'room',
            'name' => 'Kamar Deluxe King',
            'sku' => 'BHR-DELUXE',
            'currency' => 'IDR',
            'price' => 450000,
            'status' => 'published',
        ]);

        $roomType = AccommodationRoom::create([
            'accommodation_id' => $accomm->id,
            'catalog_offer_id' => $offerRoom->id,
            'name' => 'Deluxe King Room',
            'room_type' => 'deluxe',
            'total_units' => 10,
            'capacity_adults' => 2,
            'capacity_children' => 1,
            'min_stay_nights' => 1,
            'max_stay_nights' => 14,
            'status' => 'active',
        ]);

        // 8. DOMAIN CULINARY (Sate Kambing Wendy Tegal)
        $entityCulinary = CatalogEntity::create([
            'mitra_id' => $mitra->id,
            'service_type_id' => $culinaryType->id,
            'name' => 'Sate Kambing Muda Wendy Tegal',
            'slug' => 'sate-kambing-muda-wendy-tegal',
            'description' => 'Kuliner khas sate kambing empuk beraroma bumbu khas Tegal.',
            'address' => 'Jl. Letjen Suprapto No. 59, Kota Tegal',
            'status' => 'published',
            'is_featured' => true,
            'rating_average' => 4.9,
            'published_at' => now(),
        ]);
        $entityCulinary->media()->syncWithoutDetaching([$mediaSate->id => ['role' => 'cover', 'sort_order' => 1]]);

        $venue = CulinaryVenue::create([
            'catalog_entity_id' => $entityCulinary->id,
            'venue_type' => 'restaurant',
            'accepts_reservations' => true,
            'phone' => '081234567890',
        ]);

        $menuCat = CulinaryMenuCategory::create(['culinary_venue_id' => $venue->id, 'name' => 'Makanan Utama', 'sort_order' => 1]);
        $menuItem = CulinaryMenuItem::create([
            'culinary_venue_id' => $venue->id,
            'culinary_menu_category_id' => $menuCat->id,
            'name' => 'Sate Kambing Muda 10 Tusuk',
            'description' => 'Sate kambing empuk dengan bumbu kecap pedas gurih khas Tegal',
            'price' => 60000,
            'is_featured' => true,
            'status' => 'active',
        ]);

        $slot = CulinaryTableSlot::create([
            'culinary_venue_id' => $venue->id,
            'service_date' => now()->addDays(2)->toDateString(),
            'starts_at' => '12:00:00',
            'ends_at' => '14:00:00',
            'capacity_tables' => 5,
            'capacity_guests' => 20,
            'status' => 'available',
        ]);

        $reservation = CulinaryReservation::create([
            'reservation_number' => 'RSV-TEGAL-001',
            'culinary_venue_id' => $venue->id,
            'culinary_table_slot_id' => $slot->id,
            'user_id' => $consumerUser->id,
            'party_size' => 4,
            'contact_name' => $consumerUser->name,
            'contact_phone' => '08987654321',
            'status' => CulinaryReservationStatus::Confirmed,
            'decided_by' => $staffUser->id,
            'decided_at' => now(),
        ]);

        // 9. DOMAIN EVENT (Festival Budaya Tegal 2026)
        $entityEvent = CatalogEntity::create([
            'mitra_id' => $mitra->id,
            'service_type_id' => $eventType->id,
            'name' => 'Festival Budaya & Kuliner Tegal 2026',
            'slug' => 'festival-budaya-kuliner-tegal-2026',
            'description' => 'Perayaan tahunan seni, musik, dan beragam kuliner legendaris Tegal.',
            'address' => 'Alun-Alun Kota Tegal',
            'status' => 'published',
            'published_at' => now(),
        ]);

        $event = Event::create([
            'catalog_entity_id' => $entityEvent->id,
            'event_type' => 'festival',
            'venue_name' => 'Alun-Alun Kota Tegal',
            'starts_at' => now()->addDays(5),
            'ends_at' => now()->addDays(7),
        ]);

        EventSchedule::create([
            'event_id' => $event->id,
            'title' => 'Upacara Pembukaan & Pawai Budaya',
            'starts_at' => now()->addDays(5)->setTime(9, 0),
            'ends_at' => now()->addDays(5)->setTime(12, 0),
        ]);

        $offerEvent = CatalogOffer::create([
            'catalog_entity_id' => $entityEvent->id,
            'mitra_id' => $mitra->id,
            'offer_type' => 'event_ticket',
            'name' => 'Tiket Presale Festival Budaya',
            'sku' => 'FST-PRESALE',
            'currency' => 'IDR',
            'price' => 50000,
            'status' => 'published',
        ]);

        $ticketType = EventTicketType::create([
            'event_id' => $event->id,
            'catalog_offer_id' => $offerEvent->id,
            'name' => 'Tiket Presale',
            'quota' => 200,
            'issued_quantity' => 1,
            'reserved_quantity' => 0,
        ]);

        $eventTicket = EventTicket::create([
            'ticket_number' => 'TCK-EVT-001',
            'event_ticket_type_id' => $ticketType->id,
            'mitra_id' => $mitra->id,
            'user_id' => $consumerUser->id,
            'qr_token_hash' => hash('sha256', 'SAMPLE_QR_TOKEN_EVENT'),
            'status' => 'issued',
            'valid_from' => now(),
            'valid_until' => now()->addDays(8),
        ]);

        EventTicketValidationLog::create([
            'event_ticket_id' => $eventTicket->id,
            'gatekeeper_user_id' => $gatekeeperUser->id,
            'result' => 'valid',
            'device_reference' => 'Android POS Scanner 01',
            'validated_at' => now(),
        ]);

        // 10. DOMAIN RENTAL (Rental Motor & Mobil Tegal)
        $entityRental = CatalogEntity::create([
            'mitra_id' => $mitra->id,
            'service_type_id' => $rentalType->id,
            'name' => 'Rental Kendaraan Tegal Express',
            'slug' => 'rental-kendaraan-tegal-express',
            'description' => 'Layanan sewa mobil & motor lepas kunci dengan armada bersih.',
            'address' => 'Jl. AR Hakim No. 12, Kota Tegal',
            'status' => 'published',
            'published_at' => now(),
        ]);

        $vehicle = RentalVehicle::create([
            'catalog_entity_id' => $entityRental->id,
            'vehicle_type' => 'car',
            'brand' => 'Toyota',
            'model' => 'Avanza Veloz',
            'year' => 2024,
            'transmission' => 'automatic',
            'seats' => 7,
            'status' => 'active',
        ]);

        $offerRental = CatalogOffer::create([
            'catalog_entity_id' => $entityRental->id,
            'mitra_id' => $mitra->id,
            'offer_type' => 'vehicle_rental',
            'name' => 'Sewa Toyota Avanza Veloz 2024',
            'sku' => 'RNT-AVANZA',
            'currency' => 'IDR',
            'price' => 350000,
            'status' => 'published',
        ]);

        $rentalRate = \App\Models\RentalRate::create([
            'rental_vehicle_id' => $vehicle->id,
            'catalog_offer_id' => $offerRental->id,
            'drive_mode' => 'self_drive',
            'duration_unit' => 'day',
            'duration_value' => 1,
        ]);

        RenterDocument::create([
            'user_id' => $consumerUser->id,
            'media_asset_id' => $mediaPai->id,
            'document_type' => 'sim_a',
            'document_number' => '123456789012',
            'status' => RenterDocumentStatus::Approved,
            'reviewed_by' => $adminUser->id,
            'reviewed_at' => now(),
        ]);

        RentalBooking::create([
            'booking_number' => 'RNT-BKG-001',
            'rental_vehicle_id' => $vehicle->id,
            'rental_rate_id' => $rentalRate->id,
            'mitra_id' => $mitra->id,
            'user_id' => $consumerUser->id,
            'pickup_at' => now()->addDays(3),
            'return_at' => now()->addDays(5),
            'pickup_location' => 'Jl. AR Hakim No. 12, Kota Tegal',
            'return_location' => 'Jl. AR Hakim No. 12, Kota Tegal',
            'drive_mode' => 'self_drive',
            'unit_price' => 350000,
            'deposit_amount' => 0,
            'total_amount' => 700000,
            'status' => \App\Enums\RentalBookingStatus::Approved,
        ]);

        // 11. COMMERCE & VOUCHER ENGINE
        $voucher = Voucher::create([
            'mitra_id' => $mitra->id,
            'code' => 'DISKONTEGAL',
            'name' => 'Voucher Diskon Spesial Tegal',
            'discount_type' => 'flat',
            'flat_amount' => 20000,
            'minimum_order_amount' => 50000,
            'usage_limit' => 100,
            'used_count' => 1,
            'per_user_limit' => 1,
            'starts_at' => now()->subDay(),
            'ends_at' => now()->addDays(30),
            'status' => \App\Enums\VoucherStatus::Active,
            'created_by' => $ownerUser->id,
        ]);

        $claim = VoucherClaim::create([
            'voucher_id' => $voucher->id,
            'user_id' => $consumerUser->id,
            'status' => 'claimed',
            'claimed_at' => now(),
        ]);

        // 12. TRANSAKSI ORDER & PAYMENT
        $order = Order::create([
            'order_number' => 'ORD-20260807-001',
            'user_id' => $consumerUser->id,
            'mitra_id' => $mitra->id,
            'voucher_id' => $voucher->id,
            'currency' => 'IDR',
            'subtotal' => 50000,
            'admin_fee' => 2500,
            'discount_amount' => 20000,
            'total_amount' => 32500,
            'commission_basis_points' => 500,
            'commission_amount' => 2500,
            'mitra_net_amount' => 27500,
            'status' => 'completed',
            'payment_status' => 'paid',
            'user_snapshot' => ['name' => $consumerUser->name, 'email' => $consumerUser->email],
            'mitra_snapshot' => ['name' => $mitra->display_name],
            'voucher_snapshot' => ['code' => $voucher->code, 'discount' => 20000],
            'placed_at' => now(),
            'paid_at' => now(),
        ]);

        $orderItem = OrderItem::create([
            'order_id' => $order->id,
            'mitra_id' => $mitra->id,
            'catalog_offer_id' => $offerEvent->id,
            'resource_type' => 'event_ticket_type',
            'reference_id' => $ticketType->id,
            'quantity' => 1,
            'item_name' => 'Tiket Presale Festival Budaya',
            'unit_price' => 50000,
            'subtotal' => 50000,
            'admin_fee' => 2500,
            'discount_amount' => 20000,
            'line_total' => 32500,
            'fulfillment_status' => 'fulfilled',
        ]);

        IdempotencyKey::create([
            'user_id' => $consumerUser->id,
            'scope' => 'checkout',
            'key_value' => 'IDEMP-KEY-TEST-001',
            'fingerprint' => hash('sha256', 'IDEMP-KEY-TEST-001'),
            'order_id' => $order->id,
            'response_status' => 200,
            'response_payload' => ['order_id' => $order->id, 'status' => 'paid'],
            'expires_at' => now()->addHour(),
        ]);

        Payment::create([
            'order_id' => $order->id,
            'mitra_id' => $mitra->id,
            'provider' => 'midtrans',
            'provider_reference' => 'MIDTRANS-PAY-001',
            'method' => 'gopay',
            'currency' => 'IDR',
            'amount' => 32500,
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        VoucherUsage::create([
            'voucher_id' => $voucher->id,
            'voucher_claim_id' => $claim->id,
            'order_id' => $order->id,
            'user_id' => $consumerUser->id,
            'discount_amount' => 20000,
            'status' => 'applied',
            'applied_at' => now(),
        ]);

        Ticket::create([
            'ticket_code' => 'TCK-LKT-001',
            'order_item_id' => $orderItem->id,
            'mitra_id' => $mitra->id,
            'holder_user_id' => $consumerUser->id,
            'qr_token_hash' => hash('sha256', 'SAMPLE_QR_TOKEN_COMMERCE'),
            'status' => 'active',
            'valid_from' => now(),
            'valid_until' => now()->addDays(30),
        ]);

        // 13. FINANCIAL DOUBLE-ENTRY LEDGER & WITHDRAWAL
        $accountsList = [
            'cash_gateway' => LedgerAccount::create(['system_code' => 'SYS_CASH_GATEWAY', 'account_type' => 'asset']),
            'accounts_receivable' => LedgerAccount::create(['system_code' => 'SYS_ACCOUNTS_RECEIVABLE', 'account_type' => 'asset']),
            'platform_revenue' => LedgerAccount::create(['system_code' => 'SYS_PLATFORM_REVENUE', 'account_type' => 'revenue']),
            'voucher_expense' => LedgerAccount::create(['system_code' => 'SYS_VOUCHER_EXPENSE', 'account_type' => 'expense']),
            'mitra_payable' => LedgerAccount::create(['mitra_id' => $mitra->id, 'account_type' => 'liability']),
        ];

        $journal = LedgerJournal::create([
            'journal_number' => 'JRN-20260807-001',
            'mitra_id' => $mitra->id,
            'event_key' => 'PAYMENT_CAPTURE_'.$order->id,
            'event_type' => 'payment_captured',
            'order_id' => $order->id,
            'description' => 'Posting jurnal otomatis untuk pesanan '.$order->order_number,
            'effective_at' => now(),
            'posted_at' => now(),
        ]);

        LedgerLine::create(['ledger_journal_id' => $journal->id, 'ledger_account_id' => $accountsList['cash_gateway']->id, 'sequence' => 1, 'debit_amount' => 32500, 'credit_amount' => 0]);
        LedgerLine::create(['ledger_journal_id' => $journal->id, 'ledger_account_id' => $accountsList['mitra_payable']->id, 'sequence' => 2, 'debit_amount' => 0, 'credit_amount' => 27500]);
        LedgerLine::create(['ledger_journal_id' => $journal->id, 'ledger_account_id' => $accountsList['platform_revenue']->id, 'sequence' => 3, 'debit_amount' => 0, 'credit_amount' => 5000]);

        MitraBalance::updateOrCreate(
            ['mitra_id' => $mitra->id],
            [
                'currency' => 'IDR',
                'available_amount' => 350000,
                'held_amount' => 50000,
                'total_earned_amount' => 400000,
                'last_journal_id' => $journal->id,
                'rebuilt_at' => now(),
            ]
        );

        $bankAccount = $mitra->bankAccounts()->first();

        WithdrawalClaim::create([
            'withdrawal_number' => 'WDR-20260807-001',
            'mitra_id' => $mitra->id,
            'mitra_bank_account_id' => $bankAccount->id,
            'submitted_by' => $ownerUser->id,
            'amount' => 100000,
            'idempotency_key' => \Illuminate\Support\Str::random(32),
            'request_fingerprint' => hash('sha256', 'WDR-20260807-001'),
            'status' => \App\Enums\WithdrawalStatus::Submitted,
            'bank_snapshot' => ['bank_code' => 'BCA', 'number' => '1234567890'],
        ]);

        // 14. REVIEWS & AUDIT LOGS
        Review::create([
            'order_item_id' => $orderItem->id,
            'catalog_entity_id' => $entityTourism->id,
            'user_id' => $consumerUser->id,
            'rating' => 5,
            'title' => 'Pengalaman Menyenangkan',
            'body' => 'Pengalaman festival budaya yang sangat luar biasa dan berkesan!',
            'status' => 'published',
        ]);

        // 15. MITRA KATEGORI DINAS & TRANSAKSI TIKET RETRIBUSI PAD
        $dinasSupervisor = $users['supervisor.dinas@tegalkab.go.id'];

        $mitraGuci = Mitra::updateOrCreate(
            ['slug' => 'uptd-wisata-guci-tegal'],
            [
                'category' => 'dinas',
                'owner_user_id' => $dinasSupervisor->id,
                'region_id' => $firstRegion?->id,
                'legal_name' => 'UPTD Pengelolaan Objek Wisata Guci & Cacaban',
                'display_name' => 'Taman Wisata Air Panas Guci',
                'status' => 'active',
                'approved_at' => now(),
                'contact_email' => 'uptd.guci@tegalkab.go.id',
                'contact_phone' => '0283-911223',
                'address' => 'Jl. Objek Wisata Guci, Bumijawa, Kabupaten Tegal',
                'description' => 'Destinasi wisata pemandian air panas alami di lereng Gunung Slamet yang dikelola resmi oleh Dinas Pariwisata Pemkab Tegal.',
            ]
        );

        $mitraPai = Mitra::updateOrCreate(
            ['slug' => 'uptd-pantai-alam-indah'],
            [
                'category' => 'dinas',
                'owner_user_id' => $dinasSupervisor->id,
                'region_id' => $firstRegion?->id,
                'legal_name' => 'UPTD Balai Wisata Bahari Kota Tegal',
                'display_name' => 'Pantai Alam Indah (PAI) Tegal',
                'status' => 'active',
                'approved_at' => now(),
                'contact_email' => 'pai.tegal@tegalkota.go.id',
                'contact_phone' => '0283-356789',
                'address' => 'Jl. Sangir No. 1, Mintaragen, Kota Tegal',
                'description' => 'Destinasi wisata bahari pesisir pantai utara dengan fasilitas anjungan dan museum bahari milik Pemkot Tegal.',
            ]
        );

        // Feature and Gates for Dinas Mitras
        foreach ([$mitraGuci, $mitraPai] as $dinasM) {
            MitraFeature::updateOrCreate(['mitra_id' => $dinasM->id, 'service_type_id' => $tourismType->id], ['status' => 'enabled', 'enabled_at' => now()]);
            $gkDinas = $dinasM->members()->updateOrCreate(['user_id' => $gatekeeperUser->id], ['status' => 'active', 'joined_at' => now()]);
            GatekeeperAssignment::updateOrCreate(
                ['mitra_id' => $dinasM->id, 'member_id' => $gkDinas->id],
                [
                    'scope_type' => 'mitra',
                    'valid_from' => now()->subMonth(),
                    'valid_until' => now()->addYear(),
                    'assigned_by' => $dinasSupervisor->id,
                    'revoked_at' => null,
                ]
            );
        }

        // Seed Sample Paid Orders and Tickets for Dinas PAD Analytics
        $samplePrices = [20000, 25000, 15000, 30000];
        $sampleMitras = [$mitraGuci, $mitraPai];

        for ($i = 1; $i <= 15; $i++) {
            $m = $sampleMitras[$i % 2];
            $price = $samplePrices[$i % 4];
            $orderDate = now()->subDays($i * 2);

            $dinasOrder = Order::create([
                'order_number' => 'ORD-DINAS-'.now()->format('Ymd').'-'.sprintf('%04d', $i),
                'mitra_id' => $m->id,
                'user_id' => ($i % 2 === 0) ? $consumerUser->id : $users['consumer2@example.test']->id,
                'total_amount' => $price * 2,
                'subtotal' => $price * 2,
                'status' => 'paid',
                'paid_at' => $orderDate,
                'mitra_snapshot' => [
                    'id' => $m->id,
                    'name' => $m->name,
                ],
                'user_snapshot' => [
                    'name' => ($i % 2 === 0) ? $consumerUser->name : 'Rina Wisatawan',
                    'email' => ($i % 2 === 0) ? $consumerUser->email : 'consumer2@example.test',
                ],
                'created_at' => $orderDate,
            ]);

            $offer = \App\Models\CatalogOffer::first();
            $dinasOrderItem = OrderItem::create([
                'order_id' => $dinasOrder->id,
                'mitra_id' => $m->id,
                'catalog_offer_id' => $offer->id,
                'resource_type' => 'ticket_package',
                'reference_id' => \Illuminate\Support\Str::ulid(),
                'item_name' => 'Tiket Masuk Retribusi '.$m->display_name,
                'unit_price' => $price,
                'quantity' => 2,
                'subtotal' => $price * 2,
                'line_total' => $price * 2,
            ]);

            for ($t = 1; $t <= 2; $t++) {
                $isCheckedIn = ($i % 3 !== 0); // 2/3 are checked in
                Ticket::create([
                    'order_item_id' => $dinasOrderItem->id,
                    'mitra_id' => $m->id,
                    'ticket_code' => 'TKT-PAD-'.now()->format('ymd').'-'.strtoupper(str()->random(6)),
                    'qr_token_hash' => hash('sha256', \Illuminate\Support\Str::random(40)),
                    'status' => $isCheckedIn ? 'used' : 'unused',
                    'used_at' => $isCheckedIn ? $orderDate->copy()->addHours(2) : null,
                    'valid_from' => $orderDate->copy()->startOfDay(),
                    'valid_until' => $orderDate->copy()->addDays(7)->endOfDay(),
                    'created_at' => $orderDate,
                ]);
            }
        }
    }
}
