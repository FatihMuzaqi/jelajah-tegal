# Financial Storage Strategy

## Money decision

ADR-017 already selected DECIMAL; this design uses DECIMAL(15,2) for every monetary value and CHAR(3) currency. Legacy BIGINT amounts are interpreted only after identifying their unit; they are not copied blindly. FLOAT/DOUBLE is forbidden for price, amount, balance, rate result, tax, discount, fee, and settlement.

PHP uses decimal strings/value objects and BCMath or approved decimal library. Rounding is explicit and tested; database values never pass through PHP float.

## Commercial snapshots

catalog_offers is current price. order_items copies item_name, SKU, unit_price, quantity, discount, tax, line_total, service window, and safe snapshot. Changing catalog never changes historical order. Header totals reconcile to line totals under documented rounding.

## Payment storage

- One order may have multiple payment attempts; one paid attempt determines settlement unless future split payment is designed.
- provider+provider_reference is unique.
- Webhook inbox preserves event hash/reference, verified status, redacted JSON and optional encrypted raw payload.
- Payment transition is idempotent and append-audited.
- paid requires amount/currency match; unknown/duplicate/out-of-order event cannot silently settle.

## Double-entry ledger

ledger_accounts identifies system/Mitra/user account. ledger_journals represents one business event; ledger_lines contains debit/credit. Before posted_at:

1. at least two lines;
2. exactly one positive side per line;
3. debit total equals credit total for each currency;
4. line currency matches account currency;
5. event_key unique;
6. source FK is explicit where available.

These cross-row rules are enforced in a transaction service and integrity tests; MySQL CHECK cannot sum rows. Posted rows are immutable. Reversal points to original journal and negates it; no update/delete correction.

## Mitra balance

mitra_balances is a projection for fast dashboard/claim check. It is rebuilt from posted ledger lines, records last_journal_id/rebuilt_at, and is updated atomically with posting or projection worker. Claim approval must validate authoritative ledger/holds under lock, not trust stale projection alone.

## Claim lifecycle

requested → under_review → approved → processing → paid; alternate rejected/cancelled/failed transitions. V1 payout remains manual evidence. Rules:

- verified bank account required;
- amount > 0 and net = amount - fee;
- available balance/hold applied atomically;
- requester cannot approve own claim where segregation policy requires;
- rejected has reason/reviewer;
- paid has payout reference/evidence and journal;
- no soft/hard delete.

## Voucher

V1 voucher is limited fixed/percentage rule with explicit decimal value, budget/limits, claim and usage. Apply/reverse status is stored; cancellation/refund reversal beyond agreed scenarios remains disabled. Voucher amount is snapshot on usage/order.

## Reconciliation controls

Scheduled checks report:

- paid payment without order paid/journal/ticket;
- amount/currency mismatch;
- duplicate provider/event references;
- unbalanced/empty posted journal;
- projection versus ledger difference;
- claim paid without payout evidence/journal;
- expired order with unreleased reservation.

No check auto-balances or invents suspense adjustment. Exception resolution requires an audited journal/action.

## Legacy conversion

For each BigInt money field determine whether unit is rupiah, cent/minor unit, or inconsistent by source model/version. Convert to DECIMAL(15,2) using an explicit mapping table and reconcile sums before/after. Commission Decimal(5,2) is a rate, not money; future commission rule uses DECIMAL(7,4) rate and DECIMAL(15,2) result.

