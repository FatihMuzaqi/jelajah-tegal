<?php

namespace App\Http\Controllers\Dinas;

use App\Http\Controllers\Controller;
use App\Models\Mitra;
use App\Models\Order;
use App\Models\Ticket;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\View\View;

class TicketSalesController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()->can('dinas.analytics.view') || $request->user()->can('access.dinas'), 403);

        $dinasMitras = Mitra::where('category', 'dinas')->orderBy('display_name')->get();
        $dinasMitraIds = $dinasMitras->pluck('id')->toArray();

        $query = Ticket::whereIn('tickets.mitra_id', $dinasMitraIds)
            ->with(['mitra:id,display_name,legal_name', 'orderItem.order.user']);

        // 1. Filter by Mitra / Destinasi
        if ($request->filled('mitra_id')) {
            $query->where('tickets.mitra_id', $request->query('mitra_id'));
        }

        // 2. Filter by Status (unused, used, expired)
        if ($request->filled('status')) {
            $query->where('tickets.status', $request->query('status'));
        }

        // 3. Filter by Date Range
        if ($request->filled('start_date')) {
            $query->whereDate('tickets.created_at', '>=', $request->query('start_date'));
        }
        if ($request->filled('end_date')) {
            $query->whereDate('tickets.created_at', '<=', $request->query('end_date'));
        }

        // 4. Search Filter
        if ($request->filled('q')) {
            $search = '%' . trim($request->query('q')) . '%';
            $query->where(function ($q) use ($search) {
                $q->where('tickets.ticket_code', 'like', $search)
                  ->orWhereHas('orderItem', function ($oi) use ($search) {
                      $oi->where('item_name', 'like', $search)
                         ->orWhereHas('order', function ($ord) use ($search) {
                             $ord->where('order_number', 'like', $search)
                                 ->orWhereHas('user', fn($u) => $u->where('name', 'like', $search)->orWhere('email', 'like', $search));
                         });
                  });
            });
        }

        // Summary metrics for current query
        $totalTickets = (clone $query)->count();
        $usedTickets = (clone $query)->where('tickets.status', 'used')->count();
        $totalAmount = (clone $query)->join('order_items', 'tickets.order_item_id', '=', 'order_items.id')->sum('order_items.unit_price');

        $tickets = $query->latest('tickets.created_at')->paginate(20)->withQueryString();

        return view('dinas.ticket-sales.index', compact(
            'tickets',
            'dinasMitras',
            'totalTickets',
            'usedTickets',
            'totalAmount'
        ));
    }

    public function export(Request $request)
    {
        abort_unless($request->user()->can('dinas.reports.export') || $request->user()->can('access.dinas'), 403);

        $dinasMitras = Mitra::where('category', 'dinas')->orderBy('display_name')->get();
        $dinasMitraIds = $dinasMitras->pluck('id')->toArray();

        $query = Ticket::whereIn('tickets.mitra_id', $dinasMitraIds)
            ->with(['mitra:id,display_name,legal_name', 'orderItem.order.user']);

        if ($request->filled('mitra_id')) {
            $query->where('tickets.mitra_id', $request->query('mitra_id'));
        }
        if ($request->filled('status')) {
            $query->where('tickets.status', $request->query('status'));
        }
        if ($request->filled('start_date')) {
            $query->whereDate('tickets.created_at', '>=', $request->query('start_date'));
        }
        if ($request->filled('end_date')) {
            $query->whereDate('tickets.created_at', '<=', $request->query('end_date'));
        }

        $tickets = $query->latest('tickets.created_at')->get();

        $filename = 'rekapitulasi-retribusi-dinas-' . now()->format('Ymd-His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($tickets) {
            $file = fopen('php://output', 'w');
            fputs($file, "\xEF\xBB\xBF"); // BOM for UTF-8 Excel

            // Header row
            fputcsv($file, [
                'No.',
                'Kode Tiket',
                'No. Pesanan',
                'Destinasi / Objek Wisata',
                'Nama Pengunjung',
                'Layanan / Tiket',
                'Tarif Retribusi (IDR)',
                'Tanggal Booking',
                'Waktu Pembelian',
                'Status Tiket',
                'Waktu Check-In',
            ]);

            foreach ($tickets as $idx => $ticket) {
                fputcsv($file, [
                    $idx + 1,
                    $ticket->ticket_code,
                    $ticket->orderItem?->order?->order_number ?? '-',
                    $ticket->mitra?->display_name ?? '-',
                    $ticket->orderItem?->order?->user?->name ?? ($ticket->orderItem?->order?->user_snapshot['name'] ?? 'Pengunjung'),
                    $ticket->orderItem?->item_name ?? '-',
                    $ticket->orderItem?->unit_price ?? 0,
                    $ticket->valid_from?->format('Y-m-d') ?? '-',
                    $ticket->created_at?->format('Y-m-d H:i:s'),
                    strtoupper($ticket->status),
                    $ticket->used_at?->format('Y-m-d H:i:s') ?? '-',
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }
}
