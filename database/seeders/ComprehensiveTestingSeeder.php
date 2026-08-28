<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Facility;
use App\Models\Region;
use App\Models\ServiceType;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ComprehensiveTestingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        // 1. Service Types & Permissions Seeders
        $this->call([
            ServiceTypeSeeder::class,
            RolesAndPermissionsSeeder::class,
            FeatureFlagSeeder::class,
        ]);

        // 2. Akun Pengguna Utama (Password: 'password')
        setPermissionsTeamId(null);
        $accounts = [
            ['email' => 'admin@example.test', 'name' => 'Administrator Platform', 'role' => 'admin'],
            ['email' => 'superadmin@example.test', 'name' => 'Super Admin System', 'role' => 'super-admin'],
            ['email' => 'supervisor.dinas@tegalkab.go.id', 'name' => 'Dr. H. Ahmad Rasyid, M.Si (Kepala Bidang Pariwisata)', 'role' => 'dinas-supervisor'],
            ['email' => 'admin1@jelajahtegal.com', 'name' => 'Admin 1 (Budi Santoso)', 'role' => 'admin'],
            ['email' => 'admin2@jelajahtegal.com', 'name' => 'Admin 2 (Siti Rahmawati)', 'role' => 'admin'],
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

        foreach ($tegalRegions as $reg) {
            Region::updateOrCreate(
                ['code' => $reg['code']],
                ['name' => $reg['name'], 'level' => $reg['level'], 'deleted_at' => null]
            );
        }
        
        $tourismType = ServiceType::where('code', 'tourism')->first();
        $accommType = ServiceType::where('code', 'accommodation')->first();
        $culinaryType = ServiceType::where('code', 'culinary')->first();
        $eventType = ServiceType::where('code', 'event')->first();
        $rentalType = ServiceType::where('code', 'rental')->first();

        // Kategori Wisata
        $tourCategories = [
            ['slug' => 'tr-alam', 'name' => 'Wisata Alam & Pegunungan'],
            ['slug' => 'tr-pantai', 'name' => 'Wisata Pantai & Bahari'],
            ['slug' => 'tr-sejarah', 'name' => 'Wisata Sejarah & Budaya'],
            ['slug' => 'tr-buatan', 'name' => 'Taman Rekreasi Buatan'],
        ];
        foreach ($tourCategories as $tc) {
            Category::updateOrCreate(['slug' => $tc['slug']], ['service_type_id' => $tourismType?->id, 'name' => $tc['name']]);
        }

        // Kategori Penginapan
        $accCategories = [
            ['slug' => 'ac-hotel', 'name' => 'Hotel Berbintang'],
            ['slug' => 'ac-villa', 'name' => 'Villa & Resort'],
            ['slug' => 'ac-homestay', 'name' => 'Homestay & Guesthouse'],
            ['slug' => 'ac-glamping', 'name' => 'Glamping & Camp'],
        ];
        foreach ($accCategories as $ac) {
            Category::updateOrCreate(['slug' => $ac['slug']], ['service_type_id' => $accommType?->id, 'name' => $ac['name']]);
        }

        // Kategori Kuliner
        $culCategories = [
            ['slug' => 'cu-resto', 'name' => 'Restoran Keluarga'],
            ['slug' => 'cu-cafe', 'name' => 'Cafe & Nongkrong'],
            ['slug' => 'cu-lokal', 'name' => 'Warung Khas Lokal'],
            ['slug' => 'cu-seafood', 'name' => 'Seafood & Bakar'],
        ];
        foreach ($culCategories as $cc) {
            Category::updateOrCreate(['slug' => $cc['slug']], ['service_type_id' => $culinaryType?->id, 'name' => $cc['name']]);
        }
        
        // Fasilitas Umum
        $facilities = [
            ['slug' => 'f-parkir', 'name' => 'Area Parkir Luas'],
            ['slug' => 'f-toilet', 'name' => 'Toilet Bersih'],
            ['slug' => 'f-mushola', 'name' => 'Mushola'],
            ['slug' => 'f-wifi', 'name' => 'WiFi Gratis'],
            ['slug' => 'f-kantin', 'name' => 'Kantin / Foodcourt'],
            ['slug' => 'f-medis', 'name' => 'Ruang Medis'],
            ['slug' => 'f-kolam', 'name' => 'Kolam Renang'],
        ];
        foreach ($facilities as $fac) {
            Facility::updateOrCreate(['slug' => $fac['slug']], ['service_type_id' => $tourismType?->id, 'name' => $fac['name']]);
        }
        
    }
}
