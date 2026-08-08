<?php
namespace App\Actions\Event;
use App\Models\EventTicket; use App\Models\EventTicketType; use App\Models\User; use App\Notifications\DomainActivityNotification; use App\Services\AuditLogger; use Illuminate\Support\Facades\DB; use Illuminate\Validation\ValidationException;
class IssueEventTicket
{
    public function __construct(private AuditLogger $audit) {}
    public function execute(EventTicketType $type,User $consumer,User $actor): array { return DB::transaction(function()use($type,$consumer,$actor){$type=EventTicketType::lockForUpdate()->findOrFail($type->id);if($type->issued_quantity>=$type->quota)throw ValidationException::withMessages(['quota'=>'Kuota tiket habis.']);$raw=str()->random(64);$event=$type->event->catalogEntity;$ticket=EventTicket::create(['ticket_number'=>'EV-'.str()->upper(str()->random(14)),'event_ticket_type_id'=>$type->id,'mitra_id'=>$event->mitra_id,'user_id'=>$consumer->id,'qr_token_hash'=>hash('sha256',$raw),'status'=>'issued','valid_from'=>$type->event->starts_at,'valid_until'=>$type->event->ends_at]);$type->increment('issued_quantity');$this->audit->record('event.ticket_issued',$ticket,[],['consumer_id'=>$consumer->id],$actor);$consumer->notify(new DomainActivityNotification('Tiket event diterbitkan','Tiket '.$ticket->ticket_number.' tersedia.'));return [$ticket,$raw];}); }
}
