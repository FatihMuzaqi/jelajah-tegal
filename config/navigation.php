<?php

return [
    'consumer' => [
        [
            'category' => 'Aktivitas Saya',
            'items' => [
                ['label' => 'Dashboard', 'route' => 'consumer.dashboard', 'icon' => 'fa-solid fa-house-user', 'permission' => 'access.consumer'],
                ['label' => 'Pesanan & E-Tiket', 'route' => 'consumer.orders.index', 'icon' => 'fa-solid fa-ticket', 'permission' => 'access.consumer'],
                ['label' => 'Dokumen Sewa', 'route' => 'consumer.renter-documents.index', 'icon' => 'fa-solid fa-file-invoice', 'permission' => 'access.consumer'],
            ],
        ],
    ],

    'mitra' => [
        [
            'category' => 'Ringkasan',
            'items' => [
                ['label' => 'Dashboard', 'route' => 'mitra.dashboard', 'icon' => 'fa-solid fa-chart-pie', 'permission' => 'access.mitra'],
            ],
        ],
        [
            'category' => 'Katalog & Layanan',
            'items' => [
                ['label' => 'Wisata', 'route' => 'mitra.tourism.index', 'icon' => 'fa-solid fa-umbrella-beach', 'permission' => 'tourism.manage'],
                ['label' => 'Penginapan', 'route' => 'mitra.accommodation.index', 'icon' => 'fa-solid fa-hotel', 'permission' => 'accommodation.manage'],
                ['label' => 'Kuliner', 'route' => 'mitra.culinary.index', 'icon' => 'fa-solid fa-utensils', 'permission' => 'culinary.manage'],
                ['label' => 'Event & Acara', 'route' => 'mitra.event.index', 'icon' => 'fa-solid fa-calendar-days', 'permission' => 'event.manage'],
                ['label' => 'Rental Kendaraan', 'route' => 'mitra.rental.index', 'icon' => 'fa-solid fa-car', 'permission' => 'rental.manage'],
            ],
        ],
        [
            'category' => 'Penjualan & Keuangan',
            'items' => [
                ['label' => 'Pesanan Masuk', 'route' => 'mitra.orders.index', 'icon' => 'fa-solid fa-receipt', 'permission' => 'orders.view'],
                ['label' => 'Voucher Promo', 'route' => 'mitra.vouchers.index', 'icon' => 'fa-solid fa-tags', 'permission' => 'vouchers.manage'],
                ['label' => 'Penarikan Saldo', 'route' => 'mitra.withdrawals.index', 'icon' => 'fa-solid fa-money-bill-transfer', 'permission' => 'withdrawals.view'],
            ],
        ],
        [
            'category' => 'Pengaturan Bisnis',
            'items' => [
                ['label' => 'Profil Mitra', 'route' => 'mitra.profile.edit', 'icon' => 'fa-solid fa-store', 'permission' => 'mitras.view'],
                ['label' => 'Anggota Tim', 'route' => 'mitra.members.index', 'icon' => 'fa-solid fa-users', 'permission' => 'members.manage'],
                ['label' => 'Dokumen KYC', 'route' => 'mitra.kyc.index', 'icon' => 'fa-solid fa-id-card', 'permission' => 'kyc.submit'],
                ['label' => 'Rekening Bank', 'route' => 'mitra.bank-accounts.index', 'icon' => 'fa-solid fa-building-columns', 'permission' => 'bank-accounts.manage'],
                ['label' => 'Fitur Layanan', 'route' => 'mitra.features.index', 'icon' => 'fa-solid fa-sliders', 'permission' => 'mitras.update'],
            ],
        ],
    ],

    'gatekeeper' => [
        [
            'category' => 'Operasional Loket',
            'items' => [
                ['label' => 'Dashboard', 'route' => 'gatekeeper.dashboard', 'icon' => 'fa-solid fa-gauge', 'permission' => 'access.gatekeeper'],
                ['label' => 'Validasi Tiket QR', 'route' => 'gatekeeper.event-tickets.index', 'icon' => 'fa-solid fa-qrcode', 'permission' => 'tickets.validate'],
            ],
        ],
    ],

    'admin' => [
        [
            'category' => 'Ringkasan',
            'items' => [
                ['label' => 'Dashboard', 'route' => 'admin.dashboard', 'icon' => 'fa-solid fa-gauge-high', 'permission' => 'access.admin'],
            ],
        ],
        [
            'category' => 'Manajemen Pengguna',
            'items' => [
                ['label' => 'Pengguna Platform', 'route' => 'admin.users.index', 'icon' => 'fa-solid fa-users-gear', 'permission' => 'users.manage'],
                ['label' => 'Kelola Mitra', 'route' => 'admin.mitras.index', 'icon' => 'fa-solid fa-handshake', 'permission' => 'mitras.create'],
            ],
        ],
        [
            'category' => 'Verifikasi & Moderasi',
            'items' => [
                ['label' => 'Moderasi Wisata', 'route' => 'admin.tourism.index', 'icon' => 'fa-solid fa-umbrella-beach', 'permission' => 'tourism.moderate'],
                ['label' => 'Moderasi Penginapan', 'route' => 'admin.accommodation.index', 'icon' => 'fa-solid fa-hotel', 'permission' => 'accommodation.moderate'],
                ['label' => 'Moderasi Kuliner', 'route' => 'admin.culinary.index', 'icon' => 'fa-solid fa-utensils', 'permission' => 'culinary.moderate'],
                ['label' => 'Moderasi Event', 'route' => 'admin.event.index', 'icon' => 'fa-solid fa-calendar-check', 'permission' => 'event.moderate'],
                ['label' => 'Moderasi Rental', 'route' => 'admin.rental.index', 'icon' => 'fa-solid fa-car-side', 'permission' => 'rental.moderate'],
                ['label' => 'Review Dokumen KYC', 'route' => 'admin.kyc.index', 'icon' => 'fa-solid fa-file-shield', 'permission' => 'kyc.review'],
                ['label' => 'Request Fitur Mitra', 'route' => 'admin.features.index', 'icon' => 'fa-solid fa-layer-group', 'permission' => 'feature-requests.review'],
            ],
        ],
        [
            'category' => 'Operasional & Keuangan',
            'items' => [
                ['label' => 'Voucher Platform', 'route' => 'admin.vouchers.index', 'icon' => 'fa-solid fa-tags', 'permission' => 'vouchers.manage'],
                ['label' => 'Penarikan Dana', 'route' => 'admin.withdrawals.index', 'icon' => 'fa-solid fa-wallet', 'permission' => 'withdrawals.process'],
                ['label' => 'Audit Log Keamanan', 'route' => 'admin.audit.index', 'icon' => 'fa-solid fa-shield-halved', 'permission' => 'audit.view'],
            ],
        ],
    ],

    'super-admin' => [
        [
            'category' => 'Ringkasan',
            'items' => [
                ['label' => 'Dashboard', 'route' => 'super-admin.dashboard', 'icon' => 'fa-solid fa-shield-heart', 'permission' => 'access.super-admin'],
            ],
        ],
        [
            'category' => 'Akses & Keamanan',
            'items' => [
                ['label' => 'Role System', 'route' => 'super-admin.roles.index', 'icon' => 'fa-solid fa-user-shield', 'permission' => 'roles.manage'],
                ['label' => 'Permission Matrix', 'route' => 'super-admin.permissions.index', 'icon' => 'fa-solid fa-key', 'permission' => 'permissions.manage'],
                ['label' => 'Feature Flags', 'route' => 'super-admin.flags.index', 'icon' => 'fa-solid fa-toggle-on', 'permission' => 'feature-flags.manage'],
            ],
        ],
        [
            'category' => 'Sistem',
            'items' => [
                ['label' => 'Pengaturan Platform', 'route' => 'super-admin.settings.index', 'icon' => 'fa-solid fa-gears', 'permission' => 'settings.manage'],
                ['label' => 'Pengaturan AI Chatbot', 'route' => 'super-admin.chatbot.index', 'icon' => 'fa-solid fa-robot', 'permission' => 'settings.manage'],
            ],
        ],
    ],
];
