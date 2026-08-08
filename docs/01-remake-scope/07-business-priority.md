# Business Priority

## Prioritization model

Prioritas mempertimbangkan customer value, operational necessity, security/compliance, revenue enablement, dependency centrality, dan delivery risk. P0 adalah release gate; P1 mendukung kualitas/adopsi; P2 adalah ekspansi.

| Prioritas | Capability | Nilai bisnis | Konsekuensi bila ditunda |
| --- | --- | --- | --- |
| P0 | Identity, session, verification/reset, lock/suspend | Akses aman seluruh pengguna | Semua surface terblokir/berisiko |
| P0 | Dynamic RBAC dan tenant isolation | Delegasi operasi aman | Cross-tenant/privilege escalation |
| P0 | Mitra onboarding, invitation, membership, active status | Supply onboarding | Catalog tidak punya owner valid |
| P0 | Media dan master region minimum | Dependency seluruh catalog/KYC | Listing dan dokumen tidak operasional |
| P0 | Tourism, Accommodation, Culinary, Event core | Produk utama | Tidak ada inventory untuk discovery/transaksi |
| P0 | Order/item, Midtrans, QR, validation | Revenue dan fulfillment | Tidak ada flow end-to-end |
| P0 | Ledger dan claim minimum | Settlement Mitra | Saldo/payout tidak dapat dipercaya |
| P0 | Moderation dan audit | Trust, support, compliance | Perubahan sensitif tak terlacak |
| P1 | Favorite, review, notification | Retention dan trust | UX lebih lemah, core tetap berjalan |
| P1 | Voucher terbatas, CMS banner, broadcast | Growth/operations | Campaign manual masih mungkin |
| P1 | Google OAuth dan recovery code | Conversion/recovery | Login email tetap tersedia, tetapi keamanan/UX kurang |
| P2 | Rental, Marketplace, Virtual Tour | Ekspansi revenue/discovery | Tidak menghalangi V1 |
| P2 | Analytics, CMS/master data lanjutan | Optimisasi operasi | Report manual sementara |
| Future | Social, Follow, Trip, AI Planner | Eksperimen engagement | Tidak mengganggu core marketplace perjalanan |

## Recommended delivery slices

| Slice | Outcome demonstrable | Dependency |
| --- | --- | --- |
| 1. Platform foundation | Login/session, RBAC, audit, queue/storage/test harness | Laravel foundation |
| 2. Tenant foundation | Admin creates Mitra, invitation, membership switch, profile/KYC/bank | Slice 1 |
| 3. Catalog foundation | Region/media/category and four domain CRUD/moderation/public read | Slice 2 |
| 4. Commerce kernel | Offer, checkout, order/item, idempotency, Midtrans sandbox | Slice 3 |
| 5. Fulfillment | QR issuance, gatekeeper assignment, atomic validation | Slice 4 |
| 6. Finance | Ledger posting, commission, claim approve/reject/manual paid evidence | Slice 4 |
| 7. Experience/support | Consumer account/history, favorite/review, notifications, banner/broadcast | Slices 3–6 |
| 8. Hardening/cutover | Reconciliation, security/load tests, migration rehearsal, runbook | Semua slice |

## Scope control rules

- Fitur P1 boleh masuk V1 hanya jika tidak mengganggu P0 critical path.
- UI dashboard tidak boleh mendahului service/policy dan metric definition.
- Domain baru harus menggunakan commerce kernel yang sama, bukan membuat payment/order paralel.
- Setiap perubahan V1 scope membutuhkan dependency/risk impact update di dokumen ini dan matrix.

## Business success indicators

- Consumer dapat menyelesaikan transaksi dan menerima ticket.
- Gatekeeper dapat memvalidasi ticket sah tepat satu kali.
- Mitra dapat mengelola catalog dan mengajukan claim berbasis saldo ledger.
- Admin dapat onboard/review/suspend Mitra dan memoderasi catalog.
- Super-admin dapat mengelola RBAC dinamis dengan audit.
- Tidak ada data mock atau ComingSoon yang direpresentasikan sebagai capability production.

