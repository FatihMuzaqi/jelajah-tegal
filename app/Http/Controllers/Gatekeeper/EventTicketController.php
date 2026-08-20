<?php

namespace App\Http\Controllers\Gatekeeper;

use App\Actions\Event\ValidateEventTicket;
use App\Http\Controllers\Controller;
use App\Models\GatekeeperAssignment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EventTicketController extends Controller
{
    public function index(Request $r): View
    {
        abort_unless($r->user()->can('tickets.validate'), 403);

        $recentLogs = \App\Models\TicketValidationLog::where('gatekeeper_user_id', $r->user()->id)
            ->with(['ticket.orderItem', 'ticket.holderUser'])
            ->latest('scanned_at')
            ->limit(10)
            ->get();

        return view('gatekeeper.event-tickets.index', compact('recentLogs'));
    }

    public function validateTicket(Request $r, ValidateEventTicket $a): RedirectResponse
    {
        $d = $r->validate(['token' => 'required|string|min:20', 'device_reference' => 'nullable|string|max:100']);
        $mitraId = $r->session()->get('active_mitra_id');
        $assignment = GatekeeperAssignment::where('mitra_id', $mitraId)
            ->whereHas('member', fn ($query) => $query->where('user_id', $r->user()->id)->where('status', 'active'))
            ->whereNull('revoked_at')
            ->where(fn ($query) => $query->whereNull('valid_from')->orWhere('valid_from', '<=', now()))
            ->where(fn ($query) => $query->whereNull('valid_until')->orWhere('valid_until', '>=', now()))
            ->first();
        abort_unless($assignment, 403);
        $ticket = $a->execute($d['token'], $r->user(), $mitraId, $d['device_reference'] ?? null);
        $ticket->validations()->latest()->first()->update(['gatekeeper_assignment_id' => $assignment->id]);

        return back()->with('status', 'Tiket '.$ticket->ticket_number.' valid dan telah digunakan.');
    }
}
