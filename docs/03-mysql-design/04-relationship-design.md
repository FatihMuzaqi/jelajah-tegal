# Relationship Design

## Delete policy

RESTRICT untuk legal/business/transaction evidence; CASCADE hanya untuk child tanpa arti mandiri; SET NULL untuk actor/attribute opsional. Soft-deleted parent tetap memenuhi FK. Hard purge membutuhkan retention workflow.

## Identity, RBAC, and Mitra

| Child FK | Parent | Cardinality | ON DELETE |
| --- | --- | --- | --- |
| user_profiles/user_credentials.user_id | users | 1:1 | RESTRICT/CASCADE respectively |
| sessions/reset/recovery/oauth.user_id | users | N:1 | CASCADE |
| roles.mitra_id | mitras | N:1 optional | RESTRICT |
| model_has_roles role/model/mitra | roles/users/mitras | N:1 | CASCADE |
| model_has_permissions permission/model/mitra | permissions/users/mitras | N:1 | CASCADE |
| role_has_permissions role/permission | roles/permissions | N:N | CASCADE |
| mitras.owner_user_id | users | N:1 | RESTRICT |
| mitras approver/media/region | users/media/regions | N:1 optional | SET NULL |
| mitra_members.mitra_id/user_id | mitras/users | N:1 | RESTRICT |
| invitations.mitra/inviter | mitras/users | N:1 | RESTRICT |
| invitations.intended_role_id | roles | N:1 optional | SET NULL |
| features/requests/bank/KYC/assignments.mitra_id | mitras | N:1 | RESTRICT |
| requests/KYC/bank reviewer | users | N:1 optional | SET NULL |
| KYC.media_asset_id | media_assets | N:1 | RESTRICT |
| gatekeeper.member_id | mitra_members | N:1 | RESTRICT |
| gatekeeper event/destination target | events/tourism_destinations | N:1 optional | RESTRICT |

## Master and catalog

| Child FK | Parent | ON DELETE | Ownership path |
| --- | --- | --- | --- |
| categories/facilities.service_type_id | service_types | RESTRICT | Platform reference |
| category.parent_id, region.parent_id | Self | RESTRICT | Hierarchy |
| catalog_entities.mitra_id | mitras | RESTRICT | Direct tenant root |
| catalog_entities service/category | reference tables | RESTRICT | Taxonomy cannot be hard-deleted in use |
| catalog_entities.region_id | regions | SET NULL | Listing survives taxonomy correction |
| catalog_locations.entity_id | catalog_entities | CASCADE | No location without listing |
| catalog_offers.entity_id/mitra_id | catalog_entities/mitras | RESTRICT | Direct + verified same tenant |
| catalog_media entity/media | entities/media | CASCADE/RESTRICT | Pivot disappears with entity, file requires detach |
| operating_hours/entity facilities | entity/master | CASCADE/RESTRICT | Pure child/pivot |
| availabilities.offer_id/mitra_id | offers/mitras | RESTRICT | History/reservation evidence |
| favorite.user/entity | users/entities | CASCADE | Preference has no retention value |
| reviews user/entity/order_item | users/entities/order_items | RESTRICT | Moderation/transaction evidence |
| review_replies review/mitra/user | reviews/mitras/users | RESTRICT | Official response evidence |

## Domain extensions

catalog_entities has zero or one extension table corresponding to service_type. Extension table catalog_entity_id is UNIQUE and RESTRICT. Domain child rules:

- accommodation_rooms → accommodations RESTRICT; offer RESTRICT.
- tourism_ticket_packages → tourism_destinations RESTRICT; offer RESTRICT.
- culinary_menu_categories → venue CASCADE; menu_items → venue RESTRICT/category SET NULL/offer RESTRICT.
- event_ticket_types → events RESTRICT; offer RESTRICT.
- V2 rental/product/variant/tour relationships use RESTRICT for sellable/history parent and CASCADE only for panorama/hotspot pure child.
- hotspots.target_panorama_id uses SET NULL so broken navigation can be repaired.

Application/constraint verifies service_type matches extension and offer binding. A catalog entity cannot simultaneously be accommodation and event. Before migrations, decide whether this is enforced by database trigger or service invariant plus integrity audit; no JSON discriminator relation.

## Commerce and finance

| Child FK | Parent | ON DELETE | Reason |
| --- | --- | --- | --- |
| orders.user_id/mitra_id | users/mitras | RESTRICT | Historical buyer/seller |
| order_items.order/mitra/offer | orders/mitras/offers | RESTRICT | Immutable commercial evidence |
| payments.order/mitra | orders/mitras | RESTRICT | Financial evidence |
| webhook.payment/order | payments/orders | SET NULL | Unmatched/provider-first event retained |
| tickets.order_item/mitra/holder | order_items/mitras/users | RESTRICT | Fulfillment evidence |
| validation ticket/mitra/validator | tickets/mitras/users | RESTRICT | Append-only scan evidence |
| validation.assignment | gatekeeper_assignments | SET NULL | Historical result survives assignment archival |
| voucher claim/usage FKs | voucher/user/order | RESTRICT | Discount evidence |
| ledger account owner | mitra/user | RESTRICT | Exactly one owner or system code |
| ledger lines journal/account | journals/accounts | RESTRICT | Immutable double entry |
| journal order/payment/claim/source | explicit tables | RESTRICT | No polymorphic finance source |
| journal.reversal_of_id | journals | RESTRICT | Reversal history |
| mitra_balances.mitra/last_journal | mitras/journals | RESTRICT | Projection lineage |
| withdrawal bank/mitra/requester | bank/mitras/users | RESTRICT | Compliance trail |
| withdrawal reviewer | users | SET NULL | Outcome retained if user purged after retention |

Circular DDL between ledger_journals and withdrawal_claims is resolved by adding withdrawal_claim_id FK after both tables exist. Constraint remains in final schema.

## Platform

- notifications.user_id CASCADE after user retention/anonymization policy; mitra_id RESTRICT.
- notification_deliveries.notification_id CASCADE.
- broadcasts/banners/settings.mitra_id RESTRICT; actor FK RESTRICT or SET NULL as dictionary states.
- moderation report catalog/review target RESTRICT; reporter/assignee SET NULL; action report/actor RESTRICT.
- audit actor SET NULL and mitra RESTRICT; audit itself never cascades.
- outbox does not FK arbitrary aggregate type; it is delivery evidence with snapshot IDs, not ownership.

## Cross-tenant invariants

Database FK alone cannot assert two parents share the same mitra_id. Action-level invariants plus periodic integrity queries ensure offer/entity/order/item/payment/ticket/review reply share tenant. Financial roots duplicate mitra_id intentionally for filter, security, and reconciliation.

