# Architecture Decisions

Register ini merangkum keputusan target. Status Accepted berlaku untuk architecture baseline; Proposed memerlukan proof/version decision saat implementasi; Deferred sesuai scope.

| ADR | Keputusan | Status | Alasan/konsekuensi |
| --- | --- | --- | --- |
| ADR-001 | Modular Laravel monolith | Accepted | Satu deploy/runtime dan transaction boundary; domain dipisah namespace/action/policy |
| ADR-002 | Blade + Livewire + Alpine + Bootstrap + Vite | Accepted | Menghapus kebutuhan frontend REST internal; UI server-driven |
| ADR-003 | ArchitectUI hanya inspirasi pola visual | Accepted | Tidak menyalin source/aset/template berlisensi |
| ADR-004 | Stateful Laravel session untuk browser | Accepted | Refresh token legacy tidak diperlukan/dimigrasikan |
| ADR-005 | Satu identity/session untuk multi-role surfaces | Accepted | Surface dipilih dari effective permission/membership, bukan login terpisah |
| ADR-006 | Google OAuth server-side adapter/Socialite | Proposed | Verified identity linking, tetap tunduk MFA dan role internal |
| ADR-007 | TOTP MFA wajib admin/super-admin dan recent-MFA action sensitif | Accepted | Mitigasi account takeover/privilege operations |
| ADR-008 | Spatie dynamic RBAC + Policies | Accepted | Role bukan authorization tunggal; custom role didukung |
| ADR-009 | Tenant role scoped by mitra_id/team context | Proposed pending package verification | Mendukung multi-Mitra; selalu ditambah ownership policy |
| ADR-010 | Explicit /mitra/{mitra} plus session convenience | Accepted | Context terlihat dan dapat divalidasi; session bukan proof |
| ADR-011 | Action as write-use-case and transaction boundary | Accepted | Reusable dari Controller/Livewire/API/Job |
| ADR-012 | Eloquent direct by default; no universal repository layer | Accepted | Menghindari abstraction tanpa nilai; adapter/query service saat perlu |
| ADR-013 | Domain events after commit; outbox for critical integration | Accepted | Mengurangi lost event dan pre-commit side effects |
| ADR-014 | Database/email Notifications queued | Accepted | Durable inbox dan external delivery terpisah |
| ADR-015 | Laravel Storage provider-neutral; local + S3-compatible/R2 | Accepted | Portability, public/private lifecycle, direct upload |
| ADR-016 | Midtrans webhook inbox + idempotent application | Accepted | Replay/out-of-order safe, auditable |
| ADR-017 | DECIMAL database + decimal value object, never float | Accepted | Financial precision |
| ADR-018 | Immutable balanced double-entry ledger | Accepted | Saldo reconstructable; corrections via reversal |
| ADR-019 | QR uses random opaque token, hash at rest, atomic scan | Accepted | Prevent forgery/replay; no offline scan V1 |
| ADR-020 | Feature flag separated from permission | Accepted | Availability cannot grant authorization |
| ADR-021 | Tenant-aware cache keys and fail-closed security | Accepted | Prevent cross-tenant cache leak/stale privilege |
| ADR-022 | Structured JSON logs + append-only audit | Accepted | Operational observability distinct from compliance evidence |
| ADR-023 | MySQL 8 required for integration/concurrency tests | Accepted | SQLite cannot validate target locking/types/constraints |
| ADR-024 | API limited to external/future boundaries | Accepted | No duplicated internal REST layer |
| ADR-025 | AI integration behind adapter, queue, quota, flag, policy | Deferred/FUTURE | Cost/privacy/safety; AI cannot directly mutate sensitive domains |
| ADR-026 | ApexCharts only over defined server-side metrics | Accepted | UI chart does not define analytics semantics |
| ADR-027 | Queue classes by workload and monitored Scheduler | Accepted | Isolate critical/payment work and operationalize expiry/cleanup |
| ADR-028 | Request-scoped correlation and Mitra context | Accepted | Trace HTTP→job→provider without global state leakage |

## Cache architecture

Cache candidates: public catalog query fragments, master region/category, feature evaluation, settings, effective navigation/permission, and short-lived dashboard aggregates. Source-of-truth payment, ledger balance, ticket validation, claim decision, and idempotency are database-backed; cache may accelerate read but never authorize or settle alone.

Key structure includes app/version, environment, global or mitra scope, entity/query hash, locale, and cache version. Domain events invalidate targeted keys; TTL is safety net. Stampede protection/locks apply to expensive catalog/dashboard queries. Permission assignment changes invalidate Spatie and navigation caches synchronously.

## Feature flag architecture

Evaluation order: emergency global disable → environment/release constraint → global feature state → tenant entitlement/override → rollout rule → user eligibility. Result still passes policy. Flags have owner, description, default, start/end, audit, and cleanup date; permanent business entitlement moves to explicit domain state.

## AI boundary

Future AI endpoints reside in routes/api.php only when an external/mobile integration needs them; web can use Controller/Livewire calling the same Ai service. Provider request uses redacted context, timeout, retry/circuit breaker, cost token budget, prompt/version record, output validation and moderation. No AI-generated SQL, permission decision, financial posting, or automatic publication.

## Superseded legacy decisions

- Express/Next split and internal REST proxy are superseded by the monolith.
- Browser JWT refresh flow is superseded by Laravel session.
- Hardcoded role arrays are superseded by database permissions and policies.
- Mutable balance as authority is superseded by ledger.
- Mock/ComingSoon data and schema-only features do not become target modules.

## Architecture readiness

Architecture is ready for database design and implementation planning once open scope decisions affecting schema—tenant scoping mode, KYC retention, Mitra activation, commission, claim/withdrawal, QR token, media provider, region source, credential migration, retention—are closed or recorded as explicit implementation assumptions.

Status: ARCHITECTURE_COMPLETE_WITH_OPEN_DECISIONS.

