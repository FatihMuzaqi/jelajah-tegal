# Version One Scope

## Sasaran V1

V1 membuktikan operasi Lokantara dari onboarding sampai settlement untuk Tourism, Accommodation, Culinary, dan Event dalam satu Laravel monolith. Portal consumer dan Mitra dibangun ulang; workflow admin matang dipertahankan secara fungsional, bukan disalin implementasinya.

## VERSION_1_CORE

### Identity dan security

- Registrasi consumer, login, logout, email verification, reset password.
- Account lock, suspension, session management, audit authentication.
- MFA TOTP wajib untuk admin dan super-admin; enrollment dan recovery policy.
- User profile, address minimum, dan Google OAuth setelah credential live terverifikasi.
- Dynamic role/permission dengan Spatie; tenant-scoped authorization melalui membership/policy.

### Mitra dan tenant

- Admin membuat Mitra dan mengirim undangan owner.
- Owner mengaktifkan akun; satu user dapat mempunyai beberapa membership Mitra.
- Owner mengelola profil bisnis, staff, gatekeeper, rekening bank, serta module request.
- KYC minimum: submit private documents, review approve/reject, expiry/supersede, audit access.
- Lifecycle draft/active/suspended dan pemilihan active Mitra context.

### Catalog

- Tourism: destination, category/facility, media, package, availability, moderation.
- Accommodation: property, room, facility, media, availability.
- Culinary: venue, menu, hours/slot, media.
- Event: event, schedule, ticket type, quota, media, moderation.
- Public list/detail/search/filter minimum berdasarkan status, category, region, schedule, dan harga.

### Transaction

- Shared checkout dan idempotency.
- Order dan order item sebagai price/fulfillment snapshot.
- Midtrans payment initiation, notification/webhook verification, expiry, status sync.
- QR ticket untuk tourism/event dan atomic ticket validation.
- Gatekeeper assignment/scope; admin tidak otomatis boleh scan.
- Double-entry ledger, commission minimum, claim request, approve/reject, dan manual payout evidence.

### Platform

- Laravel Storage untuk media public/private, metadata, ownership, dan cleanup.
- Moderation catalog/review minimum.
- Audit log untuk auth, RBAC, Mitra/KYC, moderation, payment, ledger, claim, settings.
- Master region dan reference data yang benar-benar dibutuhkan empat catalog.
- Settings dan feature flag/module enablement.
- Queue untuk email, notification delivery, webhook follow-up, media cleanup; Scheduler untuk token/order expiry, cleanup, dan financial checks.
- Automated test untuk state machine, policy/tenant isolation, payment idempotency, ticket atomicity, dan ledger balance.

## VERSION_1_SUPPORTING

- Notification preference dan inbox.
- Favorite dan verified review per domain.
- Voucher fixed/percentage dengan scope terbatas setelah checkout stabil.
- Broadcast operasional dengan audience dan consent terbatas.
- CMS banner saja.
- Withdrawal processing status yang memisahkan approval dari evidence pembayaran manual.
- Dashboard operasional berbasis metric yang telah didefinisikan, bukan analytics suite.
- Recovery codes dan Google OAuth dapat menjadi release gate keamanan bila diputuskan wajib.

## Surface V1

| Surface | Kapabilitas wajib |
| --- | --- |
| Public | Landing, browse/search, detail empat catalog, login/register/reset/verify |
| Consumer | Profile/address, favorite/review, checkout, payment status, order history, QR ticket, notification |
| Mitra owner | Tenant switch, profile/KYC/bank, staff/gatekeeper, feature request, catalog, orders, claim/ledger view |
| Mitra staff | Catalog dan operasi sesuai permission; tidak otomatis mengelola bank/RBAC |
| Gatekeeper | Assignment dan scanner/validation history saja |
| Admin | User/Mitra/KYC, moderation, transaction support, claim, banner/broadcast, region/settings/audit |
| Super admin | Role, permission, assignment, matrix, security/configuration sensitif |

## V1 acceptance gates

1. Zero authorization berdasarkan role-name saja pada action sensitif.
2. Zero cross-tenant access pada policy/integration tests.
3. Webhook Midtrans idempotent dan amount/signature diverifikasi.
4. Ticket hanya terbit setelah payment event sah dan hanya dapat digunakan sekali.
5. Setiap posted journal seimbang; claim tidak dapat melampaui available balance.
6. KYC/bank/private media tidak pernah menggunakan URL publik permanen.
7. Email verification/reset/owner invitation berhasil melalui queue dan dapat diobservasi.
8. Consumer page tidak menampilkan mock sebagai data production.
9. Semua workflow admin sensitif menghasilkan audit event.
10. Browser Blade/Livewire menggunakan Laravel session; refresh token legacy tidak dipertahankan.

## Bukan V1

Rental, Marketplace, Virtual Tour, generic CMS, mature analytics, generic master-data console, social feed, follow, trip, AI Planner, refund, dispute, automated payout provider, dan advanced tax/invoice tidak termasuk V1.

