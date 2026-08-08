# Index Strategy

## Rules

- Every FK has a supporting index unless it is the leading part of PK/unique.
- Composite indexes follow tenant/status/time and actual query patterns.
- ULID columns use ascii_bin consistently to avoid collation mismatch/index bloat.
- JSON is not directly a relationship/filter store; promote queried values to typed/generated columns.
- Indexes are validated with EXPLAIN ANALYZE on MySQL 8 and representative volumes.

## Uniqueness

| Area | Unique rule |
| --- | --- |
| Identity | users.email; users.phone nullable; credentials.user_id; provider+subject |
| Membership | mitra_id+user_id; invitation token hash |
| RBAC | role/permission names by scope; full pivot dimensions including generated scope key |
| Reference | service_type.code; region.code; service_type+slug for category/facility |
| Catalog | mitra+service_type+slug; one location/extension per entity; mitra+SKU |
| Shared | favorite user+entity; catalog facility/media pair; operating hour entity+day+sequence |
| Availability | offer+service_date+starts_at |
| Commerce | order_number; provider+reference; provider+event ID/hash; ticket code/token hash |
| Voucher | scope+code; voucher+user; voucher+order |
| Ledger | journal number; event_key; journal+sequence; owner+account_type+currency |

Nullable global/tenant scope cannot rely on standard UNIQUE because MySQL permits multiple NULL. Use stored generated scope_key with an impossible sentinel and unique scope_key,business_key.

## Query indexes

| Query | Index |
| --- | --- |
| Login | users(email), users(phone) |
| Session cleanup | sessions(last_activity); reset(expires_at,used_at) |
| Tenant chooser | mitra_members(user_id,status), mitra_members(mitra_id,status) |
| KYC queue | kyc(status,created_at), kyc(mitra_id,status,expires_on) |
| Public catalog | entities(service_type_id,status,published_at), category_id/status, region_id/status |
| Mitra catalog | entities(mitra_id,status,updated_at) |
| Nearest | SPATIAL location plus status/service prefilter strategy in geospatial doc |
| Availability | availabilities(offer_id,service_date,status) |
| Buyer orders | orders(user_id,status,created_at) |
| Seller orders | orders(mitra_id,status,created_at) |
| Payment worker | webhook(status,created_at); payments(provider,status,created_at) |
| Ticket scan/history | tickets(qr_token_hash); validations(ticket_id,scanned_at); mitra+validator+scanned_at |
| Ledger statement | lines(account_id,created_at); journals(mitra_id,effective_at) |
| Claim queue | claims(status,requested_at); claims(mitra_id,status,requested_at) |
| Notification inbox | notifications(user_id,read_at,created_at) |
| Moderation queue | reports(status,assigned_to,created_at) |
| Scheduled content | banners(placement,status,starts_at,ends_at); broadcasts(status,scheduled_at) |
| Audit search | audit(mitra_id,event,created_at); auditable_type,id,created_at |

## Search

Start with B-tree filters and LIKE prefix for constrained admin searches. Consider FULLTEXT on catalog name/description only after Indonesian relevance benchmark and stopword/tokenization review. Do not combine premature FULLTEXT with geospatial complexity without measured query plans.

## Projection and cache

mitra_balances is point lookup by PK. It does not replace ledger indexes. Dashboard cache may reduce aggregate load, but all critical reconciliation queries remain supported by database indexes.

