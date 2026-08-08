# Data Dictionary

## Conventions

- ULID means CHAR(26) ascii_bin primary/foreign key.
- REF means BIGINT UNSIGNED AUTO_INCREMENT primary key.
- TS means TIMESTAMP(6) in UTC.
- Business base: id ULID; created_at TS not null; updated_at TS not null.
- Reference base: id REF; created_at/updated_at TS not null.
- Soft delete adds deleted_at TS nullable and index.
- Currency is CHAR(3), default IDR only where product policy is IDR.
- All strings use utf8mb4 unless ULID/hash/code explicitly ascii.
- Every FK/delete action is summarized again in relationship design.

## Identity

### users — Business base + soft delete

email VARCHAR(191) not null unique; name VARCHAR(150) not null; phone VARCHAR(32) nullable unique; status VARCHAR(32) not null default pending; email_verified_at TS nullable; preferred_locale VARCHAR(10) not null default id; remember_token VARCHAR(100) nullable; last_login_at TS nullable. Sensitive: email, phone, name.

### user_profiles — Business base + soft delete

user_id ULID not null unique FK users/RESTRICT; avatar_media_id ULID nullable FK media_assets/SET NULL; birth_date DATE nullable; gender VARCHAR(32) nullable; notification_preferences JSON nullable and schema-validated; address summary is not stored here. Sensitive: birth date.

### user_credentials — Business base, no soft delete

user_id ULID not null unique FK users/CASCADE; password_hash VARCHAR(255) nullable; mfa_secret_encrypted TEXT nullable; mfa_enabled_at TS nullable; failed_login_count SMALLINT UNSIGNED not null default 0; locked_until TS nullable; password_changed_at TS nullable. No credential value enters audit/log.

### password_reset_tokens

id ULID PK; user_id ULID nullable FK users/CASCADE; email VARCHAR(191) not null; token_hash CHAR(64) ascii not null unique; expires_at TS not null; used_at TS nullable; created_at TS not null. Index email,expires_at.

### sessions

Laravel-compatible: id VARCHAR(255) ascii PK; user_id ULID nullable FK users/CASCADE; ip_address VARCHAR(45) nullable; user_agent TEXT nullable; payload LONGTEXT not null; last_activity INT UNSIGNED not null index. Sensitive payload encrypted/signed by framework assumptions and never queried as domain data.

### mfa_recovery_codes

id ULID PK; user_id ULID not null FK users/CASCADE; code_hash CHAR(64) ascii not null unique; used_at TS nullable; created_at TS not null; index user_id,used_at.

### oauth_identities

id ULID PK; user_id ULID not null FK users/CASCADE; provider VARCHAR(32) not null; provider_subject VARCHAR(191) ascii not null; provider_email VARCHAR(191) nullable; linked_at TS not null; last_used_at TS nullable; unique provider,provider_subject; unique user_id,provider.

## Access control

### roles

id REF; mitra_id ULID nullable FK mitras/RESTRICT; name VARCHAR(100) not null; guard_name VARCHAR(50) not null default web; is_system BOOLEAN not null default false; scope_key CHAR(26) generated stored from COALESCE(mitra_id, platform sentinel); timestamps + deleted_at; unique scope_key,name,guard_name.

### permissions

id REF; name VARCHAR(150) not null; guard_name VARCHAR(50) default web; description VARCHAR(255) nullable; risk_level VARCHAR(32) default normal; timestamps + deleted_at; unique name,guard_name.

### model_has_roles

role_id BIGINT UNSIGNED FK roles/CASCADE; model_type VARCHAR(191) default App\\Models\\User with V1 check; model_id ULID physical FK users/CASCADE; mitra_id ULID nullable FK mitras/CASCADE; scope_key generated from mitra_id; unique role_id,model_type,model_id,scope_key; index model_id,model_type. No surrogate ID.

### model_has_permissions

Same pattern with permission_id FK permissions/CASCADE and unique permission_id,model_type,model_id,scope_key.

### role_has_permissions

role_id BIGINT UNSIGNED FK roles/CASCADE; permission_id BIGINT UNSIGNED FK permissions/CASCADE; created_at TS; primary key role_id,permission_id.

## Mitra and reference data

### mitras — Business base + soft delete

legal_name VARCHAR(191) not null; display_name VARCHAR(191) not null; slug VARCHAR(191) not null unique; registration_number VARCHAR(100) nullable unique; tax_number_encrypted TEXT nullable; owner_user_id ULID not null FK users/RESTRICT; status VARCHAR(32) default draft; description TEXT nullable; contact_email VARCHAR(191) nullable; contact_phone VARCHAR(32) nullable; region_id BIGINT nullable FK regions/SET NULL; address TEXT nullable; logo_media_id ULID nullable FK media_assets/SET NULL; banner_media_id ULID nullable FK media_assets/SET NULL; approved_by ULID nullable FK users/SET NULL; approved_at/suspended_at TS nullable. Index status, owner_user_id, region_id.

### mitra_members — Business base + soft delete

mitra_id ULID FK mitras/RESTRICT; user_id ULID FK users/RESTRICT; status VARCHAR(32) default invited; joined_at TS nullable; invited_by ULID nullable FK users/SET NULL; unique mitra_id,user_id; indexes user_id,status and mitra_id,status. Role assignment lives in Spatie scoped pivots, not JSON.

### mitra_invitations

id ULID; mitra_id ULID FK mitras/RESTRICT; email VARCHAR(191); intended_role_id BIGINT nullable FK roles/SET NULL; token_hash CHAR(64) unique; invited_by ULID FK users/RESTRICT; expires_at TS; accepted_at/revoked_at TS nullable; created_at TS; index mitra_id,email,expires_at.

### service_types — Reference base + soft delete

code VARCHAR(32) ascii unique; name VARCHAR(100); is_transactional BOOLEAN default true; sort_order SMALLINT UNSIGNED default 0. Seed: tourism, accommodation, culinary, event; V2 rental, marketplace, virtual-tour.

### mitra_features

id ULID; mitra_id ULID FK mitras/RESTRICT; service_type_id BIGINT FK service_types/RESTRICT; status VARCHAR(32) default disabled; enabled_at/disabled_at TS nullable; enabled_by ULID nullable FK users/SET NULL; created_at/updated_at; unique mitra_id,service_type_id.

### mitra_feature_requests

id ULID; mitra_id ULID FK mitras/RESTRICT; service_type_id BIGINT FK service_types/RESTRICT; requested_by ULID FK users/RESTRICT; reviewed_by ULID nullable FK users/SET NULL; status VARCHAR(32) default requested; reason/review_note TEXT nullable; reviewed_at TS nullable; created_at/updated_at; index mitra_id,status.

### mitra_bank_accounts — Business base + soft delete

mitra_id ULID FK mitras/RESTRICT; bank_code VARCHAR(32); account_name_encrypted TEXT; account_number_encrypted TEXT; account_fingerprint CHAR(64) ascii; status VARCHAR(32) default pending; is_primary BOOLEAN default false; verified_by ULID nullable FK users/SET NULL; verified_at TS nullable; unique mitra_id,account_fingerprint; index mitra_id,status,is_primary.

### mitra_kyc_documents — Business base, no soft delete

mitra_id ULID FK mitras/RESTRICT; media_asset_id ULID FK media_assets/RESTRICT; document_type VARCHAR(32); version SMALLINT UNSIGNED default 1; document_number_encrypted TEXT nullable; document_fingerprint CHAR(64) nullable; status VARCHAR(32) default submitted; submitted_by ULID FK users/RESTRICT; reviewed_by ULID nullable FK users/SET NULL; reviewed_at TS nullable; expires_on DATE nullable; rejection_reason TEXT nullable; superseded_by_id ULID nullable FK self/SET NULL; unique mitra_id,document_type,version; index mitra_id,status,expires_on.

### gatekeeper_assignments — Business base, no soft delete

mitra_id ULID FK mitras/RESTRICT; member_id ULID FK mitra_members/RESTRICT; event_id ULID nullable FK events/RESTRICT; tourism_destination_id ULID nullable FK tourism_destinations/RESTRICT; scope_type VARCHAR(32); valid_from/valid_until TS nullable; assigned_by ULID FK users/RESTRICT; revoked_by ULID nullable FK users/SET NULL; revoked_at TS nullable. Check target matches scope_type; unique member_id,scope_type,event_id,tourism_destination_id,valid_from.

### regions — Reference base + soft delete

parent_id BIGINT nullable FK regions/RESTRICT; level VARCHAR(32); code VARCHAR(32) ascii unique; name VARCHAR(150); index parent_id,level,name.

### categories and facilities — Reference base + soft delete

service_type_id BIGINT FK service_types/RESTRICT; parent_id BIGINT nullable self-FK/RESTRICT for categories only; name VARCHAR(150); slug VARCHAR(191); is_active BOOLEAN default true; unique service_type_id,slug.

### application_settings

id ULID; mitra_id ULID nullable FK mitras/RESTRICT; key_name VARCHAR(191); value_encrypted LONGTEXT nullable; value_json JSON nullable; value_type VARCHAR(32); is_secret BOOLEAN default false; updated_by ULID nullable FK users/SET NULL; scope_key generated from mitra_id; created_at/updated_at; unique scope_key,key_name; check only one value column appropriate.

### feature_flags

id ULID; key_name VARCHAR(191) unique; description VARCHAR(500); status VARCHAR(32) default disabled; rollout_percentage DECIMAL(5,2) default 0.00; starts_at/ends_at TS nullable; rules JSON nullable; owner_user_id ULID nullable FK users/SET NULL; created_at/updated_at. Feature flag never grants authorization.

## Shared catalog

### catalog_entities — Business base + soft delete

mitra_id ULID FK mitras/RESTRICT; service_type_id BIGINT FK service_types/RESTRICT; category_id BIGINT nullable FK categories/RESTRICT; region_id BIGINT nullable FK regions/SET NULL; name VARCHAR(191); slug VARCHAR(191); description LONGTEXT nullable; address TEXT nullable; status VARCHAR(32) default draft; is_featured BOOLEAN default false; rating_average DECIMAL(3,2) default 0.00; rating_count INT UNSIGNED default 0; published_at/archived_at TS nullable; unique mitra_id,service_type_id,slug; indexes service_type_id,status,published_at and mitra_id,status.

### catalog_locations

catalog_entity_id ULID PK/FK catalog_entities/CASCADE; location POINT SRID 4326 not null; latitude DECIMAL(10,7) not null; longitude DECIMAL(10,7) not null; created_at/updated_at; spatial index location; check latitude -90..90 and longitude -180..180.

### catalog_offers — Business base + soft delete

mitra_id ULID FK mitras/RESTRICT; catalog_entity_id ULID FK catalog_entities/RESTRICT; offer_type VARCHAR(32); sku VARCHAR(100) nullable; name VARCHAR(191); currency CHAR(3) default IDR; price DECIMAL(15,2); status VARCHAR(32) default draft; purchasable_from/until TS nullable; min_quantity INT UNSIGNED default 1; max_quantity INT UNSIGNED nullable; unique mitra_id,sku; index catalog_entity_id,status.

### media_assets — Business base + soft delete

mitra_id ULID nullable FK mitras/RESTRICT; owner_user_id ULID nullable FK users/RESTRICT; is_platform_owned BOOLEAN default false; disk VARCHAR(32); object_key VARCHAR(500); original_name VARCHAR(255) nullable; mime_type VARCHAR(127); size_bytes BIGINT UNSIGNED; checksum_sha256 CHAR(64); visibility VARCHAR(32) default private; purpose VARCHAR(64); status VARCHAR(32) default pending; metadata JSON nullable; uploaded_at TS nullable; unique disk,object_key; check exactly one ownership mode.

### catalog_media

catalog_entity_id ULID FK catalog_entities/CASCADE; media_asset_id ULID FK media_assets/RESTRICT; role VARCHAR(32) default gallery; sort_order INT UNSIGNED default 0; caption VARCHAR(255) nullable; created_at TS; primary key catalog_entity_id,media_asset_id; unique catalog_entity_id,role,sort_order where application enforces role ordering.

### operating_hours

id ULID; catalog_entity_id ULID FK catalog_entities/CASCADE; weekday TINYINT UNSIGNED; sequence TINYINT UNSIGNED default 1; opens_at TIME nullable; closes_at TIME nullable; is_closed BOOLEAN default false; created_at/updated_at; unique catalog_entity_id,weekday,sequence; check weekday 1..7 and closed/open consistency.

### catalog_facilities

catalog_entity_id ULID FK catalog_entities/CASCADE; facility_id BIGINT FK facilities/RESTRICT; notes VARCHAR(255) nullable; created_at TS; primary key catalog_entity_id,facility_id.

### availabilities — Business base

mitra_id ULID FK mitras/RESTRICT; catalog_offer_id ULID FK catalog_offers/RESTRICT; service_date DATE; starts_at/ends_at TIME nullable; capacity INT UNSIGNED; reserved_quantity INT UNSIGNED default 0; price_override DECIMAL(15,2) nullable; status VARCHAR(32) default available; metadata JSON nullable; unique catalog_offer_id,service_date,starts_at; check reserved_quantity <= capacity.

### favorites — Business base

user_id ULID FK users/CASCADE; catalog_entity_id ULID FK catalog_entities/CASCADE; unique user_id,catalog_entity_id; index catalog_entity_id,created_at.

### reviews — Business base + soft delete

user_id ULID FK users/RESTRICT; catalog_entity_id ULID FK catalog_entities/RESTRICT; order_item_id ULID nullable FK order_items/RESTRICT; rating TINYINT UNSIGNED; title VARCHAR(191) nullable; body TEXT nullable; status VARCHAR(32) default pending; moderated_by ULID nullable FK users/SET NULL; moderated_at TS nullable; unique user_id,catalog_entity_id,order_item_id; check rating 1..5; index catalog_entity_id,status,created_at.

### review_replies — Business base + soft delete

review_id ULID FK reviews/RESTRICT; mitra_id ULID FK mitras/RESTRICT; replied_by ULID FK users/RESTRICT; body TEXT; status VARCHAR(32) default published; unique review_id,mitra_id.

## Domain catalog details

| Table | Columns beyond business base | Constraints |
| --- | --- | --- |
| accommodations | catalog_entity_id ULID unique FK catalog_entities/RESTRICT; check_in_time/check_out_time TIME; property_type VARCHAR(32); star_rating TINYINT nullable | one extension per entity |
| accommodation_rooms | accommodation_id ULID FK accommodations/RESTRICT; catalog_offer_id ULID unique FK offers/RESTRICT; name VARCHAR(150); room_type VARCHAR(32); capacity SMALLINT; total_rooms SMALLINT; bed_config JSON nullable | unique accommodation_id,name |
| tourism_destinations | catalog_entity_id ULID unique FK entities/RESTRICT; destination_type VARCHAR(32); visit_duration_minutes INT nullable | one extension per entity |
| tourism_ticket_packages | tourism_destination_id ULID FK destination/RESTRICT; catalog_offer_id ULID unique FK offers/RESTRICT; name VARCHAR(150); quota_per_day INT nullable | unique destination_id,name |
| culinary_venues | catalog_entity_id ULID unique FK entities/RESTRICT; accepts_reservation BOOLEAN; reservation_notes TEXT nullable; phone VARCHAR(32) nullable | one extension per entity |
| culinary_menu_categories | culinary_venue_id ULID FK venue/CASCADE; name VARCHAR(150); sort_order INT default 0 | unique venue_id,name |
| culinary_menu_items | culinary_venue_id ULID FK venue/RESTRICT; menu_category_id ULID nullable FK category/SET NULL; catalog_offer_id ULID nullable unique FK offers/RESTRICT; name VARCHAR(191); description TEXT nullable; price DECIMAL(15,2); status VARCHAR(32) | index venue_id,status |
| events | catalog_entity_id ULID unique FK entities/RESTRICT; starts_at/ends_at TS; venue_name VARCHAR(191) nullable; event_type VARCHAR(32) | check ends_at > starts_at |
| event_ticket_types | event_id ULID FK events/RESTRICT; catalog_offer_id ULID unique FK offers/RESTRICT; name VARCHAR(150); quota INT nullable; sale_starts_at/ends_at TS nullable | unique event_id,name |
| rental_vehicles | catalog_entity_id ULID unique FK entities/RESTRICT; catalog_offer_id ULID nullable unique FK offers/RESTRICT; category VARCHAR(32); plate_number_encrypted TEXT; seats SMALLINT; status VARCHAR(32) | V2 only |
| marketplace_products | catalog_entity_id ULID unique FK entities/RESTRICT; brand VARCHAR(120) nullable; status VARCHAR(32) | V2 only |
| marketplace_variants | marketplace_product_id ULID FK product/RESTRICT; catalog_offer_id ULID unique FK offers/RESTRICT; sku VARCHAR(100); name VARCHAR(160); attributes JSON; weight_grams INT nullable; status VARCHAR(32) | unique product_id,sku; V2 |
| virtual_tours | mitra_id ULID FK mitras/RESTRICT; catalog_entity_id ULID nullable FK entities/RESTRICT; name VARCHAR(191); status VARCHAR(32) | V2; unique entity when non-null |
| panoramas | virtual_tour_id ULID FK tour/CASCADE; media_asset_id ULID FK media/RESTRICT; title VARCHAR(191); sort_order INT | unique tour_id,sort_order; V2 |
| hotspots | panorama_id ULID FK panorama/CASCADE; target_panorama_id ULID nullable FK panorama/SET NULL; action_type VARCHAR(32); yaw/pitch DECIMAL(8,4); label VARCHAR(191) | V2 |

## Commerce

### idempotency_keys

id ULID; user_id ULID nullable FK users/CASCADE; mitra_id ULID nullable FK mitras/CASCADE; scope VARCHAR(64); key_value VARCHAR(191); request_hash CHAR(64); response_status SMALLINT nullable; response_payload JSON nullable; resource_id ULID nullable; expires_at TS; created_at TS; actor_scope_key generated from user/mitra; unique actor_scope_key,scope,key_value.

### orders — Business base, no soft delete

order_number VARCHAR(32) unique; user_id ULID FK users/RESTRICT; mitra_id ULID FK mitras/RESTRICT; currency CHAR(3); subtotal,discount_amount,tax_amount,fee_amount,total_amount DECIMAL(15,2) default 0.00; status VARCHAR(32) default pending_payment; payment_status VARCHAR(32) default pending; placed_at TS; expires_at/paid_at/cancelled_at TS nullable; customer_snapshot JSON nullable; index user_id,status,created_at and mitra_id,status,created_at; check totals non-negative.

### order_items — Business base, no soft delete

order_id ULID FK orders/RESTRICT; mitra_id ULID FK mitras/RESTRICT; catalog_offer_id ULID FK offers/RESTRICT; quantity INT UNSIGNED; item_name VARCHAR(191); sku VARCHAR(100) nullable; unit_price,discount_amount,tax_amount,line_total DECIMAL(15,2); service_date DATE nullable; starts_at/ends_at TS nullable; fulfillment_status VARCHAR(32); snapshot JSON nullable; check quantity > 0; index order_id and catalog_offer_id,service_date.

### payments — Business base, no soft delete

order_id ULID FK orders/RESTRICT; mitra_id ULID FK mitras/RESTRICT; provider VARCHAR(32) default midtrans; provider_reference VARCHAR(191) nullable; method VARCHAR(32) nullable; currency CHAR(3); amount DECIMAL(15,2); status VARCHAR(32) default pending; paid_at/expired_at/failed_at TS nullable; failure_code VARCHAR(100) nullable; provider_snapshot JSON nullable redacted; unique provider,provider_reference; index order_id,status and mitra_id,status,created_at.

### payment_webhook_events

id ULID; provider VARCHAR(32); provider_event_id VARCHAR(191); payment_id ULID nullable FK payments/SET NULL; order_id ULID nullable FK orders/SET NULL; signature_hash CHAR(64) nullable; payload_hash CHAR(64); redacted_payload JSON; raw_payload_encrypted LONGTEXT nullable; status VARCHAR(32) default received; processed_at TS nullable; error_message TEXT nullable; created_at TS; unique provider,provider_event_id; unique provider,payload_hash; index status,created_at.

### tickets — Business base, no soft delete

ticket_code VARCHAR(32) unique; order_item_id ULID FK order_items/RESTRICT; mitra_id ULID FK mitras/RESTRICT; holder_user_id ULID nullable FK users/RESTRICT; qr_token_hash CHAR(64) unique; status VARCHAR(32) default active; valid_from/valid_until TS nullable; used_at TS nullable; index mitra_id,status,valid_until.

### ticket_validation_logs

id ULID; ticket_id ULID FK tickets/RESTRICT; mitra_id ULID FK mitras/RESTRICT; validator_user_id ULID FK users/RESTRICT; assignment_id ULID nullable FK gatekeeper_assignments/SET NULL; result VARCHAR(32); reason_code VARCHAR(64) nullable; scanned_at TS; device_id VARCHAR(100) nullable; ip_address VARCHAR(45) nullable; metadata JSON nullable; created_at TS; index ticket_id,scanned_at and mitra_id,validator_user_id,scanned_at.

### vouchers — Business base + soft delete

mitra_id ULID nullable FK mitras/RESTRICT; code VARCHAR(64); name VARCHAR(191); discount_type VARCHAR(32); discount_value DECIMAL(15,2); max_discount_amount/min_order_amount/budget_amount DECIMAL(15,2) nullable; usage_limit/per_user_limit INT nullable; starts_at/ends_at TS; status VARCHAR(32); scope_rules JSON nullable; scope_key generated from mitra_id; unique scope_key,code.

### voucher_claims

id ULID; voucher_id ULID FK vouchers/RESTRICT; user_id ULID FK users/RESTRICT; status VARCHAR(32) default claimed; claimed_at TS; expires_at TS nullable; created_at/updated_at; unique voucher_id,user_id.

### voucher_usages

id ULID; voucher_id ULID FK vouchers/RESTRICT; voucher_claim_id ULID nullable FK claims/RESTRICT; order_id ULID FK orders/RESTRICT; user_id ULID FK users/RESTRICT; discount_amount DECIMAL(15,2); status VARCHAR(32); applied_at TS; reversed_at TS nullable; created_at; unique voucher_id,order_id.

### ledger_accounts — Business base

mitra_id ULID nullable FK mitras/RESTRICT; user_id ULID nullable FK users/RESTRICT; system_code VARCHAR(64) nullable; account_type VARCHAR(32); currency CHAR(3); status VARCHAR(32); check exactly one owner; unique mitra_id,account_type,currency; unique user_id,account_type,currency; unique system_code,currency.

### ledger_journals

id ULID; journal_number VARCHAR(32) unique; mitra_id ULID nullable FK mitras/RESTRICT; event_key VARCHAR(191) unique; event_type VARCHAR(64); order_id ULID nullable FK orders/RESTRICT; payment_id ULID nullable FK payments/RESTRICT; withdrawal_claim_id ULID nullable FK withdrawal_claims/RESTRICT; reversal_of_id ULID nullable FK self/RESTRICT; description VARCHAR(500); effective_at/posted_at TS; metadata JSON nullable; created_at TS; index mitra_id,effective_at.

### ledger_lines

id ULID; journal_id ULID FK journals/RESTRICT; account_id ULID FK accounts/RESTRICT; sequence SMALLINT UNSIGNED; debit_amount DECIMAL(15,2) default 0.00; credit_amount DECIMAL(15,2) default 0.00; currency CHAR(3); created_at TS; unique journal_id,sequence; check exactly one of debit/credit > 0; index account_id,created_at.

### mitra_balances

mitra_id ULID PK FK mitras/RESTRICT; currency CHAR(3); available_amount,held_amount,total_earned_amount DECIMAL(15,2); last_journal_id ULID nullable FK journals/RESTRICT; rebuilt_at TS; updated_at TS. Projection only and fully reconstructable.

### withdrawal_claims — Business base, no soft delete

mitra_id ULID FK mitras/RESTRICT; bank_account_id ULID FK bank_accounts/RESTRICT; requested_by ULID FK users/RESTRICT; reviewed_by ULID nullable FK users/SET NULL; amount,fee_amount,net_amount DECIMAL(15,2); currency CHAR(3); status VARCHAR(32) default requested; requested_at TS; reviewed_at/paid_at/rejected_at TS nullable; payout_reference VARCHAR(191) nullable unique; evidence_media_id ULID nullable FK media_assets/RESTRICT; rejection_reason TEXT nullable; check net_amount = amount - fee_amount; index mitra_id,status,requested_at.

## Platform

notifications: UUID/ULID compatible primary key; user_id ULID FK users/CASCADE; mitra_id ULID nullable FK mitras/RESTRICT; type VARCHAR(191); data JSON; read_at TS nullable; created_at/updated_at; index user_id,read_at,created_at.

notification_deliveries: id ULID; notification_id FK notifications/CASCADE; channel/provider/reference/status; attempted_at/delivered_at; error_message; created_at; dedupe key unique where provided.

broadcasts: business base + soft delete; mitra_id nullable FK; created_by FK users/RESTRICT; title/body; audience_rules JSON; channel/status; scheduled_at/sent_at; indexes status,scheduled_at and mitra_id,status.

audit_logs: id ULID; mitra_id nullable FK mitras/RESTRICT; actor_user_id nullable FK users/SET NULL; event; auditable_type/id; request_id; IP/user agent; before_values/after_values/metadata JSON redacted; created_at; indexes mitra_id,event,created_at and auditable_type,auditable_id,created_at. Append-only.

moderation_reports: business base; reporter_user_id nullable FK users/SET NULL; mitra_id nullable FK mitras/RESTRICT; catalog_entity_id nullable FK entities/RESTRICT; review_id nullable FK reviews/RESTRICT; reason_code,description,status,assigned_to FK users/SET NULL,resolved_at; check exactly one target; index status,assigned_to,created_at.

moderation_actions: id ULID; report_id FK reports/RESTRICT; actor_user_id FK users/RESTRICT; action_type; notes; metadata JSON; created_at; append-only.

banners: business base + soft delete; mitra_id nullable FK mitras/RESTRICT; media_asset_id FK media/RESTRICT; title; placement; target_url nullable; status; starts_at/ends_at; sort_order; index placement,status,starts_at,ends_at.

outbox_events: id ULID; aggregate_type/id; event_type; payload JSON redacted; correlation_id; occurred_at; available_at; published_at; attempts; last_error; unique event_type,aggregate_id,idempotency key as applicable.

