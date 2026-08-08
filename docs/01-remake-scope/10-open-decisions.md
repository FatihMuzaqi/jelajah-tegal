# Open Decisions

Keputusan berikut belum menghalangi status SCOPE_COMPLETE karena capability terkait dibatasi atau ditunda. Namun keputusan P0 harus ditutup sebelum desain database/migration untuk area tersebut.

| ID | Keputusan | Opsi utama | Rekomendasi | Deadline |
| --- | --- | --- | --- | --- |
| OD-01 | Bentuk tenant scope Spatie Permission | Role global + policy tenant; role per tenant; team feature | Gunakan team/tenant scope yang teruji ditambah policy ownership | Sebelum schema RBAC |
| OD-02 | Apakah MFA wajib untuk Mitra owner finance | Admin saja; admin+owner finance | Wajib step-up untuk perubahan bank/claim high risk | Sebelum security acceptance |
| OD-03 | Recovery MFA | Recovery codes; super-admin reset; keduanya | Codes + audited super-admin recovery | Sebelum auth build selesai |
| OD-04 | Google account linking collision | Auto-link verified email; explicit link | Explicit/controlled link untuk akun existing sensitif | Sebelum OAuth production |
| OD-05 | KYC document types dan retention | Minimum internal; legal standard | Finalisasi dengan legal/operations | Sebelum KYC migration |
| OD-06 | Definition Mitra active | Admin approval saja; KYC+bank+module | State machine eksplisit per capability | Sebelum onboarding acceptance |
| OD-07 | Empat catalog diluncurkan serentak atau wave | Big bang; tourism/event lalu lainnya | Satu schema/kernel, release flag per domain | Planning V1 |
| OD-08 | Culinary payment scope | Reservasi unpaid; deposit/full payment | Mulai reservation rule sederhana yang disetujui | Sebelum culinary checkout |
| OD-09 | Accommodation cancellation/no-show | Flexible/fixed per property | Policy versioned dan snapshot order | Sebelum accommodation payment |
| OD-10 | Commission model | Fixed, percent, per domain/Mitra | Effective-dated rule + snapshot | Sebelum ledger implementation |
| OD-11 | Claim versus withdrawal terminology | Satu entity; request+payment entities | Pisahkan request state dan payout evidence | Sebelum finance schema |
| OD-12 | Payout V1 | Manual transfer evidence; provider API | Manual dual-control V1 | Sebelum operations launch |
| OD-13 | Refund/dispute | V1 minimal; V2 full | Defer, tetapi siapkan support/manual runbook | Sebelum terms published |
| OD-14 | Voucher cancellation/reversal | Consume permanent; release/reverse | Reserve/apply/release state machine | Sebelum voucher launch |
| OD-15 | Ticket QR contents | Public ticket id; signed/encrypted token; random secret hash | Random single-use token, hash at rest | Sebelum ticket schema |
| OD-16 | Notification channels | In-app/email/push/WhatsApp | V1 in-app+email; channel opt-in | Sebelum queue topology |
| OD-17 | Media provider | Local/S3-compatible/R2 | Laravel Storage adapter; provider-neutral metadata | Sebelum media migration |
| OD-18 | Source region dataset | Legacy wilayah; external canonical | Validate license/version, deterministic codes | Sebelum master import |
| OD-19 | Legacy password/data migration | Rehash compatible; reset all | Compatibility test, otherwise forced reset | Before cutover |
| OD-20 | Data retention/anonymization | Per table/ad hoc | Legal retention matrix before migration | Before migration plan |
| OD-21 | Marketplace carrier/inventory/refund | Multiple providers/policies | Product ADR before V2 schema | V2 inception |
| OD-22 | Analytics metric dictionary | Copy admin cards; new event taxonomy | New versioned metric dictionary | V2 inception |

## Decisions already closed by this scope

- Browser target uses Laravel session; refresh token legacy is not migrated.
- Authorization is permission-driven and tenant-aware.
- Super-admin is a dynamic role seed with explicit RBAC management capabilities.
- Portal Consumer dan Mitra are rebuilds; Admin workflows are functionally retained.
- CMS V1 is banner only.
- Marketplace, refund, dispute, analytics, generic CMS/master data are not V1 core.
- Dashboard uses original Bootstrap/Blade/Livewire implementation inspired by general ArchitectUI patterns only.

## Required next-stage inputs

Sebelum database design/migration, close OD-01, OD-05, OD-06, OD-10, OD-11, OD-15, OD-17, OD-18, OD-19, dan OD-20 atau document explicit assumptions with owner approval.

Status: SCOPE_COMPLETE.

