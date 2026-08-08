# Multi-Mitra Context

## Context model

User dapat menjadi anggota nol, satu, atau banyak Mitra. MitraContext adalah request-scoped object berisi mitra_id, membership_id, user_id, enabled features, dan scope permission. Context tidak disimpan sebagai singleton lintas request/worker.

## Resolution flow

```mermaid
flowchart TD
    A[Request /mitra/{mitra}] --> B[Authenticated user]
    B --> C[Bind Mitra by ULID]
    C --> D{Active membership exists?}
    D -- No --> X[404 or 403 without leakage]
    D -- Yes --> E{User and Mitra active?}
    E -- No --> X
    E -- Yes --> F[Set request MitraContext]
    F --> G[Align session active_mitra_id]
    G --> H[Policy checks resource.mitra_id]
    H --> I[Action executes]
```

Explicit URL context adalah authority request. Session active_mitra_id adalah convenience untuk navigation/default redirect, bukan bukti authorization. Tenant switch action memvalidasi membership, regenerates relevant session context, records audit, then redirects to canonical Mitra URL.

## Middleware responsibilities

resolve-mitra middleware:

- requires route parameter;
- resolves non-deleted Mitra;
- verifies active membership and user/Mitra state;
- configures Spatie team/tenant context;
- binds MitraContext into service container request scope;
- adds mitra_id to logs/audit context;
- never queries or mutates business resource beyond context.

## Query rules

- Aggregate root tenant tables have mitra_id directly.
- Child resource is queried through parent relation or verified ancestor.
- Financial roots—order, payment, ticket, ledger journal/account, claim—carry explicit mitra_id even if derivable.
- Reusable query objects accept MitraContext explicitly.
- Global scopes may be defense-in-depth but are not sole protection because admin jobs/reporting require intentional cross-tenant access.
- unscoped queries are confined to named platform services with permission and audit.

## Spatie team integration

Tenant roles use Spatie team feature or equivalent supported scoping with mitra_id. Middleware sets team ID before role/permission evaluation and clears it after request/job. Platform role checks run without tenant scope. Exact package-version behavior must be verified before database migration.

## Queue and scheduler

Job payload includes mitra_id, actor_id, correlation_id, and immutable entity ID. Worker resolves fresh membership/feature state where authorization is still relevant. System jobs use explicit system actor and tenant list; no active session assumption. Context is always cleared between jobs.

## Cache isolation

Cache keys contain environment, tenant/global scope, resource/version, and locale as relevant. Permission caches are invalidated on role/permission/membership changes. Never cache a tenant query under user-only or global key.

## Cross-tenant operations

Admin reporting/moderation can operate across tenants only through dedicated query/action paths. A cross-tenant mode requires permission, filters, pagination, query limits, reason for sensitive exports, audit, and no reuse of active Mitra repository assumptions.

## Failure behavior

Mismatch route Mitra versus resource returns 404 where revealing existence is sensitive. Disabled membership redirects only on navigation GET; commands fail without state change. Changing active context invalidates tenant-specific UI/cache state but does not destroy the login session.
