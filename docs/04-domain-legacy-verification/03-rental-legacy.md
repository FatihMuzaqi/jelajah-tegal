# Rental — verifikasi legacy dan keputusan migrasi

Status kepastian: **VERIFIED** terhadap source legacy pada 4 Agustus 2026.

## Bukti fitur aktif

| Area | Evidence | Route/fungsi aktif | Keputusan target |
|---|---|---|---|
| Publik | `services/core-service/src/modules/rental/routes.ts`, `createRentalRouter` | `GET /vehicles`, detail, review, favorite | Dipertahankan |
| Katalog Mitra | file yang sama, `RentalService` | create/update/media | Dipindahkan ke `/mitra/rental` dan tenant policy |
| Dokumen penyewa | file yang sama | submit/list/review dokumen | Dipertahankan di private storage dengan ownership eksplisit |
| Operasi | `mitra-routes.ts`, `RentalMitraOperationsService` | list/detail/confirm/pickup/return booking, availability, review reply | Dibuat ulang dengan lifecycle dan overlap lock |

## Alur bisnis target

Mitra membuat kendaraan, tarif, syarat, availability/blocked/price override lalu moderasi; consumer mengunggah KTP/SIM privat dan meminta booking; Mitra meninjau dokumen lalu menyetujui/menolak; pickup dan return mengikuti urutan status; periode overlap ditolak dalam transaksi.

## Tidak dipindahkan

- Dua keluarga Prisma Rental lama/baru dikonsolidasikan menjadi `rental_vehicles` canonical.
- Nearest materialized model tidak dibawa; query memakai `catalog_locations`.
- Checkout/payment rental legacy tidak dipindahkan pada tahap ini; booking tetap berstatus belum dibayar sampai modul transaksi tersedia.
- Frontend consumer tidak memiliki portal Rental aktif dan hanya `ComingSoon`.

