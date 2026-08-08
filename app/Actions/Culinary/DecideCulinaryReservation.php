<?php
namespace App\Actions\Culinary;
use App\Models\CulinaryReservation; use App\Models\User; use App\Notifications\DomainActivityNotification; use App\Services\AuditLogger; use Illuminate\Support\Facades\DB; use Illuminate\Validation\ValidationException;
class DecideCulinaryReservation
{
    public function __construct(private AuditLogger $audit) {}
    public function execute(CulinaryReservation $reservation,string $decision,User $actor,?string $reason=null): CulinaryReservation
    { if($reservation->status->value!=='requested') throw ValidationException::withMessages(['status'=>'Reservasi sudah diproses.']); if($decision==='reject'&&!$reason) throw ValidationException::withMessages(['reason'=>'Alasan penolakan wajib diisi.']); return DB::transaction(function() use($reservation,$decision,$actor,$reason){ $to=$decision==='confirm'?'confirmed':'rejected'; $reservation->update(['status'=>$to,'decided_by'=>$actor->id,'decided_at'=>now(),'decision_reason'=>$reason]); $this->audit->record('culinary.reservation_'.$to,$reservation,['status'=>'requested'],['status'=>$to],$actor); $reservation->user->notify(new DomainActivityNotification('Status reservasi kuliner','Reservasi '.$reservation->reservation_number.' '.$to.'.')); return $reservation->fresh(); }); }
}
