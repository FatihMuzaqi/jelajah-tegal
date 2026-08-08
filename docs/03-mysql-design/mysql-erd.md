# MySQL ERD

ERD dipisah per bounded context. ULID ditulis string; reference BIGINT ditulis bigint. Detail type/default/index/delete rule otoritatif ada pada data dictionary dan relationship design.

## Identity, RBAC, and Mitra

```mermaid
erDiagram
    USERS {
        string id PK
        string email UK
        string status
        datetime deleted_at
    }
    USER_PROFILES {
        string id PK
        string user_id FK,UK
        string avatar_media_id FK
    }
    USER_CREDENTIALS {
        string id PK
        string user_id FK,UK
        string password_hash
        text mfa_secret_encrypted
    }
    SESSIONS {
        string id PK
        string user_id FK
        int last_activity
    }
    OAUTH_IDENTITIES {
        string id PK
        string user_id FK
        string provider
        string provider_subject UK
    }
    MITRAS {
        string id PK
        string owner_user_id FK
        bigint region_id FK
        string slug UK
        string status
    }
    MITRA_MEMBERS {
        string id PK
        string mitra_id FK
        string user_id FK
        string status
    }
    MITRA_FEATURES {
        string id PK
        string mitra_id FK
        bigint service_type_id FK
        string status
    }
    MITRA_BANK_ACCOUNTS {
        string id PK
        string mitra_id FK
        string account_fingerprint UK
        string status
    }
    MITRA_KYC_DOCUMENTS {
        string id PK
        string mitra_id FK
        string media_asset_id FK
        string status
    }
    ROLES {
        bigint id PK
        string mitra_id FK
        string name
    }
    PERMISSIONS {
        bigint id PK
        string name UK
    }
    MODEL_HAS_ROLES {
        bigint role_id FK
        string model_id FK
        string mitra_id FK
    }
    MODEL_HAS_PERMISSIONS {
        bigint permission_id FK
        string model_id FK
        string mitra_id FK
    }
    ROLE_HAS_PERMISSIONS {
        bigint role_id PK,FK
        bigint permission_id PK,FK
    }
    SERVICE_TYPES {
        bigint id PK
        string code UK
    }
    REGIONS {
        bigint id PK
        bigint parent_id FK
        string code UK
    }
    MEDIA_ASSETS {
        string id PK
        string mitra_id FK
        string owner_user_id FK
        string object_key UK
    }

    USERS ||--o| USER_PROFILES : has
    USERS ||--|| USER_CREDENTIALS : authenticates
    USERS ||--o{ SESSIONS : opens
    USERS ||--o{ OAUTH_IDENTITIES : links
    USERS ||--o{ MITRAS : owns
    USERS ||--o{ MITRA_MEMBERS : joins
    MITRAS ||--o{ MITRA_MEMBERS : contains
    MITRAS ||--o{ MITRA_FEATURES : enables
    SERVICE_TYPES ||--o{ MITRA_FEATURES : identifies
    MITRAS ||--o{ MITRA_BANK_ACCOUNTS : pays_to
    MITRAS ||--o{ MITRA_KYC_DOCUMENTS : submits
    MITRAS ||--o{ ROLES : scopes
    ROLES ||--o{ MODEL_HAS_ROLES : assigned
    USERS ||--o{ MODEL_HAS_ROLES : receives
    PERMISSIONS ||--o{ MODEL_HAS_PERMISSIONS : grants
    USERS ||--o{ MODEL_HAS_PERMISSIONS : receives
    ROLES ||--o{ ROLE_HAS_PERMISSIONS : contains
    PERMISSIONS ||--o{ ROLE_HAS_PERMISSIONS : belongs
    MITRAS ||--o{ MEDIA_ASSETS : owns
    USERS ||--o{ MEDIA_ASSETS : owns
    MEDIA_ASSETS ||--o{ MITRA_KYC_DOCUMENTS : stores
    REGIONS ||--o{ REGIONS : contains
    REGIONS o|--o{ MITRAS : locates
```

## Shared catalog and V1 domains

```mermaid
erDiagram
    MITRAS {
        string id PK
    }
    SERVICE_TYPES {
        bigint id PK
    }
    CATEGORIES {
        bigint id PK
        bigint service_type_id FK
    }
    FACILITIES {
        bigint id PK
        bigint service_type_id FK
    }
    CATALOG_ENTITIES {
        string id PK
        string mitra_id FK
        bigint service_type_id FK
        bigint category_id FK
        string name
        string status
    }
    CATALOG_LOCATIONS {
        string catalog_entity_id PK,FK
        point location
        decimal latitude
        decimal longitude
    }
    CATALOG_OFFERS {
        string id PK
        string mitra_id FK
        string catalog_entity_id FK
        decimal price
        string status
    }
    MEDIA_ASSETS {
        string id PK
    }
    CATALOG_MEDIA {
        string catalog_entity_id PK,FK
        string media_asset_id PK,FK
    }
    CATALOG_FACILITIES {
        string catalog_entity_id PK,FK
        bigint facility_id PK,FK
    }
    OPERATING_HOURS {
        string id PK
        string catalog_entity_id FK
    }
    AVAILABILITIES {
        string id PK
        string mitra_id FK
        string catalog_offer_id FK
        date service_date
        int capacity
        int reserved_quantity
    }
    ACCOMMODATIONS {
        string id PK
        string catalog_entity_id FK,UK
    }
    ACCOMMODATION_ROOMS {
        string id PK
        string accommodation_id FK
        string catalog_offer_id FK,UK
    }
    TOURISM_DESTINATIONS {
        string id PK
        string catalog_entity_id FK,UK
    }
    TOURISM_TICKET_PACKAGES {
        string id PK
        string tourism_destination_id FK
        string catalog_offer_id FK,UK
    }
    CULINARY_VENUES {
        string id PK
        string catalog_entity_id FK,UK
    }
    CULINARY_MENU_CATEGORIES {
        string id PK
        string culinary_venue_id FK
    }
    CULINARY_MENU_ITEMS {
        string id PK
        string culinary_venue_id FK
        string catalog_offer_id FK,UK
    }
    EVENTS {
        string id PK
        string catalog_entity_id FK,UK
        datetime starts_at
        datetime ends_at
    }
    EVENT_TICKET_TYPES {
        string id PK
        string event_id FK
        string catalog_offer_id FK,UK
    }

    MITRAS ||--o{ CATALOG_ENTITIES : owns
    SERVICE_TYPES ||--o{ CATALOG_ENTITIES : types
    SERVICE_TYPES ||--o{ CATEGORIES : defines
    SERVICE_TYPES ||--o{ FACILITIES : defines
    CATEGORIES o|--o{ CATALOG_ENTITIES : classifies
    CATALOG_ENTITIES ||--o| CATALOG_LOCATIONS : locates
    CATALOG_ENTITIES ||--o{ CATALOG_OFFERS : sells
    CATALOG_ENTITIES ||--o{ CATALOG_MEDIA : displays
    MEDIA_ASSETS ||--o{ CATALOG_MEDIA : attaches
    CATALOG_ENTITIES ||--o{ CATALOG_FACILITIES : has
    FACILITIES ||--o{ CATALOG_FACILITIES : classifies
    CATALOG_ENTITIES ||--o{ OPERATING_HOURS : opens
    CATALOG_OFFERS ||--o{ AVAILABILITIES : schedules
    CATALOG_ENTITIES ||--o| ACCOMMODATIONS : extends
    ACCOMMODATIONS ||--o{ ACCOMMODATION_ROOMS : contains
    CATALOG_OFFERS ||--o| ACCOMMODATION_ROOMS : prices
    CATALOG_ENTITIES ||--o| TOURISM_DESTINATIONS : extends
    TOURISM_DESTINATIONS ||--o{ TOURISM_TICKET_PACKAGES : contains
    CATALOG_OFFERS ||--o| TOURISM_TICKET_PACKAGES : prices
    CATALOG_ENTITIES ||--o| CULINARY_VENUES : extends
    CULINARY_VENUES ||--o{ CULINARY_MENU_CATEGORIES : groups
    CULINARY_VENUES ||--o{ CULINARY_MENU_ITEMS : serves
    CATALOG_OFFERS o|--o| CULINARY_MENU_ITEMS : prices
    CATALOG_ENTITIES ||--o| EVENTS : extends
    EVENTS ||--o{ EVENT_TICKET_TYPES : tiers
    CATALOG_OFFERS ||--o| EVENT_TICKET_TYPES : prices
```

## Commerce, ticket, and finance

```mermaid
erDiagram
    USERS {
        string id PK
    }
    MITRAS {
        string id PK
    }
    CATALOG_ENTITIES {
        string id PK
    }
    CATALOG_OFFERS {
        string id PK
        string mitra_id FK
    }
    ORDERS {
        string id PK
        string user_id FK
        string mitra_id FK
        string order_number UK
        decimal total_amount
        string status
    }
    ORDER_ITEMS {
        string id PK
        string order_id FK
        string mitra_id FK
        string catalog_offer_id FK
        decimal line_total
    }
    PAYMENTS {
        string id PK
        string order_id FK
        string mitra_id FK
        decimal amount
        string status
    }
    PAYMENT_WEBHOOK_EVENTS {
        string id PK
        string payment_id FK
        string order_id FK
        string provider_event_id UK
    }
    TICKETS {
        string id PK
        string order_item_id FK
        string mitra_id FK
        string qr_token_hash UK
        string status
    }
    TICKET_VALIDATION_LOGS {
        string id PK
        string ticket_id FK
        string mitra_id FK
        string validator_user_id FK
        string assignment_id FK
        string result
    }
    GATEKEEPER_ASSIGNMENTS {
        string id PK
        string mitra_id FK
        string member_id FK
    }
    FAVORITES {
        string id PK
        string user_id FK
        string catalog_entity_id FK
    }
    REVIEWS {
        string id PK
        string user_id FK
        string catalog_entity_id FK
        string order_item_id FK
    }
    LEDGER_ACCOUNTS {
        string id PK
        string mitra_id FK
        string user_id FK
        string account_type
    }
    LEDGER_JOURNALS {
        string id PK
        string order_id FK
        string payment_id FK
        string withdrawal_claim_id FK
        string reversal_of_id FK
        string event_key UK
    }
    LEDGER_LINES {
        string id PK
        string journal_id FK
        string account_id FK
        decimal debit_amount
        decimal credit_amount
    }
    WITHDRAWAL_CLAIMS {
        string id PK
        string mitra_id FK
        string bank_account_id FK
        decimal amount
        string status
    }
    MITRA_BALANCES {
        string mitra_id PK,FK
        string last_journal_id FK
        decimal available_amount
    }

    USERS ||--o{ ORDERS : places
    MITRAS ||--o{ ORDERS : fulfills
    ORDERS ||--|{ ORDER_ITEMS : contains
    CATALOG_OFFERS ||--o{ ORDER_ITEMS : snapshots
    ORDERS ||--o{ PAYMENTS : attempts
    PAYMENTS o|--o{ PAYMENT_WEBHOOK_EVENTS : receives
    ORDER_ITEMS ||--o{ TICKETS : issues
    TICKETS ||--o{ TICKET_VALIDATION_LOGS : records
    USERS ||--o{ TICKET_VALIDATION_LOGS : validates
    GATEKEEPER_ASSIGNMENTS o|--o{ TICKET_VALIDATION_LOGS : authorizes
    USERS ||--o{ FAVORITES : saves
    CATALOG_ENTITIES ||--o{ FAVORITES : saved
    USERS ||--o{ REVIEWS : writes
    CATALOG_ENTITIES ||--o{ REVIEWS : reviewed
    ORDER_ITEMS o|--o{ REVIEWS : verifies
    MITRAS ||--o{ LEDGER_ACCOUNTS : owns
    USERS ||--o{ LEDGER_ACCOUNTS : owns
    LEDGER_JOURNALS ||--|{ LEDGER_LINES : contains
    LEDGER_ACCOUNTS ||--o{ LEDGER_LINES : posts
    ORDERS o|--o{ LEDGER_JOURNALS : sources
    PAYMENTS o|--o{ LEDGER_JOURNALS : sources
    MITRAS ||--o{ WITHDRAWAL_CLAIMS : requests
    WITHDRAWAL_CLAIMS o|--o{ LEDGER_JOURNALS : sources
    LEDGER_JOURNALS o|--o| LEDGER_JOURNALS : reverses
    MITRAS ||--o| MITRA_BALANCES : projects
    LEDGER_JOURNALS o|--o{ MITRA_BALANCES : advances
```

## Platform

```mermaid
erDiagram
    USERS ||--o{ NOTIFICATIONS : receives
    MITRAS o|--o{ NOTIFICATIONS : scopes
    NOTIFICATIONS ||--o{ NOTIFICATION_DELIVERIES : delivers
    USERS ||--o{ BROADCASTS : creates
    MITRAS o|--o{ BROADCASTS : scopes
    USERS o|--o{ AUDIT_LOGS : acts
    MITRAS o|--o{ AUDIT_LOGS : scopes
    USERS o|--o{ MODERATION_REPORTS : reports
    CATALOG_ENTITIES o|--o{ MODERATION_REPORTS : targets
    REVIEWS o|--o{ MODERATION_REPORTS : targets
    MODERATION_REPORTS ||--o{ MODERATION_ACTIONS : records
    USERS ||--o{ MODERATION_ACTIONS : acts
    MEDIA_ASSETS ||--o{ BANNERS : displays
    MITRAS o|--o{ BANNERS : scopes
```

## V2 domain extension

```mermaid
erDiagram
    CATALOG_ENTITIES ||--o| RENTAL_VEHICLES : extends
    CATALOG_OFFERS o|--o| RENTAL_VEHICLES : prices
    CATALOG_ENTITIES ||--o| MARKETPLACE_PRODUCTS : extends
    MARKETPLACE_PRODUCTS ||--o{ MARKETPLACE_VARIANTS : contains
    CATALOG_OFFERS ||--o| MARKETPLACE_VARIANTS : prices
    MITRAS ||--o{ VIRTUAL_TOURS : owns
    CATALOG_ENTITIES o|--o{ VIRTUAL_TOURS : enriches
    VIRTUAL_TOURS ||--o{ PANORAMAS : contains
    MEDIA_ASSETS ||--o{ PANORAMAS : renders
    PANORAMAS ||--o{ HOTSPOTS : contains
    PANORAMAS o|--o{ HOTSPOTS : targets
```

V2 entities are design-only. Social, AI, analytics, refunds, disputes, invoice, booking reference, shipment, and generic CMS do not appear because they are excluded or awaiting redesign.

