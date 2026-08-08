# Event — verifikasi legacy dan keputusan migrasi

Status kepastian: **VERIFIED** terhadap source legacy pada 4 Agustus 2026.

## Bukti fitur aktif

| Area | Evidence | Route/fungsi aktif | Keputusan target |
|---|---|---|---|
| Publik | `services/core-service/src/modules/event/routes.ts`, `createEventRouter` | `GET /events`, detail, review dan favorite | Dipertahankan |
| Mitra | file yang sama, `EventController` | CRUD event, ticket type, media, reply review | Dipertahankan dan dilengkapi schedule/facility |
| Admin | file yang sama | `GET /admin/events`, `PATCH /admin/events/:id/moderate` | Dipertahankan dengan lifecycle canonical |
| Kuota | `models.ts`, `incrementUsedQuota`, `incrementTicketTypeUsedQuota`; `service.ts`, `checkQuota` | Kuota memiliki penggunaan nyata | Dipertahankan dengan row lock |

## Alur bisnis target

Mitra membuat event beserta schedule, deadline, media, fasilitas, panduan pengunjung dan tipe tiket; admin memoderasi; consumer melihat event published dan menerima tiket QR dari proses penerbitan terotorisasi; gatekeeper yang ditugaskan memvalidasi token satu kali dan seluruh tindakan diaudit.

## Tidak dipindahkan

- Checkout Event legacy tidak dianggap production-ready dan tidak direplikasi pada tahap ini; penerbitan tiket tahap 11 bersifat complimentary/manual, siap diikat ke order item pada modul transaksi.
- Status lama dikonsolidasikan ke lifecycle katalog canonical.
- Frontend consumer Event masih `ComingSoon`; UI dibuat ulang dengan Blade.

