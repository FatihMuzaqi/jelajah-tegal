<?php

namespace App\Actions\Withdrawals;

use App\Models\DatabaseNotification;
use App\Models\User;
use App\Models\WithdrawalClaim;
use App\Models\WithdrawalTransfer;
use App\Services\AuditLogger;
use App\Services\Ledger\WithdrawalLedger;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TransitionWithdrawal
{
    public function __construct(private WithdrawalLedger $ledger, private AuditLogger $audit) {}

    public function execute(WithdrawalClaim $claim, string $transition, User $actor, array $data = []): WithdrawalClaim
    {
        return DB::transaction(function () use ($claim, $transition, $actor, $data) {
            $claim = WithdrawalClaim::lockForUpdate()->findOrFail($claim->id);
            $from = $claim->status->value;
            $allowed = ['review' => ['submitted'], 'approve' => ['under_review'], 'reject' => ['submitted', 'under_review', 'approved'], 'processing' => ['approved'], 'paid' => ['processing'], 'cancel' => ['submitted', 'under_review', 'approved']];
            if (! in_array($from, $allowed[$transition] ?? [], true)) throw ValidationException::withMessages(['status' => 'Transisi withdrawal tidak valid dari '.$from.'.']);
            $now = now();
            if ($transition === 'review') $claim->update(['status' => 'under_review', 'reviewed_by' => $actor->id, 'reviewed_at' => $now]);
            if ($transition === 'approve') $claim->update(['status' => 'approved', 'approved_by' => $actor->id, 'approved_at' => $now]);
            if ($transition === 'reject') {if (blank($data['reason'] ?? null)) throw ValidationException::withMessages(['reason' => 'Alasan penolakan wajib.']);$this->ledger->release($claim, 'rejected');$claim->update(['status' => 'rejected', 'rejected_by' => $actor->id, 'rejected_at' => $now, 'rejection_reason' => $data['reason']]);}
            if ($transition === 'cancel') {if (blank($data['reason'] ?? null)) throw ValidationException::withMessages(['reason' => 'Alasan pembatalan wajib.']);$this->ledger->release($claim, 'cancelled');$claim->update(['status' => 'cancelled', 'cancelled_by' => $actor->id, 'cancelled_at' => $now, 'cancellation_reason' => $data['reason']]);}
            if ($transition === 'processing') {$this->ledger->processing($claim);$claim->update(['status' => 'processing', 'processing_by' => $actor->id, 'processing_at' => $now]);}
            if ($transition === 'paid') {if (blank($data['transfer_reference'] ?? null)||blank($data['transferred_at']??null)) throw ValidationException::withMessages(['transfer_reference' => 'Referensi dan waktu transfer wajib.']);WithdrawalTransfer::create(['withdrawal_claim_id' => $claim->id, 'transfer_reference' => $data['transfer_reference'], 'amount' => $claim->amount, 'currency' => $claim->currency, 'bank_snapshot' => $claim->bank_snapshot, 'recorded_by' => $actor->id, 'transferred_at' => $data['transferred_at'], 'notes' => $data['notes'] ?? null]);$this->ledger->paid($claim);$claim->update(['status' => 'paid', 'paid_by' => $actor->id, 'paid_at' => $now]);}
            $claim = $claim->fresh();
            $this->audit->record('withdrawal.'.$transition, $claim, ['status' => $from], ['status' => $claim->status->value], $actor);
            DatabaseNotification::create(['user_id' => $claim->submitted_by, 'mitra_id' => $claim->mitra_id, 'type' => 'withdrawal.'.$claim->status->value, 'data' => ['withdrawal_id' => $claim->id, 'withdrawal_number' => $claim->withdrawal_number, 'status' => $claim->status->value]]);
            return $claim;
        }, 5);
    }
}
