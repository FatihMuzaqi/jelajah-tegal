<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DinasSeeder extends Seeder
{
    public function run(): void
    {
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        // 1. Roles & Permissions Dinas
        $dinasPermissions = [
            'access.dinas',
            'dinas.analytics.view',
            'dinas.reports.export',
            'dinas.visitors.monitor',
            'orders.view',
            'tickets.validate',
            'profile.update',
            'notifications.view',
        ];

        foreach ($dinasPermissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        $dinasRole = Role::firstOrCreate(['name' => 'dinas-supervisor', 'guard_name' => 'web']);
        $dinasRole->syncPermissions($dinasPermissions);

        // Also give super-admin and admin access.dinas
        if ($superAdminRole = Role::where('name', 'super-admin')->first()) {
            $superAdminRole->givePermissionTo('access.dinas');
        }
        if ($adminRole = Role::where('name', 'admin')->first()) {
            $adminRole->givePermissionTo('access.dinas');
        }

        // 2. Akun Supervisor Dinas Pemda
        setPermissionsTeamId(null);
        $dinasUser = User::updateOrCreate(
            ['email' => 'supervisor.dinas@tegalkab.go.id'],
            [
                'name' => 'Dr. H. Ahmad Rasyid, M.Si (Kepala Bidang Pariwisata)',
                'status' => 'active',
                'email_verified_at' => now(),
                'phone' => '081223344556',
            ]
        );

        $dinasUser->credential()->updateOrCreate(
            ['user_id' => $dinasUser->id],
            ['password_hash' => Hash::make('password')]
        );

        $dinasUser->profile()->updateOrCreate(
            ['user_id' => $dinasUser->id],
            ['notification_preferences' => ['in_app' => true, 'email' => true]]
        );

        $dinasUser->syncRoles(['dinas-supervisor']);
    }
}
