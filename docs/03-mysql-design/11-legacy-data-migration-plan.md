# Legacy Data Migration Plan

## Principles

Migration is ETL with evidence, not INSERT SELECT of 157 tables. Source remains read-only. Every migrated target row maps to source_model/source_id in temporary legacy_id_map and produces counts/checksums/exception report.

## Phases

1. Profile source models, counts, nulls, duplicates, orphan FKs, enum distribution, money totals, coordinate quality, and active-route evidence.
2. Freeze mapping specification and canonical precedence per duplicate family.
3. Generate deterministic ULIDs or persistent source-to-target map.
4. Load reference/identity/tenant; reconcile before catalog.
5. Load canonical catalog/shared children; quarantine ambiguous rows.
6. Load order→item→payment→ticket; reconcile totals/status/evidence.
7. Reconstruct ledger/opening entries and claim state with finance approval.
8. Load engagement/platform evidence under retention/redaction rules.
9. Execute repeated dry runs, delta strategy, cutover, and post-cutover verification.

## Duplicate family rules

| Family | Canonical target rule |
| --- | --- |
| Mitra/MitraProfile | Match by verified legacy IDs/membership, registration number, normalized name only with manual review; prefer row used by active routes and richer audited state |
| MitraUser/StaffAccount | Merge by target mitra+user; derive role mapping into scoped RBAC; JSON permissions mapped only to registered permissions |
| Resto/Culinary | Map both IDs to one culinary venue only when ownership/name/location evidence agrees; newer Culinary status/slug preferred, active Resto rows not discarded silently |
| Marketplace variants | Match product+SKU; newer variant fields preferred; price/unit normalized; conflicting stock reconciled from stock ledger, not max value |
| Social overlaps | Not migrated; archive/export decision separate |
| Domain favorite/review duplicates | Resolve user and catalog entity; dedupe by source timestamps/order evidence; ambiguous polymorphic rows quarantine |

## Enum/status transformation

Use versioned lookup tables/scripts. Payment and claim rules follow enum design. Unknown values produce migration exception. paid/refunded/completed status requires provider/payout evidence; label alone is insufficient.

## Money

For each source column record unit assumption. Compare order line/header/payment/ledger/claim totals per currency and status before/after. Rounding difference gets explicit report; never silently posts adjustment. Legacy MitraBalance is comparison input, not authority; approved opening journal derives from reconciled transactions and signed exception list.

## Credentials and tokens

- Do not migrate sessions, refresh/reset/activation/recovery token values.
- Password hash migrates only if Laravel verifier compatibility and rehash-on-login are tested; otherwise forced reset/invitation.
- TOTP secret migrates only if decryptable, ownership verified, and key transfer approved; otherwise secure re-enrollment.
- Google identity maps provider subject/email with collision review.

## Media and KYC

Verify owner, URL/object existence, checksum/size/MIME, visibility, and legal retention. Copy to target object key/provider through restartable task. Private KYC never becomes public. Orphan/unreachable/external-license-unclear assets quarantine.

## Geospatial

Compare legacy Float and PostGIS values; transform WGS84, detect swapped axes/zero/outliers, write POINT and scalar columns together. Known coordinate fixtures validate target distances.

## Excluded data

Analytics, AI planner, social, refund/dispute, invoice, booking reference, generic CMS, cart/shipment, and mock/demo rows are not loaded into production target tables. If legal/product requires retention, preserve immutable export outside operational schema.

## Reconciliation gates

| Gate | Required result |
| --- | --- |
| Identity | Explainable user/Mitra/member counts and duplicate decisions |
| Tenant | Zero cross-tenant FK/integrity mismatch |
| Catalog | Every published entity has valid tenant/service/status; shared children resolved |
| Money | Approved order/payment/claim totals per currency/status; zero unexplained drift |
| Ledger | Every posted journal balances; opening balances approved |
| Ticket | Every ticket maps paid eligible item; used evidence exception documented |
| Media | Object/checksum/visibility reconciliation |
| Security | Legacy sessions/tokens revoked; no secret in logs/files |

## Cutover

At least two dry runs. Choose write freeze or CDC/delta plan before production. Rollback means traffic reversal plus delta reconciliation—not destructive target reset. Midtrans webhook endpoint switch, session invalidation, email/OAuth redirect, workers, scheduler, storage, and monitoring require coordinated runbook.

