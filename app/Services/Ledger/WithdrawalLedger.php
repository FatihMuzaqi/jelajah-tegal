<?php

namespace App\Services\Ledger;

use App\Models\LedgerAccount;
use App\Models\LedgerJournal;
use App\Models\MitraBalance;
use App\Models\WithdrawalClaim;
use App\Support\Money;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class WithdrawalLedger
{
    public function hold(WithdrawalClaim $claim): LedgerJournal
    {
        return $this->post($claim, 'held', 'withdrawal_held', $this->mitra($claim, 'mitra_available'), $this->mitra($claim, 'mitra_held'), function (MitraBalance $balance, string $amount) {
            $balance->decrement('available_amount', (float) $amount);
            $balance->increment('held_amount', (float) $amount);
        });
    }

    public function release(WithdrawalClaim $claim, string $reason): LedgerJournal
    {
        return $this->post($claim, 'released', 'withdrawal_'.$reason, $this->mitra($claim, 'mitra_held'), $this->mitra($claim, 'mitra_available'), function (MitraBalance $balance, string $amount) {
            $balance->decrement('held_amount', (float) $amount);
            $balance->increment('available_amount', (float) $amount);
        });
    }

    public function processing(WithdrawalClaim $claim): LedgerJournal
    {
        return $this->post($claim, 'processing', 'withdrawal_processing', $this->mitra($claim, 'mitra_held'), $this->mitra($claim, 'withdrawal_payable'), function (MitraBalance $balance, string $amount) {
            $balance->decrement('held_amount', (float) $amount);
        });
    }

    public function paid(WithdrawalClaim $claim): LedgerJournal
    {
        return $this->post($claim, 'paid', 'withdrawal_paid', $this->mitra($claim, 'withdrawal_payable'), $this->system('platform_cash'), fn () => null);
    }

    private function post(WithdrawalClaim $claim, string $suffix, string $eventType, LedgerAccount $debit, LedgerAccount $credit, callable $updateBalance): LedgerJournal
    {
        $eventKey = 'withdrawal.'.$suffix.':'.$claim->id;
        if ($existing = LedgerJournal::where('event_key', $eventKey)->first()) return $existing;
        $minor = Money::toMinor($claim->amount);
        if ($minor <= 0) throw new RuntimeException('Nominal jurnal withdrawal harus positif.');
        $currency = $claim->currency ?? 'IDR';

        return DB::transaction(function () use ($claim, $eventKey, $eventType, $debit, $credit, $updateBalance, $minor, $currency) {
            $balance = MitraBalance::whereKey($claim->mitra_id)->lockForUpdate()->firstOrFail();
            $amount = Money::fromMinor($minor);
            $journal = LedgerJournal::create(['journal_number' => 'JRN-'.now()->format('ymd').'-'.str()->upper(str()->random(10)), 'mitra_id' => $claim->mitra_id, 'event_key' => $eventKey, 'event_type' => $eventType, 'withdrawal_claim_id' => $claim->id, 'description' => $eventType.' '.$claim->withdrawal_number, 'effective_at' => now(), 'posted_at' => now(), 'metadata' => ['currency' => $currency], 'created_at' => now()]);
            $journal->lines()->create(['ledger_account_id' => $debit->id, 'sequence' => 1, 'debit_amount' => $amount, 'credit_amount' => '0.00', 'currency' => $currency, 'created_at' => now()]);
            $journal->lines()->create(['ledger_account_id' => $credit->id, 'sequence' => 2, 'debit_amount' => '0.00', 'credit_amount' => $amount, 'currency' => $currency, 'created_at' => now()]);
            if ($journal->lines()->sum('debit_amount') !== $journal->lines()->sum('credit_amount')) throw new RuntimeException('Jurnal withdrawal tidak seimbang.');
            $updateBalance($balance, $amount);
            $balance->update(['last_journal_id' => $journal->id, 'rebuilt_at' => now()]);
            return $journal;
        });
    }

    private function mitra(WithdrawalClaim $claim, string $type): LedgerAccount
    {
        $currency = $claim->currency ?? 'IDR';

        return LedgerAccount::firstOrCreate(['mitra_id' => $claim->mitra_id, 'account_type' => $type, 'currency' => $currency], ['status' => 'active']);
    }

    private function system(string $type): LedgerAccount
    {
        return LedgerAccount::firstOrCreate(['system_code' => $type, 'currency' => 'IDR'], ['account_type' => $type, 'status' => 'active']);
    }
}
