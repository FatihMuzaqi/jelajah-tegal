<?php

namespace App\Http\Controllers\Dinas;

use App\Http\Controllers\Controller;
use App\Models\Mitra;
use App\Models\Order;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DestinationMonitoringController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()->can('access.dinas'), 403);

        $query = Mitra::where('category', 'dinas')
            ->with(['region:id,name', 'owner:id,name,email', 'features.serviceType', 'gatekeeperAssignments']);

        if ($request->filled('q')) {
            $search = '%' . trim($request->query('q')) . '%';
            $query->where(function ($q) use ($search) {
                $q->where('display_name', 'like', $search)
                  ->orWhere('legal_name', 'like', $search)
                  ->orWhere('slug', 'like', $search);
            });
        }

        $destinations = $query->latest()->get()->map(function ($mitra) {
            $totalRevenue = Order::where('mitra_id', $mitra->id)->where('status', 'paid')->sum('total_amount');
            $totalTickets = Ticket::where('mitra_id', $mitra->id)->count();
            $usedTickets = Ticket::where('mitra_id', $mitra->id)->where('status', 'used')->count();

            return [
                'model' => $mitra,
                'revenue' => (float) $totalRevenue,
                'total_tickets' => $totalTickets,
                'used_tickets' => $usedTickets,
                'active_gates' => $mitra->gatekeeperAssignments->whereNull('revoked_at')->count(),
            ];
        });

        return view('dinas.destinations.index', compact('destinations'));
    }
}
