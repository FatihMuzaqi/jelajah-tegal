# Service and Action Guidelines

## Action first

Setiap mutating use case mempunyai Action kecil dengan satu public entry point. Controller, Livewire, API, dan Job memanggil Action yang sama. Input berupa typed data object/value object atau validated associative contract; output berupa model/result object, bukan HTTP response.

Contoh kategori: RegisterConsumer, CreateMitra, InviteMitraMember, PublishTourismDestination, PlaceOrder, ApplyMidtransNotification, ValidateTicket, PostLedgerJournal, ApproveClaim.

## Action responsibilities

- Recheck critical authorization/invariants independent of UI.
- Own database transaction bila mengubah satu consistency boundary.
- Lock/read rows in deterministic order for quota, ticket, payment, and ledger.
- Make idempotency decision before side effects.
- Persist state and audit atomically where required.
- Dispatch domain events after commit.
- Return deterministic result for duplicate request.

Action tidak mengirim redirect, flash, Blade view, Livewire event, atau vendor HTTP response.

## Service categories

| Type | Kegunaan | Contoh konseptual |
| --- | --- | --- |
| Domain service | Logic lintas beberapa model dalam satu domain | Pricing, availability, ledger posting |
| Integration adapter | Boundary vendor | MidtransGateway, GoogleIdentityProvider, ObjectStorage |
| Query service | Read model/list/report kompleks | AdminOrderQuery, MitraLedgerStatement |
| Context service | Request/job context | MitraContext, RequestContext |
| Application coordinator | Orkestrasi multi-step tanpa menjadi domain entity | CheckoutService |

External services menggunakan interface/port agar fake dapat dipakai pada test dan provider diganti tanpa memengaruhi Action.

## Transaction boundary

Gunakan database transaction untuk order+reservation, payment state+ledger+ticket outbox, ticket validation, claim approval+ledger, role assignment+audit, dan state transitions sensitif. Jangan membungkus provider network call di transaction panjang. Pola:

1. prepare/persist intent;
2. call provider dengan idempotency/reference setelah commit bila memungkinkan;
3. apply verified result dalam transaction baru;
4. schedule retry/reconciliation.

## Financial and temporal values

Money tidak pernah float. Database DECIMAL; PHP menggunakan decimal string/value object dan BCMath atau library yang disetujui. Currency wajib eksplisit. Semua time disimpan UTC dan ditampilkan pada timezone user/platform; date-only service date tidak dikonversi sebagai timestamp.

## Eloquent guidelines

- Models expose relationships, casts, guarded/fillable policy, small state predicates/scopes.
- Eager-load knowingly; prevent lazy loading pada non-production warning/test.
- Avoid model observers for critical invisible side effects. Use explicit Actions/events.
- Avoid mass assignment for status, ownership, money, permission, and audit fields.
- Use enum casts for status; unknown database value fails visibly.
- Soft delete only recoverable aggregates, never as substitute for transaction history.

## Validation and authorization

Form Request handles syntax and contextual validation using read-only queries. Race-sensitive rules—stock/quota, uniqueness, balance, ticket state—are revalidated under transaction locks by Action. Policy authorization happens before calling Action; Action validates actor/context for defense-in-depth.

## Idempotency

Checkout, provider application, ticket issuance, ledger posting, notification fan-out, and destructive retryable jobs carry idempotency key/event key. Store request fingerprint and prior result; same key+different payload is conflict.

## Anti-patterns

- Fat Livewire component that directly changes multiple models.
- Generic BaseService/BaseRepository with hidden queries.
- Catching Throwable and returning success/false.
- Dispatching queued events before transaction commit.
- Model observer creating ledger/ticket/payment implicitly.
- Authorization based only on role name or UI visibility.
- Business relation stored only in JSON.

