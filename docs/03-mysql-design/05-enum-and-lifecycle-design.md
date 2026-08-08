# Enum and Lifecycle Design

All enums are lowercase PHP backed enums persisted as VARCHAR(32/64). MySQL ENUM is not used. Unknown migrated value is quarantined, never silently defaulted.

## Core enums

| Enum | Values | Lifecycle notes |
| --- | --- | --- |
| UserStatus | pending, active, locked, suspended, disabled | lock is security-temporary; suspension administrative |
| MitraStatus | draft, submitted, under_review, active, rejected, suspended, archived | active criteria remains product ADR |
| MemberStatus | invited, active, suspended, revoked | revoked terminal |
| FeatureStatus | disabled, enabled, suspended | entitlement, distinct from rollout flag |
| FeatureRequestStatus | requested, under_review, approved, rejected, cancelled | approved may enable feature in same transaction |
| KycStatus | submitted, under_review, approved, rejected, expired, superseded | reject requires reason; no delete |
| BankAccountStatus | pending, verified, rejected, disabled | claim requires verified |
| CatalogStatus | draft, pending_review, published, rejected, suspended, archived | only published public |
| OfferStatus | draft, active, paused, sold_out, archived | active also respects purchase window |
| AvailabilityStatus | available, blocked, sold_out, closed | capacity constraint remains numeric |
| ReviewStatus | pending, published, rejected, hidden | verified state derives order item, not enum |
| OrderStatus | pending_payment, paid, confirmed, processing, fulfilled, completed, cancelled, expired, refunded | refunded reserved for future verified flow |
| FulfillmentStatus | pending, reserved, issued, fulfilled, cancelled | item-level |
| PaymentStatus | pending, requires_action, paid, denied, cancelled, expired, failed, partially_refunded, refunded | consolidated legacy duplicates |
| WebhookStatus | received, processing, processed, ignored, failed | retries only failed/transient |
| TicketStatus | active, used, expired, cancelled, revoked | active→used atomic |
| ValidationResult | accepted, already_used, expired, cancelled, revoked, invalid, unauthorized | every scan logs result |
| ClaimStatus | requested, under_review, approved, processing, paid, rejected, cancelled, failed | single consolidated flow |
| LedgerAccountStatus | active, frozen, closed | closed account remains readable |
| VoucherStatus | draft, active, paused, expired, exhausted, archived | V1 limited |
| VoucherClaimStatus | claimed, reserved, used, expired, released | one claim per user/voucher |
| VoucherUsageStatus | applied, reversed | reversal policy must be explicit |
| ModerationStatus | open, investigating, resolved, dismissed | actions append history |
| BroadcastStatus | draft, scheduled, sending, sent, cancelled, failed | queued fan-out |
| MediaStatus | pending, ready, quarantined, deletion_pending, deleted, failed | object lifecycle |

## Payment normalization

| Legacy value | Target | Rule |
| --- | --- | --- |
| PENDING | pending | Direct |
| CAPTURED, SETTLEMENT | paid | Only if signed provider evidence and amount match |
| DENY | denied | Direct |
| CANCEL | cancelled | Direct |
| EXPIRE, EXPIRED | expired | Consolidate |
| FAILURE, FAILED | failed | Consolidate |
| REFUND, REFUNDED | refunded | Only with verified refund evidence; otherwise quarantine because target refund is deferred |

## Claim normalization

| Legacy values | Target |
| --- | --- |
| PENDING, REQUESTED, SUBMITTED | requested |
| UNDER_REVIEW | under_review |
| APPROVED | approved |
| PROCESSING | processing |
| PAID | paid |
| COMPLETED | paid only with payout evidence; otherwise exception review |
| REJECTED | rejected |
| CANCELLED | cancelled |

## Transition invariants

- Payment paid requires paid_at, canonical amount/currency, and unique provider reference.
- Order paid only follows verified payment; expired/cancelled cannot return pending.
- Ticket used requires accepted validation log and used_at in one transaction.
- Posted journal and lines are immutable; correction is reversal.
- Claim rejected requires reviewer/reason; paid requires payout reference/evidence and journal.
- Published catalog requires moderation/required fields and published_at.
- KYC approved/rejected requires reviewer and timestamp.
- Feature flag does not change permission or Mitra entitlement.

## V2/Future enum rule

Rental, marketplace, and virtual-tour enum values are drafted only when V2 use cases finalize. Social/AI/analytics/refund/dispute enum families are not created now.

