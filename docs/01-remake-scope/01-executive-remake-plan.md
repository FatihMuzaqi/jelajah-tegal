# Executive Remake Plan

Status dokumen: SCOPE_COMPLETE. Tahap ini hanya menentukan scope; tidak membuat migration, model, UI, package, atau database.

## Tujuan

Lokantara dibuat ulang sebagai Laravel monolith menggunakan Laravel 13, PHP 8.3-compatible, MySQL 8, Blade, Livewire, Alpine.js, Bootstrap 5, Vite, Spatie Laravel Permission, ApexCharts, Queue, Scheduler, Storage, serta Pest atau PHPUnit.

Dashboard memakai pola informasi ArchitectUI sebagai inspirasi: sidebar, top navigation, cards, tables, filters, charts, dan responsive hierarchy. Source code, aset, CSS, JavaScript, ikon, maupun template berlisensi tidak disalin.

## Evidence dan tingkat kepastian

Folder dokumentasi target lama telah dihapus oleh pemilik workspace. Scope ini diregenerasi dari:

- dokumentasi internal backend, terutama docs/FEATURE_MATRIX.md, docs/ARCHITECTURE.md, docs/design/manual-auth-admin.md, PRD, OpenAPI, dan schema Prisma;
- dokumentasi frontend FEATURE_MATRIX.md dan DESIGN.md;
- source aktif services/core-service/src/modules dan app.ts;
- source halaman, Server Actions, API client, proxy, dan komponen web-lokantara-main;
- temuan legacy yang ditetapkan langsung dalam prompt ini.

Fitur tidak dianggap aktif hanya karena mempunyai model Prisma. Implemented berarti ditemukan route/service/controller atau pemakaian frontend nyata. Model-only atau in-memory tetap diklasifikasikan future, redesign, atau not migrated.

## Kondisi legacy yang menentukan strategi

| Temuan | Dampak terhadap remake |
| --- | --- |
| Backend mempunyai modul aktif sekaligus model future/legacy | Hanya flow yang terbukti dan diprioritaskan yang masuk V1 |
| Consumer web masih dominan mock dan ComingSoon | Consumer surface dibuat ulang secara bertahap, bukan port visual mentah |
| Mitra portal hanya shell/minimal | Portal Mitra adalah rebuild, walaupun API Mitra cukup luas |
| Admin frontend paling matang | Pola workflow admin dipertahankan, implementasi UI dibangun ulang dengan Blade/Livewire/Bootstrap |
| Marketplace checkout belum production-ready | Marketplace berstatus NEEDS_REDESIGN dan masuk V2 |
| Refund/dispute belum lengkap | Tidak masuk V1; financial policy harus diputuskan dahulu |
| CMS baru banner | Banner dapat masuk V1 supporting; CMS generik tidak diasumsikan ada |
| Master data dan analytics belum matang | Master region minimum V1; master generik dan analytics V2 |
| Kontrak frontend/backend tidak selalu sama | Laravel menggunakan kontrak internal tunggal; tidak menyalin mismatch legacy |
| Role aktual customer, mitra_owner, mitra_staff, admin, gatekeeper | Dipetakan menjadi enam seed role target yang dinamis |

## Prinsip scope

1. V1 harus menyelesaikan satu alur bisnis end-to-end: identity → tenant/catalog → checkout → Midtrans → ticket → validation → ledger/claim → audit/notification.
2. Semua authorization memakai permission database dan tenant membership; nama role hanya baseline assignment.
3. Monolith tidak membutuhkan refresh token untuk browser Blade/Livewire. Session Laravel menggantikan mekanisme itu; token legacy aktif tidak dimigrasikan.
4. Model catalog yang aktif tidak otomatis berarti semua domain diluncurkan bersamaan. Tourism, Accommodation, Culinary, dan Event diprioritaskan; Rental dan Marketplace ditunda.
5. Saldo dan payout tidak boleh menggunakan mock atau mutable balance tanpa journal. Ledger harus menjadi sumber kebenaran.
6. Queue menangani email, notification, webhook follow-up, media processing, dan broadcast; Scheduler menangani expiry/reconciliation/cleanup.

## Release shape

| Lapisan | V1 |
| --- | --- |
| Public | Landing dan discovery catalog, detail, auth, checkout, status transaksi |
| Consumer | Profil, alamat, favorite, review, order, payment, voucher bila siap, ticket/QR, notification |
| Mitra | Tenant switch, profil bisnis, staff/gatekeeper, bank/KYC, catalog, order/ticket operations, claim |
| Gatekeeper | Scanner/validasi tiket sesuai assignment scope |
| Admin | Dashboard, user, Mitra/KYC, catalog moderation, transaction support, claim, banner, broadcast, master region, settings, audit, RBAC |
| Super Admin | Seluruh administrasi RBAC dan konfigurasi sensitif dengan MFA dan audit |

## Scope summary

- VERSION_1_CORE: security, dynamic RBAC, Mitra tenancy, empat catalog utama, order/payment/ticket/ledger, moderation, media, audit, minimum master/config.
- VERSION_1_SUPPORTING: notification preferences, favorite/review, voucher terbatas, broadcast transactional, CMS banner, operational dashboards.
- VERSION_2: Rental, Marketplace setelah redesign, Virtual Tour, analytics, richer CMS/master data.
- FUTURE: social feed, follow, trip sharing, AI Planner dan capability eksperimental.
- DEPRECATED/NOT_MIGRATED: hardcoded role checks, legacy browser refresh-token flow, duplicate schema/model families, static/mock/ComingSoon data, obsolete endpoint aliases.
- NEEDS_REDESIGN: marketplace checkout/stock/shipping, refund/dispute, claim-to-payout automation, generic CMS, analytics definitions.

## Exit criteria scope

Scope dianggap selesai karena setiap fitur pada prompt telah mendapat klasifikasi, versi, dependency, alasan, role/surface, risiko, dan open decision. Keputusan terbuka tidak menghalangi arsitektur V1 selama fitur terkait didefer atau dibatasi eksplisit.

