# Prisma to MySQL Map

Status: DESIGN BASELINE. Sumber: scope Tahap 1, arsitektur Tahap 2, dan schema Prisma legacy berisi 157 model/79 enum. Pemetaan tidak mengotorisasi migration.

## Global decisions

| Area | Target decision |
| --- | --- |
| Engine/charset | MySQL 8, InnoDB, utf8mb4, utf8mb4_0900_ai_ci |
| Business IDs | ULID CHAR(26) CHARACTER SET ascii COLLATE ascii_bin |
| Reference IDs | BIGINT UNSIGNED AUTO_INCREMENT |
| Money | DECIMAL(15,2) + CHAR(3) currency, mengikuti ADR-017; tidak FLOAT |
| Time | TIMESTAMP(6) UTC; date-only tetap DATE |
| Enum | VARCHAR + PHP backed enum, bukan MySQL ENUM |
| Tenant | mitra_id physical FK pada aggregate tenant dan financial roots |
| PostgreSQL schema | Dihapus; nama tabel unik pada satu namespace MySQL |
| JSONB | JSON hanya flexible snapshot/provider/config; tidak untuk relation atau money |

## Identity and access

| Prisma legacy | MySQL target | Treatment | Scope |
| --- | --- | --- | --- |
| AuthProfile, CustomerAccount | users + user_profiles | Merge identity dan profile consumer | V1 |
| AuthCredential | user_credentials | Retain terpisah untuk password/MFA secret/lock state | V1 |
| AuthRefreshToken | sessions | Tidak migrasikan token; gunakan Laravel session table | V1 runtime |
| AuthPasswordReset | password_reset_tokens | Consolidate Laravel-compatible hashed token | V1 |
| AuthEmailVerification | Signed verification + users.email_verified_at | Tidak perlu token table permanen | V1 |
| AuthActivationToken | mitra_invitations | Invitation-specific one-use hash | V1 |
| AuthRecoveryCode | mfa_recovery_codes | Retain hashed one-use codes | V1 |
| Google subject pada AuthProfile | oauth_identities | Normalize provider identity | V1 supporting |
| AdminUser | users + global roles | Tidak ada identity admin kedua | V1 |
| AdminPermission/AdminRolePermission | permissions/role_has_permissions | Map ke Spatie dynamic RBAC | V1 |
| Fixed AdminRole/MitraUserRole | roles + model_has_roles | Legacy values hanya migration mapping | Deprecated source |

## Mitra consolidation

| Prisma legacy | Target | Treatment |
| --- | --- | --- |
| Mitra + MitraProfile | mitras | Satu tenant/legal/business aggregate; pilih canonical row per source map |
| MitraUser + MitraStaffAccount | mitra_members | Satu membership; role/permission tidak disimpan JSON |
| MitraFeature + feature field variants | mitra_features | Feature references service_types |
| MitraFeatureRequest + MitraFeatureActivationRequest | mitra_feature_requests | Satu request lifecycle |
| MitraBankAccount | mitra_bank_accounts | Encrypt account fields; fingerprint for uniqueness |
| MitraKYCDocument + MitraDocument | mitra_kyc_documents | Satu versioned document history; media FK |
| GatekeeperAssignment | gatekeeper_assignments | Replace subject string with member/user FK and explicit target FK |
| MitraOutlet | Excluded V1 | Outlet unresolved; no table until ADR closes |

## Catalog consolidation

All catalog aggregate tables extend catalog_entities one-to-one. Shared media/location/hours/favorite/review/facility use FK to catalog_entities, eliminating duplicated domain tables and unsafe resource_type/resource_id pairs.

| Prisma legacy family | Target | Treatment | Scope |
| --- | --- | --- | --- |
| Property and child | accommodations, accommodation_rooms | Rename canonical domain; retain active room flow | V1 |
| TourismDestination/TourismTicketPackage | tourism_destinations, tourism_ticket_packages | Retain active package/quota | V1 |
| RestoVenue + CulinaryVenue | culinary_venues | Merge; Culinary generation is target terminology; reconcile row conflicts |
| RestoMenuCategory + CulinaryMenuCategory | culinary_menu_categories | Merge by canonical venue/source map |
| RestoMenuItem + CulinaryMenuItem | culinary_menu_items | Merge; decimal price and common offer |
| Event/EventTicketType | events, event_ticket_types | Retain active ticket flow | V1 |
| RentalVehicle and newer Rental* | rental_vehicles | Merge active/newest fields; designed only | V2 |
| MarketplaceProduct | marketplace_products | Retain canonical product | V2 |
| MarketplaceVariant + MarketplaceProductVariant | marketplace_variants | Merge into newer variant semantics; stock history not mutable authority | V2 |
| VirtualTour + panorama/hotspot + relation variants | virtual_tours, panoramas, hotspots | Merge relation generations; designed only | V2 |
| Domain Media tables | media_assets + catalog_media | Consolidate attachment with physical FKs |
| Domain Facility tables | facilities + catalog_facilities | Consolidate shared facility relation |
| Domain Favorite tables + AuthProfileFavorite/SavedResource | favorites | Single FK to catalog_entities; discard ambiguous/unresolvable rows |
| Domain Review tables + AuthProfileReview | reviews + review_replies | Single review model; reply separated; verified order item optional |
| Operating hour JSON | operating_hours | Normalize weekly schedule; exceptions remain future explicit table |
| Availability tables/slots/calendars | availabilities | Normalize against catalog_offers/date/time; domain-specific metadata limited JSON |

catalog_offers is added as a stable purchasable abstraction. Rooms, tourism packages, menu items/reservation offers, event ticket types, rental vehicles/rates, and marketplace variants bind one-to-one to an offer. order_items references catalog_offers and stores immutable snapshots.

## Transaction consolidation

| Prisma legacy | Target | Treatment |
| --- | --- | --- |
| Order | orders | Retain, one Mitra per order |
| OrderItem | order_items | Retain snapshot + catalog_offer FK |
| CheckoutSession/IdempotencyRecord | idempotency_keys | Checkout draft UI is session/form state; durable retry key retained |
| Payment | payments | Provider-neutral payment attempt, decimal amount |
| PaymentWebhookEvent | payment_webhook_events | Retain inbox/idempotency evidence |
| PaymentReconciliation | Excluded V1 table | Scheduler/report first; add dedicated table in V2 only if workflow approved |
| Ticket | tickets | Token plaintext replaced with qr_token_hash |
| TicketValidationLog | ticket_validation_logs | Retain append-only results |
| Voucher/VoucherClaim/VoucherUsage | vouchers/voucher_claims/voucher_usages | Retain limited V1 and consolidate lifecycle |
| LedgerJournal + LedgerLine | ledger_journals + ledger_lines + ledger_accounts | Retain as authoritative double entry |
| LedgerEntry | Merge into ledger_lines | Remove competing journal representation |
| MitraBalance | mitra_balances | Rebuildable projection/cache, never authority |
| WithdrawalClaim | withdrawal_claims | Consolidated lifecycle; manual payout evidence V1 |
| Invoice | No table | Unused model; order_number and order snapshots satisfy V1 |
| BookingReference | No table | Unused model; order_number/ticket_code/service snapshot satisfy V1 |
| RefundRequest/DisputeCase | No table | NEEDS_REDESIGN; no active complete workflow |
| ShippingShipment/TraceResiLookup/Cart | No V1 tables | Marketplace V2 ADR required |

## Status normalization

- Payment FAILURE and FAILED → failed.
- Payment EXPIRE and EXPIRED → expired.
- Payment REFUND and REFUNDED → refunded.
- CAPTURED/SETTLEMENT map to paid only when legacy provider evidence and amount verify; otherwise quarantine.
- Claim PENDING/REQUESTED/SUBMITTED → requested; COMPLETED → paid only with payout evidence, otherwise approved/exception review.
- Catalog domain-specific draft/published/archive/takedown enums → one CatalogStatus.

## Platform

| Legacy | Target | Treatment |
| --- | --- | --- |
| Notification | notifications | Retain database inbox |
| NotificationBroadcast/BroadcastCampaign | broadcasts | Merge; limited operational V1 |
| AuditLog | audit_logs | Append-only, redacted |
| ModerationReport/ContentReport | moderation_reports | Merge target to catalog/review explicit FK |
| ModerationAction/ContentFlag | moderation_actions optional companion | Retain action history; automated flags deferred unless used |
| CMSBanner/HomeBanner | banners | Merge only banner capability |
| PlatformConfig/PlatformSetting | application_settings | Merge typed/encrypted scoped settings |
| feature enums/config | feature_flags + mitra_features | Separate rollout from entitlement |
| AnalyticsEvent/MetricSnapshot | No V1/V2 migration table yet | Analytics definition immature; exclude until ADR |

## Social and AI

FeedPost, comment, like, FollowRelation/SocialFollow, SocialTrip*, AiPlanner* and CatalogSyncOutbox are not included. Social and AI are FUTURE; in-memory/mock or model-only rows do not justify target tables.

