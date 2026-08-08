<?php
namespace App\Actions\Tickets;
use App\Models\Ticket;use App\Models\User;use App\Services\AuditLogger;use Illuminate\Support\Facades\DB;use Illuminate\Validation\ValidationException;
class RevokeTicket
{public function __construct(private AuditLogger $audit){}public function execute(Ticket $ticket,User $actor,string $reason):Ticket{return DB::transaction(function()use($ticket,$actor,$reason){$ticket=Ticket::lockForUpdate()->findOrFail($ticket->id);if(!in_array($ticket->status,['unused','active'],true))throw ValidationException::withMessages(['ticket'=>'Hanya tiket belum digunakan yang dapat dicabut.']);$before=$ticket->status;$ticket->update(['status'=>'revoked','revoked_by'=>$actor->id,'revoked_at'=>now(),'revocation_reason'=>$reason]);$this->audit->record('ticket.revoked',$ticket,['status'=>$before],['status'=>'revoked','reason'=>$reason],$actor);return $ticket->fresh();});}}
