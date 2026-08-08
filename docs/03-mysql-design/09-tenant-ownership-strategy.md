# Tenant Ownership Strategy

## Model

Single database/shared schema row-level tenancy. mitras is tenant root. users is global and joins tenants through mitra_members. Every tenant action resolves explicit MitraContext from /mitra/{mitra} plus active membership.

## Ownership matrix

| Data class | mitra_id direct | Allowed inherited path |
| --- | --- | --- |
| Mitra/profile/member/KYC/bank/features | Yes | None |
| catalog_entities/offers | Yes | None |
| Domain extension | Usually no | extension → catalog_entity → mitra |
| Catalog media/hour/facility/location | No | child → catalog_entity → mitra |
| Availability | Yes | also offer → entity; both must match |
| Order/item/payment/ticket | Yes | Other FKs additionally verify same tenant |
| Ledger/claim/balance | Yes or explicit system/user account | No ambiguous owner |
| Review reply | Yes | review → entity must match |
| CMS/broadcast/settings | Nullable | null means platform, non-null tenant |
| Audit/moderation/notification | Nullable/explicit target | null means platform context |

Direct duplication on financial roots is intentional for isolation, index, and reconciliation. Action validates equality; migration integrity report must show zero mismatches.

## Database constraints

- Physical mitra_id FKs use RESTRICT.
- Tenant-scoped business uniques include mitra/scope key.
- Nullable global scope uses generated sentinel scope_key to prevent duplicate NULL records.
- Child FK replaces JSON/resource ID wherever a common parent exists.
- Cross-table same-tenant equality is enforced by Action plus integrity audit; composite FKs may be introduced if they remain maintainable.

## Access control data

Tenant roles/permissions carry mitra_id scope compatible with Spatie team mode. mitra_members is canonical membership; role assignment does not create membership. Revoking membership invalidates tenant permission/navigation cache and active context.

## Global administrators

admin/super-admin role does not automatically bind tenant. Cross-tenant operations use dedicated platform queries/actions with explicit permission, reason for sensitive access/export, audit, and no reuse of hidden tenant global scope. Ticket scan still needs assignment.

## Jobs and cache

Job payload includes mitra_id, actor_id/system actor, entity ID, correlation/idempotency key. Worker sets/clears team context per job. Cache key includes environment + global/mitra scope + version. Security checks query current state or fail closed; cache cannot grant access.

## Lifecycle

Suspended Mitra keeps data and finance evidence. It disables new publication/purchase/claim according to policy while allowing support/reconciliation. Hard purge requires legal retention, zero unresolved balance/claims, media cleanup, user anonymization decision, and approval audit.

## Integrity queries required

- member/role assignment scope matches;
- catalog offer/entity Mitra matches;
- availability offer/mitra matches;
- order item/order/offer Mitra matches;
- payment/order Mitra matches;
- ticket/order item Mitra matches;
- reply/review catalog Mitra matches;
- ledger journal/account/source tenant consistent.

