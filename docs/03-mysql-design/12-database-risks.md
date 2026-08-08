# Database Risks

| # | Risk | Impact | Mitigation/gate |
| ---: | --- | --- | --- |
| 1 | 157 models/79 enums copied mechanically | Duplicate/unused schema and ambiguous truth | Canonical map and excluded list |
| 2 | Mitra generations cannot be matched safely | Wrong tenant ownership | Deterministic evidence + quarantine/manual review |
| 3 | Resto/Culinary duplicate rows conflict | Data loss/duplicate catalog | Source map, ownership/location comparison, reconciliation |
| 4 | Marketplace variant price/stock units conflict | Financial/stock error | V2 only; SKU match and stock-ledger reconciliation |
| 5 | Legacy BigInt money unit unknown | Major financial drift | Per-column unit registry and sum reconciliation |
| 6 | Payment duplicate statuses overstate paid/refund | Ticket/ledger fraud | Evidence-based normalization, quarantine |
| 7 | Claim COMPLETED lacks payout evidence | False settlement | Map to paid only with evidence |
| 8 | Mutable MitraBalance treated as authority | Wrong available balance | Ledger reconstruction; balance projection only |
| 9 | Cross-tenant equality not fully expressible by simple FK | Data leak | Direct mitra on roots, action invariant, integrity audit/composite FK review |
| 10 | Polymorphic legacy favorites/reviews unresolved | Orphans/wrong target | catalog_entities FK and quarantine ambiguity |
| 11 | Common offer bound to multiple domain rows | Wrong order product | offer_type + unique child FK + integrity check/possible trigger ADR |
| 12 | MySQL nullable unique scope duplicates global rows | Duplicate roles/settings/vouchers | Generated scope_key sentinel |
| 13 | Spatie team schema/version mismatch | Broken permission isolation | Verify package exact version before migration |
| 14 | ULID charset/collation inconsistency | FK/index failure | ascii_bin everywhere for ULID |
| 15 | POINT axis order/SRID mistake | Wrong nearest results | Single converter and known-coordinate integration tests |
| 16 | Spatial index not used | Slow discovery | Bounding prefilter + EXPLAIN ANALYZE |
| 17 | KYC/bank/media encryption/key loss | Breach or unreadable data | Versioned keys, backup/rotation, audit, private storage |
| 18 | Webhook duplicate/out-of-order | Double journal/ticket | Unique inbox, row lock, event_key idempotency |
| 19 | Cross-row ledger balance not DB CHECK-able | Unbalanced finance | Posting service transaction + integrity job/test |
| 20 | Soft delete/unique reuse conflict | Duplicate identity/slug/code | Restore/reuse policy and scoped generated keys |
| 21 | Audit/provider JSON leaks PII/secret | Compliance breach | Redacted JSON + encrypted raw payload + retention |
| 22 | Cyclic FK omitted permanently | Orphan data | Explicit final-FK migration gate |
| 23 | V2 tables accidentally launched | Half-built product | Feature/migration release boundary; no V1 seed/routes |
| 24 | Analytics/refund/dispute model pressure | Premature schema debt | Explicit exclusion until product ADR |
| 25 | MySQL behavior tested only on SQLite | False confidence | MySQL 8 integration/concurrency/spatial tests |

## Open schema decisions

- Exact Spatie team/mitra scope schema.
- KYC document taxonomy, retention, and key custody.
- Mitra activation prerequisites.
- Commission/account chart and claim hold timing.
- Whether catalog extension/offer exclusivity uses trigger or audited service invariant.
- Region source/license/version.
- Legacy password/TOTP compatibility.
- Media provider/bucket retention.
- Final voucher reversal boundary.

## Completion checklist

| Check | Design result |
| --- | --- |
| Important V1 tables present | Pass |
| V2/Future separated | Pass |
| No PostgreSQL namespaces | Pass |
| Money never FLOAT | Pass |
| Tenant ownership documented | Pass |
| FK delete behavior documented | Pass |
| Duplicate enums/lifecycles consolidated | Pass |
| Geospatial MySQL strategy defined | Pass |
| Social/AI/analytics/refund/dispute excluded per scope | Pass |
| Laravel migrations created | No, intentionally |

Status: MYSQL_DESIGN_COMPLETE_WITH_OPEN_DECISIONS.

