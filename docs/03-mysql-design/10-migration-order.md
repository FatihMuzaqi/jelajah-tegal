# Migration Order

This is future Laravel migration sequencing, not migration implementation.

| Wave | Tables/constraints | Reason/gate |
| --- | --- | --- |
| 0 | MySQL charset/collation conventions; temporary legacy_id_map outside final domain | Deterministic mapping and environment |
| 1 | users, user_credentials, password reset, sessions, recovery, OAuth | Identity root |
| 2 | service_types, regions, categories, facilities | Reference parents |
| 3 | mitras, then media_assets; add cyclic logo/avatar media FKs afterward | Tenant/storage roots |
| 4 | mitra_members/invitations/features/requests/bank/KYC | Tenant operations |
| 5 | roles, permissions, Spatie pivots | users and mitras now exist |
| 6 | catalog_entities, catalog_locations, catalog_offers | Shared catalog root |
| 7 | V1 domain extension/children | Four catalog domains |
| 8 | catalog_media/hours/facilities/availabilities | Shared children after parents |
| 9 | idempotency_keys, orders, order_items | Commerce root |
| 10 | payments, webhook inbox | Payment roots |
| 11 | gatekeeper_assignments, tickets, validation logs | Event/destination/order parents exist |
| 12 | vouchers/claims/usages, favorites/reviews/replies | Transaction and entity parents exist |
| 13 | ledger_accounts, withdrawal_claims, ledger_journals, ledger_lines, mitra_balances | Add circular claim FK after both exist |
| 14 | notifications/deliveries, broadcasts, audit, moderation/actions, banners, outbox | Platform capabilities |
| 15 | Laravel jobs/batches/failed/cache tables | Exact framework-version schema |
| V2 | rental, marketplace, virtual-tour tables | Separate approved release migrations only |

## Cyclic FK handling

- users/profile/mitras reference media; create roots first and add media FK later.
- gatekeeper targets event/tourism; create after catalog.
- reviews reference order_items; create after commerce.
- ledger_journals references withdrawal_claims while claims may record journal lineage; add final FK in separate migration.

Temporary omission must be resolved within the same deployment wave; final schema has constraints.

## Deployment migration rules

- One concern per migration with explicit names for indexes/FKs.
- No destructive rename/drop in same release as code switch; use expand/migrate/contract.
- Large backfill runs batched/restartable outside request path.
- Enums stored as strings need compatible code deployment order.
- Production migrations never disable FK/check permanently.
- Every migration stage has forward/rollback feasibility and backup/reconciliation gate.

## V1 schema gate

Do not generate migrations until unresolved schema-impact decisions close: Spatie team mode/version, KYC types/retention, Mitra activation, commission/claim semantics, QR token, media provider metadata, region dataset/license, credential migration, data retention.

