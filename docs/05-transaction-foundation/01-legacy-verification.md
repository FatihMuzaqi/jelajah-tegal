# Verifikasi legacy Voucher, Order, dan Checkout

Status: **VERIFIED** terhadap source legacy pada 4 Agustus 2026.

| Area | Evidence | Kondisi legacy | Keputusan target |
|---|---|---|---|
| Checkout pusat | `services/core-service/src/modules/transaction/checkout/routes.ts`, `CheckoutService.createCheckout` | Route aktif untuk checkout/order/tiket dan reservasi kuliner, tetapi memuat banyak cabang domain | Satu orchestration service dengan adapter domain dan order snapshot canonical |
| Tourism | `modules/tourism/checkout/routes.ts` dan `TourismCheckoutService` | Checkout aktif dan menggunakan quota | Dipertahankan dengan lock `availabilities` |
| Event | `modules/event/checkout/routes.ts` dan `EventCheckoutService` | Checkout aktif, quota ticket type digunakan | Dipertahankan dengan `reserved_quantity` terpisah dari tiket issued |
| Rental | `modules/rental/checkout/routes.ts`, `availability.ts` | Checkout dan pemeriksaan overlap tersedia | Dipertahankan, tetapi hanya booking approved yang dapat menjadi order |
| Culinary | `transaction/checkout/routes.ts`, `createKulinerReservationCheckout` | Reservasi kuliner masuk transaction module | Dipertahankan sebagai zero/fee reservation order; kapasitas tetap dimiliki reservation workflow |
| Accommodation | model/offer/availability aktif pada target Laravel | Checkout legacy terfragmentasi antargenerasi penginapan | Dibuat ulang memakai room offer dan lock availability per malam |
| Voucher | `modules/voucher/routes.ts`, `VoucherService.validate`, `recordUsage` | CRUD admin, claim, validate, limit dan usage aktif | Dipertahankan; percentage disimpan basis points dan applicability dinormalisasi |
| Payment/Ledger | `modules/transaction/payment/*`, `ledger/*` | Webhook dan ledger aktif, tetapi lifecycle legacy tidak disalin mentah | Fondasi payment + immutable double-entry; posting hanya saat capture |

Marketplace checkout tidak diberi route atau adapter sampai transactional stock tersedia.

