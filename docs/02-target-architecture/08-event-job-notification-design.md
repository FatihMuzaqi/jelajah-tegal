# Event, Job, and Notification Design

## Event taxonomy

Domain events use past tense and describe committed facts: UserRegistered, MitraActivated, CatalogPublished, OrderPlaced, PaymentSettled, TicketIssued, TicketValidated, ClaimApproved, ClaimPaid. Events carry identifiers and minimal immutable facts, not full Eloquent models or sensitive documents.

Events are dispatched after commit. A database outbox is recommended for payment/financial events where loss between commit and queue publish is unacceptable.

## Synchronous listeners versus jobs

| Work | Mode | Reason |
| --- | --- | --- |
| State invariant and ledger posting | Synchronous in Action transaction | Required consistency |
| Write critical audit event | Same transaction or guaranteed outbox | Must not disappear |
| Email/notification delivery | Queued after commit | External/slow |
| Image metadata/cleanup | Queued | Retryable external storage |
| Broadcast fan-out | Batched queue | Volume control |
| Analytics projection | Queued, V2 | Eventual consistency acceptable |
| Provider reconciliation | Scheduled job | Periodic external check |

## Queue topology

Logical queues: critical, payments, notifications, media, default, analytics. Payment webhook state application may enter critical/payments after inbox persistence. Queue priority, worker count, timeout, retry/backoff, and failed-job alert are deployment configuration.

Jobs must be idempotent, serializable by IDs, bounded in runtime, and safe after entity state changes. Use unique jobs/overlap locks for order expiry, payment reconciliation, media deletion, and campaign fan-out as appropriate. Never serialize decrypted KYC/bank data.

## Retry policy

- Retry transient network/rate-limit failures with exponential backoff and jitter.
- Do not retry validation, authorization, invalid signature, or terminal domain conflict.
- Cap attempts and move to failed_jobs/dead-letter workflow.
- Manual retry records actor/reason and maintains original correlation/idempotency key.

## Notifications

Laravel Notifications supports database and email in V1. Channel choice respects user preferences except mandatory security/legal messages. Notification record stores safe presentation/data references; sensitive detail is fetched through authorized page, not embedded in email/database payload.

Representative notifications: VerifyEmail, ResetPassword, OwnerInvitation, KycDecision, OrderStatusChanged, PaymentConfirmed, TicketIssued, ClaimStatusChanged, SuspiciousLogin.

## Broadcast

Operational broadcast is a campaign aggregate with audience definition, schedule, status, content version, creator, and counts. Fan-out snapshots recipients at send time or records query version. Consent, rate limits, cancel semantics, and deduplication are mandatory. Marketing automation remains out of scope V1.

## Scheduler

routes/console.php schedules:

| Schedule | Task | Guard |
| --- | --- | --- |
| Every minute/few minutes | Expire pending orders and release reservations | overlap lock, deterministic batches |
| Frequent | Reconcile pending Midtrans payments | provider rate limit, idempotent |
| Daily | Expire KYC/feature/voucher/banner as policy dictates | audited state transition |
| Daily | Clean abandoned/expired media | attachment check, retry external delete |
| Daily | Ledger integrity checks | alert only; never auto-balance |
| Periodic | Prune sessions/tokens/audit per retention | legal retention policy |

Use withoutOverlapping/onOneServer semantics where deployment supports. Scheduler heartbeat and last-success metrics are monitored.

## Real-time

Livewire polling is default for low-volume inbox/status. SSE or WebSocket is introduced only if latency/scale requires, with authenticated user/tenant channels. Real-time delivery never replaces durable database notification or domain state.

