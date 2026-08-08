<?php

return [
    'consumer' => [
        ['label' => 'Dashboard', 'route' => 'consumer.dashboard', 'permission' => 'access.consumer'],
        ['label' => 'Notifikasi', 'href' => '#notifications', 'permission' => 'notifications.view'],
    ],
    'mitra' => [
        ['label' => 'Dashboard', 'route' => 'mitra.dashboard', 'permission' => 'access.mitra'],
        ['label' => 'Wisata', 'route' => 'mitra.tourism.index', 'permission' => 'tourism.manage'],
        ['label' => 'Penginapan', 'route' => 'mitra.accommodation.index', 'permission' => 'accommodation.manage'],
        ['label' => 'Profil Mitra', 'route' => 'mitra.profile.edit', 'permission' => 'mitras.view'],
        ['label' => 'Anggota', 'route' => 'mitra.members.index', 'permission' => 'members.manage'],
        ['label' => 'KYC', 'route' => 'mitra.kyc.index', 'permission' => 'kyc.submit'],
        ['label' => 'Fitur Bisnis', 'route' => 'mitra.features.index', 'permission' => 'mitras.update'],
        ['label' => 'Rekening Bank', 'route' => 'mitra.bank-accounts.index', 'permission' => 'bank-accounts.manage'],
    ],
    'gatekeeper' => [
        ['label' => 'Dashboard', 'route' => 'gatekeeper.dashboard', 'permission' => 'access.gatekeeper'],
        ['label' => 'Validasi Tiket', 'href' => '#validation', 'permission' => 'tickets.validate'],
    ],
    'admin' => [
        ['label' => 'Dashboard', 'route' => 'admin.dashboard', 'permission' => 'access.admin'],
        ['label' => 'Pengguna', 'route' => 'admin.users.index', 'permission' => 'users.manage'],
        ['label' => 'Mitra', 'route' => 'admin.mitras.index', 'permission' => 'mitras.create'],
        ['label' => 'Moderasi Wisata', 'route' => 'admin.tourism.index', 'permission' => 'tourism.moderate'],
        ['label' => 'Moderasi Penginapan', 'route' => 'admin.accommodation.index', 'permission' => 'accommodation.moderate'],
        ['label' => 'Review KYC', 'route' => 'admin.kyc.index', 'permission' => 'kyc.review'],
        ['label' => 'Request Fitur', 'route' => 'admin.features.index', 'permission' => 'feature-requests.review'],
        ['label' => 'Audit Log', 'route' => 'admin.audit.index', 'permission' => 'audit.view'],
    ],
    'super-admin' => [
        ['label' => 'Dashboard', 'route' => 'super-admin.dashboard', 'permission' => 'access.super-admin'],
        ['label' => 'Role System', 'route' => 'super-admin.roles.index', 'permission' => 'roles.manage'],
        ['label' => 'Permission Matrix', 'route' => 'super-admin.permissions.index', 'permission' => 'permissions.manage'],
        ['label' => 'Feature Flags', 'route' => 'super-admin.flags.index', 'permission' => 'feature-flags.manage'],
        ['label' => 'Pengaturan Platform', 'route' => 'super-admin.settings.index', 'permission' => 'settings.manage'],
    ],
];
