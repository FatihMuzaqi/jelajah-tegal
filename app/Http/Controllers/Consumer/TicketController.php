<?php
namespace App\Http\Controllers\Consumer;
use App\Http\Controllers\Controller;use App\Models\Ticket;use App\Services\Tickets\QrTicketRenderer;use App\Support\TicketToken;use Illuminate\Http\Request;use Symfony\Component\HttpFoundation\Response;
class TicketController extends Controller
{public function qr(Request $request,Ticket $ticket,QrTicketRenderer $renderer):Response{abort_unless($ticket->holder_user_id===$request->user()->id,403);abort_unless(in_array($ticket->status,['unused','active'],true),410);$token=TicketToken::for($ticket->id,$ticket->token_version);abort_unless(hash_equals($ticket->qr_token_hash,TicketToken::hash($token)),410);return response($renderer->svg($token),200,['Content-Type'=>'image/svg+xml','Cache-Control'=>'private, no-store']);}}
