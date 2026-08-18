<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public const PERMISSIONS = [
        'access.consumer', 'access.mitra', 'access.gatekeeper', 'access.dinas', 'access.admin', 'access.super-admin',
        'profile.update', 'mitras.view', 'mitras.update', 'mitras.create', 'members.manage', 'bank-accounts.manage',
        'kyc.submit', 'kyc.review', 'feature-requests.review', 'tickets.validate', 'tickets.issue',
        'claims.create', 'claims.review', 'ledger.view', 'users.manage', 'moderation.review', 'audit.view',
        'settings.manage', 'roles.manage', 'permissions.manage', 'role-assignments.manage', 'feature-flags.manage',
        'media.manage', 'notifications.view', 'tourism.manage', 'tourism.submit', 'tourism.moderate',
        'accommodation.manage', 'accommodation.submit', 'accommodation.moderate', 'culinary.manage',
        'culinary.submit', 'culinary.moderate', 'culinary.reservations.manage', 'event.manage', 'event.submit',
        'event.moderate', 'rental.manage', 'rental.submit', 'rental.moderate', 'rental.bookings.manage',
        'renter-documents.review', 'orders.view', 'vouchers.claim', 'vouchers.manage', 'payments.capture',
        'payments.reconcile', 'withdrawals.view', 'withdrawals.submit', 'withdrawals.cancel',
        'withdrawals.process', 'bank-accounts.verify', 'favorites.manage', 'reviews.create',
        'dinas.analytics.view', 'dinas.reports.export', 'dinas.visitors.monitor',
    ];

    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        foreach (self::PERMISSIONS as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        $map = [
            'consumer' => ['access.consumer', 'profile.update', 'notifications.view', 'vouchers.claim', 'favorites.manage', 'reviews.create'],
            'mitra-owner' => ['access.mitra', 'profile.update', 'mitras.view', 'mitras.update', 'members.manage', 'bank-accounts.manage', 'kyc.submit', 'claims.create', 'ledger.view', 'withdrawals.view', 'withdrawals.submit', 'withdrawals.cancel', 'media.manage', 'notifications.view', 'orders.view', 'vouchers.manage', 'tourism.manage', 'tourism.submit', 'accommodation.manage', 'accommodation.submit', 'culinary.manage', 'culinary.submit', 'culinary.reservations.manage', 'event.manage', 'event.submit', 'tickets.issue', 'rental.manage', 'rental.submit', 'rental.bookings.manage', 'renter-documents.review'],
            'mitra-staff' => ['access.mitra', 'profile.update', 'mitras.view', 'media.manage', 'notifications.view', 'orders.view', 'tourism.manage', 'tourism.submit', 'accommodation.manage', 'accommodation.submit', 'culinary.manage', 'culinary.submit', 'culinary.reservations.manage', 'event.manage', 'event.submit', 'tickets.issue', 'rental.manage', 'rental.submit', 'rental.bookings.manage', 'renter-documents.review'],
            'gatekeeper' => ['access.gatekeeper', 'profile.update', 'tickets.validate'],
            'dinas-supervisor' => ['access.dinas', 'profile.update', 'notifications.view', 'dinas.analytics.view', 'dinas.reports.export', 'dinas.visitors.monitor', 'orders.view', 'tickets.validate'],
            'admin' => ['access.admin', 'access.dinas', 'users.manage', 'mitras.create', 'kyc.review', 'feature-requests.review', 'claims.review', 'moderation.review', 'audit.view', 'vouchers.manage', 'payments.capture', 'payments.reconcile', 'withdrawals.process', 'bank-accounts.verify', 'tourism.moderate', 'accommodation.moderate', 'culinary.moderate', 'event.moderate', 'rental.moderate', 'dinas.analytics.view', 'dinas.reports.export', 'dinas.visitors.monitor'],
            'super-admin' => self::PERMISSIONS,
        ];

        setPermissionsTeamId(null);
        foreach ($map as $name => $permissions) {
            $role = Role::firstOrCreate(['name' => $name, 'guard_name' => 'web', 'mitra_id' => null]);
            $role->syncPermissions($permissions);
        }
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
