# Deprecated and Excluded Features

## DEPRECATED

| Domain | Fitur | Kondisi legacy | Target | Versi | Dependency | Alasan |
| --- | --- | --- | --- | --- | --- | --- |
| Auth | Refresh token browser legacy | Opaque token diperlukan arsitektur API/Next lama | DEPRECATE | DEPRECATED | Laravel session | Monolith Blade/Livewire tidak membutuhkannya |
| Auth | Role dari claim/nama role saja | Service legacy masih memakai requireAnyRole di banyak tempat | REPLACE | DEPRECATED | Spatie permission, policies | Tidak cukup granular dan rawan tenant leak |
| Role | customer, mitra_owner, mitra_staff dalam format legacy | Role aktual dan masih dipakai | MAP TO SEED | DEPRECATED | Role migration map | Target memakai consumer, mitra-owner, mitra-staff |
| Frontend | Static/mock consumer catalog | Homepage mengandung mock data dan banyak ComingSoon | EXCLUDE | DEPRECATED | Real catalog query | Tidak boleh masuk production |
| Frontend | Next.js API proxy/session pattern | Berguna pada frontend terpisah | REPLACE | DEPRECATED | Laravel routes/session | Tidak relevan pada monolith |
| Backend | Duplicate schema/model families | Sebagian model hanya future/legacy atau duplikat domain | CONSOLIDATE | DEPRECATED | Data reconciliation | Mencegah dua source of truth |
| API | Endpoint aliases dan kontrak mismatch | Frontend/backend tidak selalu cocok | REPLACE | DEPRECATED | Laravel route contract | Jangan melestarikan drift |

## NOT_MIGRATED

| Domain | Fitur/data | Kondisi legacy | Target | Versi | Dependency | Alasan |
| --- | --- | --- | --- | --- | --- | --- |
| Security | Refresh token aktif, reset token, activation token, session | Runtime secret/credential legacy | REVOKE | NOT_MIGRATED | Cutover auth | Hash/token tidak berguna dan tidak aman dipindah |
| Security | Password yang algoritme/asalnya tidak dapat diverifikasi | Beberapa fase auth legacy berubah | RESET/REHASH ONLY | NOT_MIGRATED | Account recovery | Tidak mengimpor hash tanpa compatibility test |
| AI | AI Planner in-memory conversations | Scaffold tanpa durable LLM production | EXCLUDE DATA | NOT_MIGRATED | Future product decision | Mock state bukan data bisnis |
| Frontend | ComingSoon state dan dummy dashboard values | Presentation placeholder | EXCLUDE | NOT_MIGRATED | Real services | Tidak memiliki nilai migrasi |
| Tests | Memory repository fixtures/demo secrets | Test/demo only | RECREATE SAFELY | NOT_MIGRATED | Test strategy | Bukan production data |
| Integration | Raw provider payload yang mengandung secret/PII | Sebagian payload legacy tersimpan | REDACT/SELECTIVE | NOT_MIGRATED | Legal retention | Hanya field audit yang diperlukan dipindah |
| Storage | Orphan/unmanaged media tanpa ownership terverifikasi | Legacy mendukung URL eksternal | QUARANTINE | NOT_MIGRATED | Media ownership audit | Mencegah broken link/data leak |

## FUTURE yang sengaja dikecualikan dari roadmap committed

Social feed, Follow, Trip, dan AI Planner diklasifikasikan FUTURE. Capability ini tidak dihapus secara permanen, tetapi tidak mendapat tanggal atau migration sampai validasi produk, moderation/privacy, biaya, dan operating model disetujui.

## NEEDS_REDESIGN yang tidak boleh disamakan dengan deprecated

- Marketplace checkout, inventory, shipping, cancellation.
- Refund dan dispute lintas payment/ticket/voucher/ledger.
- Automated payout/withdrawal provider.
- CMS generik di luar banner.
- Analytics dan master-data console.

Data legacy terkait boleh dianalisis dan dipetakan, tetapi schema target tidak dibuat sebelum desain capability selesai.

## Data preservation rule

DEPRECATED capability tidak berarti seluruh data langsung dihapus. Order, payment, ticket, ledger, KYC, audit, dan consent tunduk pada retention/legal hold. Penghapusan atau anonymization dilakukan melalui keputusan migrasi data terpisah.

