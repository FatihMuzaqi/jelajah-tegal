# Role and Surface Design

## Model authorization

Role dan permission tersimpan dinamis menggunakan Spatie Laravel Permission. Enam role di bawah adalah seed awal, bukan enum dan bukan satu-satunya dasar authorization. Setiap action dilindungi permission, policy, ownership, tenant context, state transition, dan bila perlu MFA/step-up.

| Role target | Mapping legacy | Surface utama | Scope data |
| --- | --- | --- | --- |
| consumer | customer | Public + Consumer | Data sendiri dan catalog published |
| mitra-owner | mitra_owner | Mitra | Mitra membership yang dipilih; capability owner sesuai permission |
| mitra-staff | mitra_staff | Mitra | Mitra membership yang dipilih; operasi delegated |
| gatekeeper | gatekeeper | Gatekeeper scanner | Assignment tourism/event/Mitra yang aktif |
| admin | admin | Admin | Platform operation sesuai permission |
| super-admin | role target baru | Admin security/config | Platform-wide, tetap permission dan MFA-bound |

## Permission namespace

Permission disarankan berbentuk resource.action dan tidak menanam role, misalnya users.view, mitras.create, mitras.suspend, kyc.review, catalog.tourism.manage, orders.view, tickets.validate, claims.review, ledger.view, roles.manage, permissions.assign, audit.view, settings.manage.

Permission tenant-aware diperiksa bersama membership. Memiliki catalog.tourism.manage tidak memberi akses ke semua Mitra; policy juga wajib memverifikasi active mitra_id.

## Baseline permission matrix

| Capability | consumer | mitra-owner | mitra-staff | gatekeeper | admin | super-admin |
| --- | :---: | :---: | :---: | :---: | :---: | :---: |
| Kelola profil sendiri | Ya | Ya | Ya | Ya | Ya | Ya |
| Browse catalog published | Ya | Ya | Ya | Ya | Ya | Ya |
| Checkout/order/ticket sendiri | Ya | Opsional sebagai consumer | Opsional | Tidak | Support read bila diberi | Support read bila diberi |
| Kelola profil Mitra | Tidak | Default | Jika diberi | Tidak | Jika diberi | Jika diberi |
| Kelola staff/gatekeeper | Tidak | Default | Jika diberi | Tidak | Jika diberi | Jika diberi |
| Kelola rekening/claim | Tidak | Default | Tidak secara default | Tidak | Review bila diberi | Review bila diberi |
| Kelola catalog tenant | Tidak | Default | Jika diberi | Tidak | Moderate/support bila diberi | Bila diberi |
| Validasi tiket | Tidak | Hanya bila permission+assignment | Hanya bila permission+assignment | Default+assignment | Tidak otomatis | Tidak otomatis |
| Review KYC/moderasi | Tidak | Tidak | Tidak | Tidak | Jika diberi | Jika diberi |
| Kelola settings sensitif | Tidak | Tenant setting terbatas | Tidak | Tidak | Jika diberi | Jika diberi |
| Kelola role/permission | Tidak | Tidak secara default | Tidak | Tidak | Tidak secara default | Default |
| Lihat audit log | Audit sendiri terbatas | Tenant bila diberi | Tenant bila diberi | Scan history sendiri | Jika diberi | Default |

Default pada tabel berarti assignment seed, bukan check nama role di source.

## Super Admin requirements

Super Admin harus memiliki UI dan service untuk:

- membuat role baru;
- mengubah nama/metadata role dengan perlindungan system role;
- memberikan dan mencabut permission dari role;
- memberikan dan mencabut direct permission user bila kebijakan mengizinkan;
- menetapkan/mencabut role kepada user pada scope platform atau tenant yang valid;
- melihat dan memfilter matriks role-permission;
- melihat effective permission user beserta sumber role/direct grant;
- mengaudit seluruh perubahan RBAC.

Operasi ini memerlukan MFA valid, permission khusus, CSRF protection, audit before/after, dan larangan self-lockout terakhir super-admin.

## Surface boundaries

| Surface | Navigation domain | Guard tambahan |
| --- | --- | --- |
| Public | Discovery, auth, content published | Optional auth; moderation status |
| Consumer | Account, favorites, reviews, orders, payments, tickets, notifications | User ownership |
| Mitra | Tenant dashboard, catalog, operations, member, KYC/bank/claim | Active membership + selected tenant |
| Gatekeeper | Scan, result, assigned scope, history | Permission + assignment + active tenant/event |
| Admin | User/Mitra/catalog/transaction/platform operations | Permission + MFA for sensitive actions |
| Super Admin | RBAC, sensitive settings, audit/security | Elevated permission + MFA/step-up |

Satu user dapat mengakses beberapa surface. Surface bukan role; navigation dirender dari effective permission. User dengan membership dua Mitra harus memilih tenant context dan tidak boleh mencampur resource kedua tenant dalam satu request.

## Visual direction

Admin/Mitra dashboard memakai sidebar collapsible, topbar, breadcrumb, summary cards, filterable tables, modal/drawer yang proporsional, dan chart bila metric valid. ArchitectUI hanya referensi pola visual; implementasi memakai Bootstrap 5/Blade/Livewire/Alpine/Vite dan aset berlisensi aman milik project.

