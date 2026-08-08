<?php
namespace App\Http\Controllers\Gatekeeper;
use App\Actions\Tickets\ValidateQrTicket;use App\Http\Controllers\Controller;use Illuminate\Http\RedirectResponse;use Illuminate\Http\Request;
class TicketController extends Controller
{public function validateTicket(Request $request,ValidateQrTicket $action):RedirectResponse{$data=$request->validate(['token'=>'required|string|min:40','device_reference'=>'nullable|string|max:100']);$ticket=$action->execute($data['token'],$request->user(),(string)$request->session()->get('active_mitra_id'),$data['device_reference']??null);return back()->with('status','Tiket '.$ticket->ticket_code.' valid dan telah digunakan.');}}
