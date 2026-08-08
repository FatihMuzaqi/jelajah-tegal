# MySQL Table Inventory

Legend: CORE = V1 required; SUPPORT = V1 supporting/infrastructure; V2-DESIGN = designed but migration prohibited until V2 approval; EXCLUDED = intentionally absent.

## Identity and access

| Table | ID | Scope | Tenant | Soft delete | Audit sensitivity |
| --- | --- | --- | --- | --- | --- |
| users | ULID | CORE | Global | Yes | High |
| user_profiles | ULID | CORE | user-owned | Yes | Medium |
| user_credentials | ULID | CORE | user-owned | No | Critical |
| password_reset_tokens | ULID | CORE | user-owned | No | Critical |
| sessions | VARCHAR framework ID | CORE infra | user-owned | No | High |
| mfa_recovery_codes | ULID | SUPPORT | user-owned | No | Critical |
| oauth_identities | ULID | SUPPORT | user-owned | No | High |
| roles | BIGINT | CORE | global or mitra | Yes | Critical |
| permissions | BIGINT | CORE | global registry | Yes | Critical |
| model_has_roles | Composite unique | CORE | global or mitra | No | Critical |
| model_has_permissions | Composite unique | CORE | global or mitra | No | Critical |
| role_has_permissions | Composite PK | CORE | role-owned | No | Critical |

## Mitra and master

| Table | ID | Scope | Ownership | Soft delete |
| --- | --- | --- | --- | --- |
| mitras | ULID | CORE | tenant root | Yes |
| mitra_members | ULID | CORE | mitra_id | Yes |
| mitra_invitations | ULID | CORE | mitra_id | No |
| service_types | BIGINT | CORE | platform reference | Yes |
| mitra_features | ULID | CORE | mitra_id | No |
| mitra_feature_requests | ULID | SUPPORT | mitra_id | No |
| mitra_bank_accounts | ULID | CORE | mitra_id | Yes |
| mitra_kyc_documents | ULID | CORE | mitra_id | No |
| gatekeeper_assignments | ULID | CORE | mitra_id | No |
| regions | BIGINT | CORE | platform reference | Yes |
| categories | BIGINT | CORE | service type | Yes |
| facilities | BIGINT | CORE | service type | Yes |
| application_settings | ULID | CORE | global or mitra | No |
| feature_flags | ULID | CORE | global rollout | No |

## Shared catalog

| Table | ID | Scope | Ownership | Notes |
| --- | --- | --- | --- | --- |
| catalog_entities | ULID | CORE | mitra_id | Common aggregate root |
| catalog_locations | ULID | CORE | via catalog entity | POINT SRID 4326 + lat/lng |
| catalog_offers | ULID | CORE | mitra_id + entity | Purchasable abstraction |
| media_assets | ULID | CORE | user or mitra | Provider-neutral storage registry |
| catalog_media | Composite unique | CORE | via catalog entity | Ordered media pivot |
| operating_hours | ULID | CORE | via catalog entity | Normal weekly schedule |
| catalog_facilities | Composite PK | CORE | via catalog entity | Shared facility pivot |
| availabilities | ULID | CORE | offer/mitra | Capacity and price override by slot |
| favorites | ULID | SUPPORT | user + catalog entity | Unique user/entity |
| reviews | ULID | SUPPORT | user + catalog entity | Moderated/verified |
| review_replies | ULID | SUPPORT | review + mitra | One official reply initially |

## Domain catalog

| Table | Scope | Parent/ownership | Notes |
| --- | --- | --- | --- |
| accommodations | CORE | catalog_entity_id | Property details |
| accommodation_rooms | CORE | accommodation + offer | Room sellable unit |
| tourism_destinations | CORE | catalog_entity_id | Destination details |
| tourism_ticket_packages | CORE | destination + offer | Package sellable unit |
| culinary_venues | CORE | catalog_entity_id | Canonical Resto/Culinary merge |
| culinary_menu_categories | CORE | venue | Ordered grouping |
| culinary_menu_items | CORE | venue/category + offer optional | Item/reservation sellable |
| events | CORE | catalog_entity_id | Event schedule |
| event_ticket_types | CORE | event + offer | Ticket tier/quota |
| rental_vehicles | V2-DESIGN | catalog_entity_id + offer | No V1 migration |
| marketplace_products | V2-DESIGN | catalog_entity_id | No V1 migration |
| marketplace_variants | V2-DESIGN | product + offer | Consolidated variants |
| virtual_tours | V2-DESIGN | catalog_entity/mitra | No V1 migration |
| panoramas | V2-DESIGN | virtual_tour + media | Panorama scenes |
| hotspots | V2-DESIGN | panorama/target panorama | Navigation/action point |

## Commerce and finance

| Table | ID | Scope | Ownership | Mutability |
| --- | --- | --- | --- | --- |
| idempotency_keys | ULID | CORE | actor/mitra | Expiring result registry |
| orders | ULID | CORE | user + mitra | State-controlled, no delete |
| order_items | ULID | CORE | order/mitra | Snapshot immutable after placement |
| payments | ULID | CORE | order/mitra | State-controlled, no delete |
| payment_webhook_events | ULID | CORE | provider/order | Append-only inbox |
| tickets | ULID | CORE | order item/mitra | State-controlled |
| ticket_validation_logs | ULID | CORE | ticket/mitra | Append-only |
| vouchers | ULID | SUPPORT | global or mitra | Soft delete before/after lifecycle |
| voucher_claims | ULID | SUPPORT | user/voucher | State-controlled |
| voucher_usages | ULID | SUPPORT | order/voucher | Append-only/reversal status |
| ledger_accounts | ULID | CORE | system/mitra/user | Restricted |
| ledger_journals | ULID | CORE | platform/mitra | Immutable after posted |
| ledger_lines | ULID | CORE | journal/account | Append-only |
| mitra_balances | mitra_id PK | SUPPORT projection | mitra | Rebuildable cache only |
| withdrawal_claims | ULID | CORE | mitra | State-controlled, no delete |

## Platform and infrastructure

| Table | Scope | Notes |
| --- | --- | --- |
| notifications | SUPPORT | Laravel database notification shape plus tenant context |
| notification_deliveries | SUPPORT | Delivery attempt/provider result |
| broadcasts | SUPPORT | Operational campaigns only |
| audit_logs | CORE | Append-only redacted evidence |
| moderation_reports | CORE | Explicit catalog_entity_id or review_id target |
| moderation_actions | CORE | Append-only action history |
| banners | SUPPORT | CMS banner only |
| outbox_events | CORE infrastructure | Critical after-commit delivery |
| jobs, job_batches, failed_jobs | CORE infrastructure | Laravel queue tables |
| cache, cache_locks | Optional infrastructure | If database cache/locks selected |

## Explicitly absent

No tables are designed for social feed/follow/trip, AI Planner, analytics events/snapshots, refunds, disputes, invoice, booking references, shipment/tracking, generic CMS, outlets, or cart in V1. Marketplace-specific cart/shipping awaits V2 redesign.

