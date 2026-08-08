# Testing Strategy

Pest direkomendasikan untuk expressive datasets, dengan PHPUnit underneath. PHPUnit langsung tetap acceptable bila tim memilih konsistensi tersebut. Test suite tidak bergantung pada production services.

## Test pyramid

| Level | Fokus | Contoh |
| --- | --- | --- |
| Unit | Enum/value object/calculator/state rule | Money rounding, commission, status transition, QR token hash |
| Action/service | Use case dan transaction | PlaceOrder, ApplyWebhook, ValidateTicket, ApproveClaim |
| Policy | Permission+tenant+ownership matrix | Cross-tenant denial, inactive membership, recent MFA |
| Feature HTTP | Route/middleware/request/response | Auth, verification, scoped binding, webhook |
| Livewire | Form state, authorize setiap mutation, pagination/filter | Mitra catalog, admin KYC, role matrix |
| Integration | MySQL locking/constraints/provider adapters | Quota concurrency, unique idempotency, ledger balance |
| Browser smoke | Critical user journey | Register→checkout→payment status→ticket; gatekeeper scan |

## Critical suites

### Authentication

Register, email verification expiry/replay, reset one-use and session revoke, login lock/unlock, suspension, OAuth state/linking, MFA enroll/challenge/recovery/replay, session fixation prevention, multi-surface redirect.

### Authorization and tenancy

Dataset across six seed roles, custom role, direct permission, two Mitras, active/inactive membership, suspended tenant, feature disabled, resource from other tenant, admin without override, super-admin without recent MFA. Tests assert both hidden UI and server denial; server denial is authoritative.

### Payment and finance

Valid/invalid signature, amount mismatch, duplicate/out-of-order webhook, retry after partial failure, concurrent processing, order expiry, one ledger journal per event, debit=credit, no float drift, insufficient claim balance, reversal semantics when introduced.

### Ticket

Only paid eligible item issues ticket; token not logged; invalid/expired/cancelled/out-of-scope results; two concurrent scans yield exactly one accepted; admin without assignment denied.

### Storage

Storage fake for ordinary tests, adapter contract for local/S3-compatible, presign intent/finalize, MIME/size/ownership, private URL authorization, orphan cleanup retry, KYC access audit.

## Database strategy

Unit/feature tests can use isolated test database, but concurrency, locking, generated constraints, decimal, JSON, and collation tests must run against MySQL 8—not SQLite. Parallel tests use separate schemas/databases. Factories create valid tenant ownership by default and explicit invalid states when testing defenses.

## External adapters

Fake Midtrans/Google/Storage/Mail for deterministic tests plus adapter contract tests. Staging smoke uses provider sandbox/real credentials under controlled environment; secrets never committed. Webhook fixtures are sanitized and signature-generation helper is test-only.

## Queue and scheduler

Queue fake asserts dispatch-after-commit; integration worker tests idempotent retry and context clearing. Scheduler commands test batching, overlap prevention, dry-run where relevant, and no auto-balancing financial anomalies.

## Quality gates

- All migrations rollback/forward tested when migration stage begins.
- Static analysis and formatting target agreed before implementation.
- Zero skipped critical security/payment/ledger tests at release.
- Coverage is risk-based; line percentage does not replace scenario matrix.
- Mutation/contract tests considered for money, policy, state transition.
- Accessibility smoke for auth/forms/tables/modal and responsive surfaces.
- Performance/load tests for discovery, checkout contention, webhook bursts, scan bursts, and admin exports.

## Migration parity later

Legacy parity tests compare business outcomes and reconciled counts, not old HTTP envelope or internal implementation. Deprecated endpoint/mocks are explicitly excluded.

