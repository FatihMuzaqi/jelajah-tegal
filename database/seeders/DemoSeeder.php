<?php

namespace Database\Seeders;

use App\Models\Mitra;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\PermissionRegistrar;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $accounts = [
            ['email' => 'admin@example.test', 'name' => 'Administrator Platform', 'role' => 'admin'],
            ['email' => 'superadmin@example.test', 'name' => 'Super Admin System', 'role' => 'super-admin'],
            ['email' => 'consumer@example.test', 'name' => 'Pengguna Consumer Demo', 'role' => 'consumer'],
            ['email' => 'owner@example.test', 'name' => 'Budi Susanto (Owner Mitra)', 'role' => 'mitra-owner'],
        ];

        setPermissionsTeamId(null);

        foreach ($accounts as $acc) {
            $user = User::updateOrCreate(
                ['email' => $acc['email']],
                ['name' => $acc['name'], 'status' => 'active', 'email_verified_at' => now()]
            );

            // Buat atau perbarui password credential (password = 'password')
            $user->credential()->updateOrCreate(
                ['user_id' => $user->id],
                ['password_hash' => Hash::make('password')]
            );

            $user->profile()->updateOrCreate(
                ['user_id' => $user->id],
                ['notification_preferences' => []]
            );

            if ($acc['role'] !== 'mitra-owner') {
                $user->syncRoles([$acc['role']]);
            }
        }

        // 4. Konfigurasi Khusus Mitra Owner
        $owner = User::where('email', 'owner@example.test')->firstOrFail();
        
        $mitra = Mitra::updateOrCreate(
            ['slug' => 'mitra-utama-tegal'],
            [
                'owner_user_id' => $owner->id,
                'legal_name' => 'PT Lokantara Mitra Tegal',
                'display_name' => 'Mitra Utama Tegal',
                'status' => 'active',
                'approved_at' => now(),
                'contact_email' => 'owner@example.test',
                'contact_phone' => '081234567890',
                'address' => 'Jl. Pancasila No. 1, Kota Tegal',
            ]
        );

        $mitra->members()->updateOrCreate(
            ['user_id' => $owner->id],
            ['status' => 'active', 'joined_at' => now()]
        );

        setPermissionsTeamId($mitra->id);
        $owner->syncRoles(['mitra-owner']);
        setPermissionsTeamId(null);

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
