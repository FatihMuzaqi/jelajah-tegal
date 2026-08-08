# Risk Register

| # | Risiko | Probabilitas | Dampak | Mitigasi | Gate/owner |
| ---: | --- | --- | --- | --- | --- |
| 1 | Cross-tenant access karena multi-Mitra dan role bercampur | Tinggi | Kritis | Tenant context, scoped policies, permission + ownership tests | Security gate |
| 2 | Port auth mengurangi keamanan refresh/MFA/lockout | Sedang | Kritis | Laravel session, MFA encrypted, rate limit, recovery, audit, security tests | Security owner |
| 3 | Payment webhook duplicate/out-of-order atau amount mismatch | Tinggi | Kritis | Signature+amount verification, unique event, idempotency, DB transaction | Payment gate |
| 4 | Ledger/claim tidak balance atau saldo legacy salah | Tinggi | Kritis | Double-entry, immutable journal, reconciliation, no mutable balance source | Finance approval |
| 5 | Marketplace dipaksakan ke V1 | Tinggi | Tinggi | Explicit V2 redesign gate | Product owner |
| 6 | KYC/bank/private media bocor | Sedang | Kritis | Encryption, private Storage, signed access, audit read, retention | Security/legal |
| 7 | Endpoint mismatch legacy disalin ke Laravel | Tinggi | Tinggi | Define Laravel internal contract from use cases; integration tests | Architecture |
| 8 | Model-only dianggap active feature | Tinggi | Tinggi | Evidence route/service/UI; matrix classification | Product/architecture |
| 9 | Consumer mock/ComingSoon terbawa ke production | Tinggi | Sedang | No production mocks; empty/error/loading states | QA gate |
| 10 | Mitra portal scope membesar karena belum ada UI matang | Tinggi | Tinggi | Surface MVP per permission and delivery slices | Product owner |
| 11 | Admin workflow matang hilang saat rewrite | Sedang | Tinggi | Use-case parity checklist, not code copy | Admin users/QA |
| 12 | Permission terlalu granular atau role-name checks kembali muncul | Sedang | Tinggi | Permission namespace governance, policy review, static test conventions | Security |
| 13 | Ticket di-scan lebih dari sekali saat concurrency | Sedang | Tinggi | Atomic conditional update/lock, unique validation semantics | Fulfillment test |
| 14 | Voucher membuat total/reversal tidak konsisten | Sedang | Tinggi | Limited V1 rules, snapshot, defer advanced policy | Commerce owner |
| 15 | Email/OAuth hanya bekerja di fake/local adapter | Sedang | Tinggi | Staging credential smoke, queue monitoring, fallback recovery | Release gate |
| 16 | Scheduler/queue tidak berjalan di deployment | Sedang | Tinggi | Health/heartbeat, failed job alert, runbook | Operations |
| 17 | Dashboard ArchitectUI ditiru secara ilegal | Rendah | Tinggi | Inspiration only; Bootstrap/components/assets original/licensed | Design review |
| 18 | Laravel 13/package compatibility berubah | Sedang | Sedang | Lock supported versions before implementation, proof-of-concept | Engineering |
| 19 | Analytics chart memberi angka menyesatkan | Tinggi | Sedang | V2 metric dictionary/event schema before ApexCharts | Data owner |
| 20 | Legacy data duplicate/orphan tidak dapat dipetakan | Tinggi | Tinggi | Profiling, quarantine, deterministic map, reconciliation report | Migration owner |

## Risiko terbesar

Risiko terbesar adalah kombinasi tenant isolation, payment idempotency, dan ledger correctness. Satu kesalahan dapat membocorkan data antar-Mitra atau menimbulkan kerugian finansial. Ketiganya adalah release gate, bukan backlog hardening setelah launch.

## Risk acceptance rules

- Risiko critical tidak boleh diterima secara implisit.
- Fitur dengan unresolved critical risk dipindah ke NEEDS_REDESIGN atau versi berikutnya.
- Setiap acceptance harus mempunyai owner, expiry/review date, compensating control, dan audit decision.

