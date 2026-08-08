# Feature Retention Matrix

Target menjelaskan perlakuan remake: RETAIN, REBUILD, MERGE, REDESIGN, DEPRECATE, atau EXCLUDE. Versi menggunakan klasifikasi yang diwajibkan.

| Domain | Fitur | Kondisi legacy | Target | Versi | Dependency | Alasan |
| --- | --- | --- | --- | --- | --- | --- |
| Identity | Registrasi Consumer | Backend route/service aktif; frontend form tersedia | REBUILD | VERSION_1_CORE | User, email delivery, rate limit | Entry point consumer |
| Identity | Login | Aktif untuk role lintas surface | REBUILD | VERSION_1_CORE | Session, lockout, audit | Fondasi seluruh surface |
| Identity | Logout | Aktif dengan revocation legacy | REBUILD | VERSION_1_CORE | Laravel session | Wajib mengakhiri sesi |
| Identity | Email verification | Backend aktif; delivery bergantung konfigurasi | REBUILD | VERSION_1_CORE | Queue, mail provider | Mencegah akun palsu |
| Identity | Reset password | Route/token aktif; live SMTP belum selalu terverifikasi | REBUILD | VERSION_1_CORE | Queue, mail, token hash | Recovery dasar |
| Identity | Google OAuth | Flow backend/frontend ada; live credential belum selalu diuji | REBUILD | VERSION_1_SUPPORTING | Google credentials, account linking | Convenience, bukan satu-satunya login |
| Identity | Refresh token legacy | Opaque rotating token aktif di API legacy | DEPRECATE | NOT_MIGRATED | Laravel session | Blade/Livewire memakai session; token aktif tidak aman dipindah |
| Identity | MFA Admin | Route/service/TOTP aktif | REBUILD | VERSION_1_CORE | Encryption key, recovery, audit | Wajib untuk admin dan super-admin |
| Identity | Recovery code | Model/desain ada; kesiapan flow tidak sepenuhnya konsisten | REBUILD | VERSION_1_SUPPORTING | MFA, secure display/reset | Menghindari recovery manual saja |
| Identity | Account lock | Failed-login/lockedUntil aktif | REBUILD | VERSION_1_CORE | Login, scheduler optional | Brute-force control |
| Identity | Account suspension | Guard/status aktif | RETAIN | VERSION_1_CORE | User status, policy, audit | Kontrol platform |
| Identity | Profil pengguna | API aktif | REBUILD | VERSION_1_CORE | Media, region | Consumer identity |
| Identity | Preferensi notifikasi | Field/API profile tersedia | RETAIN | VERSION_1_SUPPORTING | Notification channels | Consent per kanal |
| Authorization | Dynamic role/permission | Legacy role derivation masih bercampur role tetap | REDESIGN | VERSION_1_CORE | Spatie Permission, tenant scope | Requirement utama target |
| Mitra | Pembuatan Mitra oleh Admin | API dan admin UI tersedia | REBUILD | VERSION_1_CORE | RBAC, region, audit | Onboarding terkendali |
| Mitra | Aktivasi owner melalui undangan | Activation flow aktif | REBUILD | VERSION_1_CORE | Email queue, token | Tidak ada self-provisioning Mitra |
| Mitra | Mitra owner | Membership role aktif | RETAIN | VERSION_1_CORE | Mitra membership, permissions | Pemilik tenant |
| Mitra | Mitra staff | CRUD staff/membership aktif | RETAIN | VERSION_1_CORE | Invitation, permissions | Operasi harian |
| Mitra | Gatekeeper | Module, assignment, ticket scan aktif | REBUILD | VERSION_1_CORE | Ticket scope, permissions | Validasi QR terpisah |
| Mitra | Profil bisnis | API aktif; Mitra UI belum matang | REBUILD | VERSION_1_CORE | Media, region | Identitas tenant |
| Mitra | Fitur bisnis Mitra | Module/feature enablement aktif | REDESIGN | VERSION_1_CORE | Feature flag, permission | Jangan hardcode per role |
| Mitra | Rekening bank | API aktif dan masking disebutkan | REBUILD | VERSION_1_CORE | Encryption, audit, withdrawal | Payout/claim |
| Mitra | KYC | Data dan admin workflow ada, rule legal belum final | REBUILD minimum | VERSION_1_CORE | Private media, moderation, legal policy | Aktivasi/finance trust |
| Mitra | Feature request | API/admin workflow aktif | RETAIN | VERSION_1_SUPPORTING | Feature registry, audit | Permintaan aktivasi modul |
| Mitra | Mitra aktif/suspend | API/status aktif | RETAIN | VERSION_1_CORE | KYC, admin approval | Lifecycle tenant |
| Mitra | Multi-Mitra membership | Endpoint memberships dan selected Mitra tersedia | REDESIGN | VERSION_1_CORE | Tenant context, scoped RBAC | User dapat tergabung di banyak Mitra |
| Catalog | Tourism | CRUD, availability, review/favorite aktif | REBUILD | VERSION_1_CORE | Mitra, media, region, moderation | Domain utama Lokantara |
| Catalog | Accommodation | CRUD room/facility/availability/booking aktif | REBUILD | VERSION_1_CORE | Mitra, media, transaction | Domain matang dan bernilai bisnis |
| Catalog | Culinary | CRUD menu/slot/reservation aktif | REBUILD | VERSION_1_CORE | Mitra, media, transaction | Domain utama discovery |
| Catalog | Event | CRUD, moderation, ticket type/quota aktif | REBUILD | VERSION_1_CORE | Mitra, payment, QR | Flow end-to-end kuat |
| Catalog | Rental | Backend relatif luas; surface target belum prioritas | RETAIN LATER | VERSION_2 | Availability, document, payment | Mengurangi beban V1 |
| Catalog | Marketplace | Catalog ada, checkout/stock/shipping belum production-ready | REDESIGN | VERSION_2 | Product variant, inventory, shipping, refund | Risiko transaksi tinggi |
| Catalog | Virtual Tour | Backend persistent ada; bukan kebutuhan transaksi inti | RETAIN LATER | VERSION_2 | Media panorama, catalog link | Enhancement discovery |
| Interaction | Review | Aktif pada beberapa domain, rule verified-review tidak seragam | MERGE PATTERN | VERSION_1_SUPPORTING | Paid order/fulfillment, moderation | Trust; wajib konsisten per domain |
| Interaction | Favorite | Aktif lintas domain | MERGE PATTERN | VERSION_1_SUPPORTING | User, catalog | Nilai UX dengan kompleksitas rendah |
| Interaction | Social feed | Backend ada; bukan core remake | EXCLUDE V1 | FUTURE | Moderation, privacy, scale | Fokus transaksi dulu |
| Interaction | Follow | Model/API social tersedia tetapi bukan core | EXCLUDE V1 | FUTURE | Social graph/privacy | Tidak kritis |
| Interaction | Trip | Implementasi legacy ada menurut matrix, consumer web belum matang | EXCLUDE V1 | FUTURE | Social, catalog, sharing | Perlu product validation |
| Interaction | AI Planner | Scaffold/in-memory, tanpa LLM production | REDESIGN | FUTURE | Trip/catalog, LLM, cost/privacy | Mock/scaffold bukan fitur production |
| Transaction | Order | Shared transaction module aktif | REBUILD | VERSION_1_CORE | Catalog offer, user, Mitra | Agregat transaksi utama |
| Transaction | Order item | Aktif sebagai snapshot resource | REBUILD | VERSION_1_CORE | Order, catalog offer | Integritas harga/fulfillment |
| Transaction | Payment Midtrans | Adapter/webhook aktif; perlu hardening saat port | REBUILD | VERSION_1_CORE | Idempotency, queue, ledger | Pembayaran V1 |
| Transaction | Voucher | API claim/validate tersedia; reversal policy belum lengkap | RETAIN LIMITED | VERSION_1_SUPPORTING | Order, budget, reversal policy | Diskon setelah checkout stabil |
| Transaction | QR ticket | Aktif untuk tourism/event | REBUILD | VERSION_1_CORE | Paid order, secure token | Fulfillment digital |
| Transaction | Ticket validation | Scan atomik dan log aktif | REBUILD | VERSION_1_CORE | Gatekeeper scope, audit | Operasi lapangan |
| Transaction | Claim | Owner request/admin approve-reject aktif; transfer manual | REBUILD | VERSION_1_CORE | Bank verified, ledger | Settlement minimum |
| Transaction | Withdrawal | Sebagian menyatu dengan claim/manual transfer | REDESIGN | VERSION_1_SUPPORTING | Claim state, payout policy | Pisahkan request, approval, payment |
| Transaction | Ledger | Balanced journal disebut aktif | REBUILD | VERSION_1_CORE | Payment, claim, commission | Source of truth saldo |
| Transaction | Refund | Workflow API tidak lengkap | REDESIGN | NEEDS_REDESIGN | Payment, ticket/voucher reversal, ledger | Tidak aman dipaksakan ke V1 |
| Transaction | Dispute | Workflow API tidak lengkap | REDESIGN | NEEDS_REDESIGN | Provider, evidence, ledger | Perlu policy lengkap |
| Platform | Notification | Inbox/read/SSE aktif | REBUILD | VERSION_1_SUPPORTING | Queue, preferences | Komunikasi transaksi |
| Platform | Broadcast | Admin endpoints/UI tersedia | REBUILD LIMITED | VERSION_1_SUPPORTING | Queue, audience, consent | Operasional; bukan marketing suite |
| Platform | CMS banner | Admin CRUD tersedia; CMS lain belum nyata | REBUILD | VERSION_1_SUPPORTING | Media, schedule | Scope CMS yang terbukti |
| Platform | Moderation | Admin surface dan beberapa domain aktif | REBUILD | VERSION_1_CORE | Catalog/review, permission, audit | Menjaga kualitas konten |
| Platform | Audit log | Backend/admin UI tersedia | REBUILD | VERSION_1_CORE | Actor, request context | Security/compliance |
| Platform | Master region | Location/customer/catalog menggunakan region | REBUILD | VERSION_1_CORE | Seed/import wilayah | Dependency catalog/alamat |
| Platform | Settings | Admin config tersedia | REBUILD | VERSION_1_CORE | Encryption, RBAC, audit | Konfigurasi runtime bisnis |
| Platform | Analytics | Admin page ada; definisi metric belum matang | REDESIGN | VERSION_2 | Stable events, aggregation, ApexCharts | Jangan membuat chart dari metric ambigu |
| Platform | Media | Presigned R2 flow aktif | REBUILD | VERSION_1_CORE | Laravel Storage, private/public policy | Shared dependency |
| Platform | Feature flag | Ada module/feature enablement terfragmentasi | MERGE | VERSION_1_CORE | Settings, Mitra feature registry | Controlled rollout |
| Platform | Master data generik | Admin page belum lengkap | REDESIGN | VERSION_2 | Domain taxonomy governance | V1 hanya region dan reference wajib |

## Kebijakan retention

- RETAIN/REBUILD bukan copy schema atau code; business capability dipertahankan dengan desain Laravel baru.
- VERSION_1_SUPPORTING tidak boleh menahan launch core kecuali menjadi dependency keamanan/transaksi.
- NEEDS_REDESIGN tidak boleh menghasilkan migration sebelum open decision ditutup.
- NOT_MIGRATED berarti data/artefak runtime legacy tidak dipindahkan, walaupun capability penggantinya tersedia.

