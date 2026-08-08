<?php

namespace App\Actions\Withdrawals;

use App\Models\Mitra;
use App\Models\MitraBalance;
use App\Models\MitraBankAccount;
use App\Models\User;
use App\Models\WithdrawalClaim;
use App\Services\AuditLogger;
use App\Services\Ledger\WithdrawalLedger;
use App\Support\Money;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SubmitWithdrawal
{
    public function __construct(private WithdrawalLedger $ledger, private AuditLogger $audit) {}

    public function execute(Mitra $mitra, User $actor, MitraBankAccount $bank, string $amount, string $idempotencyKey, ?string $notes = null): WithdrawalClaim
    {
        $fingerprint = hash('sha256', $bank->id.'|'.Money::fromMinor(Money::toMinor($amount)).'|'.trim((string) $notes));
        return DB::transaction(function () use ($mitra, $actor, $bank, $amount, $idempotencyKey, $notes, $fingerprint) {
            if ($existing = WithdrawalClaim::where('mitra_id', $mitra->id)->where('submitted_by', $actor->id)->where('idempotency_key', $idempotencyKey)->lockForUpdate()->first()) {
                if (! hash_equals($existing->request_fingerprint, $fingerprint)) throw ValidationException::withMessages(['idempotency_key' => 'Key sudah digunakan untuk permintaan berbeda.']);
                return $existing;
            }
            if ($bank->mitra_id !== $mitra->id || $bank->status !== 'verified' || ! $bank->verified_at) throw ValidationException::withMessages(['bank_account_id' => 'Rekening harus milik Mitra aktif dan telah diverifikasi.']);
            $minor = Money::toMinor($amount);
            $balance = MitraBalance::whereKey($mitra->id)->lockForUpdate()->first();
            if (! $balance || $minor <= 0 || Money::toMinor($balance->available_amount) < $minor) throw ValidationException::withMessages(['amount' => 'Saldo tersedia tidak mencukupi.']);
            $claim = WithdrawalClaim::create(['withdrawal_number' => 'WDR-'.now()->format('ymd').'-'.str()->upper(str()->random(10)), 'mitra_id' => $mitra->id, 'mitra_bank_account_id' => $bank->id, 'submitted_by' => $actor->id, 'amount' => Money::fromMinor($minor), 'status' => 'submitted', 'idempotency_key' => $idempotencyKey, 'request_fingerprint' => $fingerprint, 'bank_snapshot' => ['bank_code' => $bank->bank_code, 'account_name' => $bank->account_name_encrypted, 'last_four' => substr($bank->account_number_encrypted, -4), 'fingerprint' => $bank->account_fingerprint], 'notes' => $notes]);
            $this->ledger->hold($claim);
            $this->audit->record('withdrawal.submitted', $claim, [], ['amount' => $claim->amount, 'bank_code' => $bank->bank_code], $actor);
            return $claim->fresh();
        }, 5);
    }
}
