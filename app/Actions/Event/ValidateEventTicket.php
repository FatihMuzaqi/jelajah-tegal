<?php
namespace App\Actions\Event;
use App\Models\EventTicket; use App\Models\EventTicketValidationLog; use App\Models\User; use App\Services\AuditLogger; use Illuminate\Support\Facades\DB; use Illuminate\Validation\ValidationException;
class ValidateEventTicket
{
    public function __construct(private AuditLogger $audit) {}
    public function execute(string $token,User $gatekeeper,string $mitraId,?string $device=null): EventTicket { return DB::transaction(function()use($token,$gatekeeper,$mitraId,$device){$ticket=EventTicket::where('qr_token_hash',hash('sha256',$token))->lockForUpdate()->first();if(!$ticket||$ticket->mitra_id!==$mitraId)throw ValidationException::withMessages(['token'=>'Tiket tidak valid untuk Mitra aktif.']);if($ticket->status->value!=='issued'||($ticket->valid_until&&$ticket->valid_until->isPast())){$ticket->validations()->create(['gatekeeper_user_id'=>$gatekeeper->id,'result'=>'rejected','device_reference'=>$device,'validated_at'=>now()]);throw ValidationException::withMessages(['token'=>'Tiket sudah digunakan, void, atau kedaluwarsa.']);}$ticket->update(['status'=>'used','used_at'=>now()]);$ticket->validations()->create(['gatekeeper_user_id'=>$gatekeeper->id,'result'=>'accepted','device_reference'=>$device,'validated_at'=>now()]);$this->audit->record('event.ticket_validated',$ticket,['status'=>'issued'],['status'=>'used'],$gatekeeper);return $ticket;}); }
}
