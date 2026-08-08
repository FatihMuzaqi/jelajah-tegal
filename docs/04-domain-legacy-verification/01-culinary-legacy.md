# Culinary — verifikasi legacy dan keputusan migrasi

Status kepastian: **VERIFIED** terhadap source legacy pada 4 Agustus 2026.

## Bukti fitur aktif

| Area | Evidence | Route/fungsi aktif | Keputusan target |
|---|---|---|---|
| Publik | `services/core-service/src/modules/kuliner/routes.ts`, `createKulinerRouter` | `GET /kuliner`, detail, menu, review, table slot, favorite | Dipertahankan sebagai web route Laravel |
| Mitra | file yang sama, `KulinerController` | CRUD venue/menu/media, availability, slot, review reply | Dipertahankan dengan policy dan active-Mitra context |
| Reservasi | `models.ts`, `reserveTableSlot`/`releaseTableSlot`; `service.ts`, `getReservationCatalog` | Reservasi digunakan melalui kontrak service/repository, tetapi tidak diekspos langsung oleh router kuliner | Dibuat ulang sebagai workflow web eksplisit dan transaksional |
| Persistensi | `prisma/schema.prisma` model `RestoVenue` s.d. `RestoTableSlot` | Keluarga `Resto*` dipakai repository aktif | Dikonsolidasikan menjadi `Culinary*` canonical |

## Alur bisnis target

Mitra membuat venue draft, menu, fasilitas, media, jam operasional, dan slot meja; mengajukan moderasi; admin menerbitkan/menolak/menurunkan; publik hanya membaca venue published milik Mitra aktif; consumer dapat favorite/review dan mengajukan reservasi; Mitra mengonfirmasi atau menolak tanpa dapat mengakses tenant lain.

## Tidak dipindahkan

- Keluarga Prisma `CulinaryVenue*` generasi kedua tidak disalin berdampingan dengan `Resto*`; hanya satu skema canonical.
- `RestoNearestFromDestination` tidak menjadi tabel materialized; nearby memakai `catalog_locations` dan query spasial.
- Tidak ada checkout/menu delivery karena tidak memiliki route produksi aktif pada modul Culinary.
- Frontend `web-lokantara-main/app/(consumer)/page.tsx` hanya `ComingSoon`; tidak ada UI/domain call yang dipindahkan.

