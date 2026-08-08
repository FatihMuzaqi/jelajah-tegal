# Error and Logging Standard

## Error categories

| Category | Web behavior | API behavior | Retry |
| --- | --- | --- | --- |
| Validation | Redirect/live field errors | 422 validation envelope | No |
| Authentication | Login/intended redirect or 401 | 401 | After credential/user action |
| Authorization | 403 or 404 to hide tenant resource | 403/404 | No |
| Not found | 404 page | 404 | No |
| Conflict/state | Flash actionable message | 409 | Only after state refresh |
| Rate limit | 429 page/message | 429 + Retry-After | Later |
| Provider unavailable | Safe retry message/status page | 502/503 | Job/backoff if safe |
| Unexpected | Generic 500 with correlation ID | 500 envelope | Investigate |

Expected domain exceptions include InsufficientAvailability, InvalidStateTransition, IdempotencyConflict, TenantContextMismatch, InsufficientBalance, TicketAlreadyUsed, ProviderSignatureInvalid. Exception handler maps them centrally; controller does not duplicate try/catch mapping.

## Web UX

Use Post/Redirect/Get, validation bag, flash success/warning/error, and persistent transaction status pages. Never display stack trace, SQL, provider payload, secret, or internal authorization detail. Livewire failures preserve safe form state and use accessible feedback.

## API envelope

API error shape is versioned and stable: code, message, correlation_id, and optional field errors/details safe for client. Machine code is English stable key; translated message is presentation. Success webhook response is provider-contract appropriate, not necessarily generic envelope.

## Request context

Middleware creates or validates correlation/request ID and attaches user_id, mitra_id, surface, route name, and deployment version to log context. Incoming ID is length/charset validated; server generates canonical ID. Queue job propagates correlation and causal event ID.

## Structured logging

Production logs are structured JSON. Levels:

- debug only non-production diagnostics;
- info successful significant lifecycle events, not every query;
- notice/warning recoverable anomaly, denied sensitive action, retry;
- error failed operation needing attention;
- critical data/security/financial integrity risk.

Do not log password, token, session ID, cookie, Authorization header, TOTP, bank/KYC plaintext, signed URL, Midtrans key, full webhook/OAuth payload, or arbitrary Form Request body.

## Audit versus application log

Application log is operational and may rotate. Audit log is durable business/security evidence. A login failure may create security log and aggregated audit as policy dictates; role/permission changes, KYC decision, payment application, ledger/claim, ticket validation, settings and sensitive exports always create audit entries.

## Observability signals

Metrics/alerts include request error/latency by route, login lockouts, queue lag/failure, scheduler heartbeat, storage failures, webhook signature failures, payment inbox age, paid-without-ledger/ticket, ticket replay, ledger imbalance, and tenant authorization denials. High-cardinality IDs remain logs/traces, not metric labels.

## Error reporting hygiene

Exception reporting fingerprints repeated failures, tags deployment/request/route and safe entity IDs, and excludes expected validation/authorization noise unless security-significant. Provider failures retain sanitized response code/reference, not body by default.

## Cache errors

Cache is optimization. Read-cache failure falls back to database when safe; permission/tenant/security checks never fail open. Cache write failure does not roll back valid core transaction unless cache is intentionally a lock/idempotency dependency, in which case behavior is explicit and monitored.

