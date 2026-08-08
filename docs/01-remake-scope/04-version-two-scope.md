# Version Two Scope

V2 dimulai hanya setelah acceptance gate V1 untuk tenant isolation, payment idempotency, ticket atomicity, dan balanced ledger lulus.

## Fitur V2

| Domain | Fitur | Kondisi legacy | Target | Versi | Dependency | Alasan |
| --- | --- | --- | --- | --- | --- | --- |
| Catalog | Rental | API catalog, availability, checkout, document, review/favorite relatif luas | REBUILD | VERSION_2 | V1 transaction kernel, private media, renter verification | Kapabilitas nyata tetapi menambah fulfillment dan dokumen sensitif |
| Catalog | Marketplace | Catalog/variant/stock tersedia; checkout/shipping belum production-ready | REDESIGN | VERSION_2 | Inventory ledger, cart, shipping, refund/dispute | Tidak aman diluncurkan memakai flow setengah jadi |
| Catalog | Virtual Tour | Persistent tour/panorama/hotspot dan link tourism tersedia | REBUILD | VERSION_2 | Media lifecycle, catalog moderation, performance | Enhancement discovery, bukan revenue foundation |
| Platform | Analytics | Admin surface ada, metric belum matang | REDESIGN | VERSION_2 | Stable audit/domain events, aggregation, ApexCharts | Chart harus punya definisi bisnis dan source jelas |
| Platform | Master data console | Halaman admin belum lengkap | REDESIGN | VERSION_2 | Taxonomy ownership, import/export, audit | V1 cukup master region/reference wajib |
| Platform | CMS lanjutan | Legacy nyata hanya banner | REDESIGN | VERSION_2 | Media, publishing workflow, moderation | Jangan menganggap model CMS sebagai fitur aktif |
| Transaction | Voucher lanjutan | Claim/validate aktif, reversal dan multi-scope belum final | EXTEND | VERSION_2 | Refund/cancel policy, budget ledger | V1 hanya rule sederhana |
| Transaction | Automated withdrawal/payout | Claim approve/reject ada, transfer masih manual | REDESIGN | VERSION_2 | Provider payout, reconciliation, ledger, dual control | Otomasi membawa risiko finansial tinggi |
| Platform | Broadcast campaign lanjutan | Admin broadcast ada | EXTEND | VERSION_2 | Consent, segmentation, delivery analytics | V1 dibatasi pesan operasional |

## Marketplace redesign boundary

Marketplace V2 harus menyelesaikan sebagai satu paket:

- product dan variant sebagai sellable unit;
- inventory movement/reservation, bukan stok mutable tanpa history;
- cart per Mitra dan price snapshot;
- checkout idempotent;
- shipping address, origin, rate, carrier, tracking, delivery evidence;
- cancellation, stock release, refund dan dispute;
- voucher reversal dan commission;
- payment reconciliation dan ledger posting;
- Mitra fulfillment surface serta consumer order tracking.

Catalog Marketplace dapat dibangun sebelum transaksi, tetapi tidak boleh dipresentasikan sebagai purchasable production sampai seluruh gate di atas selesai.

## Rental V2 boundary

Rental meliputi vehicle, availability, pricing, renter document, booking, payment, lifecycle pickup/return, cancellation, review/favorite, dan claim. Dokumen renter harus private, tenant-scoped, memiliki retention, dan aksesnya diaudit.

## Analytics V2

Metric minimum harus mempunyai nama, definisi formula, grain, timezone, source event, owner, dan late-arrival policy. ApexCharts hanya presentation. Data operasional V1 boleh ditampilkan sebagai count sederhana, tetapi tidak disebut analytics product.

## V2 entry criteria

1. V1 production stabil dan observability tersedia.
2. Refund/dispute policy disetujui sebelum Marketplace payment dibuka.
3. Inventory dan shipping provider dipilih.
4. Data retention Rental disetujui.
5. Event taxonomy analytics versioned dan diuji.

