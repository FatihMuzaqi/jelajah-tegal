<?php
namespace App\Http\Controllers\Mitra;
use App\Actions\Tickets\RevokeTicket;use App\Http\Controllers\Controller;use App\Http\Controllers\Mitra\Concerns\ResolvesActiveMitra;use App\Models\Ticket;use Illuminate\Http\RedirectResponse;use Illuminate\Http\Request;
class TicketController extends Controller
{use ResolvesActiveMitra;public function revoke(Request $request,Ticket $ticket,RevokeTicket $action):RedirectResponse{abort_unless($request->user()->can('tickets.issue')&&$ticket->mitra_id===$this->activeMitra($request)->id,403);$data=$request->validate(['reason'=>'required|string|min:5|max:500']);$action->execute($ticket,$request->user(),$data['reason']);return back()->with('status','Tiket dicabut.');}}
