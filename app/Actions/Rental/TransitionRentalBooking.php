<?php
namespace App\Actions\Rental;
use App\Models\RentalBooking; use App\Models\User; use App\Notifications\DomainActivityNotification; use App\Services\AuditLogger; use Illuminate\Validation\ValidationException;
class TransitionRentalBooking
{
    private const NEXT=['requested'=>['document_review','rejected','cancelled'],'document_review'=>['approved','rejected'],'approved'=>['active','cancelled'],'active'=>['completed']];
    public function __construct(private AuditLogger $audit) {}
    public function execute(RentalBooking $booking,string $to,User $actor,?string $reason=null): RentalBooking { $from=$booking->status->value;if(!in_array($to,self::NEXT[$from]??[],true))throw ValidationException::withMessages(['status'=>'Transisi booking tidak valid.']);if($to==='rejected'&&!$reason)throw ValidationException::withMessages(['reason'=>'Alasan penolakan wajib diisi.']);if($to==='approved'&&$booking->documents()->where('status','!=','approved')->exists())throw ValidationException::withMessages(['documents'=>'Semua dokumen wajib disetujui.']);$booking->update(['status'=>$to,'decided_by'=>$actor->id,'decided_at'=>now(),'decision_reason'=>$reason]);$this->audit->record('rental.booking_'.$to,$booking,['status'=>$from],['status'=>$to],$actor);$booking->user->notify(new DomainActivityNotification('Status booking rental','Booking '.$booking->booking_number.' menjadi '.$to.'.'));return $booking->fresh(); }
}
