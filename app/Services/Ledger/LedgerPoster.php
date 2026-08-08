<?php

namespace App\Services\Ledger;

use App\Models\LedgerAccount;
use App\Models\LedgerJournal;
use App\Models\Order;
use App\Models\Payment;
use App\Support\Money;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class LedgerPoster
{
    public function paymentRefunded(Order $order, Payment $payment): ?LedgerJournal
    {
        $captured = LedgerJournal::with('lines')->where('event_key', 'payment.captured:'.$payment->id)->first();
        if (! $captured) return null;
        if ($existing = LedgerJournal::where('event_key', 'payment.refunded:'.$payment->id)->first()) return $existing;

        return DB::transaction(function () use ($order, $payment, $captured) {
            $journal = LedgerJournal::create(['journal_number' => 'JRN-'.now()->format('ymd').'-'.str()->upper(str()->random(10)), 'mitra_id' => $order->mitra_id, 'event_key' => 'payment.refunded:'.$payment->id, 'event_type' => 'payment_refunded', 'order_id' => $order->id, 'payment_id' => $payment->id, 'reversal_of_id' => $captured->id, 'description' => 'Payment refunded '.$order->order_number, 'effective_at' => now(), 'posted_at' => now(), 'metadata' => ['currency' => $order->currency], 'created_at' => now()]);
            foreach ($captured->lines as $line) {
                $journal->lines()->create(['ledger_account_id' => $line->ledger_account_id, 'sequence' => $line->sequence, 'debit_amount' => $line->credit_amount, 'credit_amount' => $line->debit_amount, 'currency' => $line->currency, 'created_at' => now()]);
            }
            $net = Money::fromMinor(Money::toMinor($order->mitra_net_amount));
            DB::table('mitra_balances')->where('mitra_id', $order->mitra_id)->lockForUpdate()->update(['available_amount' => DB::raw('available_amount - '.$net), 'total_earned_amount' => DB::raw('total_earned_amount - '.$net), 'last_journal_id' => $journal->id, 'rebuilt_at' => now(), 'updated_at' => now()]);
            return $journal;
        });
    }

    public function paymentCaptured(Order $order, Payment $payment): ?LedgerJournal
    {
        if (Money::toMinor($order->total_amount) === 0 && Money::toMinor($order->mitra_net_amount) === 0 && Money::toMinor($order->commission_amount) === 0) {
            return null;
        }$existing = LedgerJournal::where('event_key', 'payment.captured:'.$payment->id)->first();
        if ($existing) {
            return $existing;
        }

return DB::transaction(function () use ($order, $payment) {
            $cash = $this->system('platform_cash', 'platform_cash');
            $revenue = $this->system('platform_revenue', 'platform_revenue');
            $payable = $this->mitra($order->mitra_id, 'mitra_available');
            $debits = [[$cash, Money::toMinor($order->total_amount)]];
            $sponsor = $order->voucher_snapshot['sponsor'] ?? null;
            if ($sponsor === 'platform' && Money::toMinor($order->discount_amount) > 0) {
                $debits[] = [$revenue, Money::toMinor($order->discount_amount)];
            }$credits = [[$payable, Money::toMinor($order->mitra_net_amount)], [$revenue, Money::toMinor($order->commission_amount) + Money::toMinor($order->admin_fee)]];
            $debitTotal = array_sum(array_column($debits, 1));
            $creditTotal = array_sum(array_column($credits, 1));
            if ($debitTotal !== $creditTotal) {
                throw new RuntimeException('Ledger payment tidak seimbang.');
            }$journal = LedgerJournal::create(['journal_number' => 'JRN-'.now()->format('ymd').'-'.str()->upper(str()->random(10)), 'mitra_id' => $order->mitra_id, 'event_key' => 'payment.captured:'.$payment->id, 'event_type' => 'payment_captured', 'order_id' => $order->id, 'payment_id' => $payment->id, 'description' => 'Payment captured '.$order->order_number, 'effective_at' => now(), 'posted_at' => now(), 'metadata' => ['currency' => $order->currency], 'created_at' => now()]);
            $seq = 1;
            foreach ($debits as [$account,$amount]) {
                if ($amount > 0) {
                    $journal->lines()->create(['ledger_account_id' => $account->id, 'sequence' => $seq++, 'debit_amount' => Money::fromMinor($amount), 'credit_amount' => '0.00', 'currency' => $order->currency, 'created_at' => now()]);
                }
            }foreach ($credits as [$account,$amount]) {
                if ($amount > 0) {
                    $journal->lines()->create(['ledger_account_id' => $account->id, 'sequence' => $seq++, 'debit_amount' => '0.00', 'credit_amount' => Money::fromMinor($amount), 'currency' => $order->currency, 'created_at' => now()]);
                }
            }$net = Money::fromMinor(Money::toMinor($order->mitra_net_amount));
            $balance = DB::table('mitra_balances')->where('mitra_id', $order->mitra_id)->lockForUpdate()->first();
            if ($balance) {
                DB::table('mitra_balances')->where('mitra_id', $order->mitra_id)->update(['available_amount' => DB::raw('available_amount + '.$net), 'total_earned_amount' => DB::raw('total_earned_amount + '.$net), 'last_journal_id' => $journal->id, 'rebuilt_at' => now(), 'updated_at' => now()]);
            } else {
                DB::table('mitra_balances')->insert(['mitra_id' => $order->mitra_id, 'currency' => $order->currency, 'available_amount' => $net, 'held_amount' => '0.00', 'total_earned_amount' => $net, 'last_journal_id' => $journal->id, 'rebuilt_at' => now(), 'updated_at' => now()]);
            }

            return $journal;
        });
    }

    private function system(string $code, string $type): LedgerAccount
    {
        return LedgerAccount::firstOrCreate(['system_code' => $code, 'currency' => 'IDR'], ['account_type' => $type, 'status' => 'active']);
    }

    private function mitra(string $id, string $type): LedgerAccount
    {
        return LedgerAccount::firstOrCreate(['mitra_id' => $id, 'account_type' => $type, 'currency' => 'IDR'], ['status' => 'active']);
    }
}
