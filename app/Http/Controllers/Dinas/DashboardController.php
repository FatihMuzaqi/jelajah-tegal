<?php

namespace App\Http\Controllers\Dinas;

use App\Http\Controllers\Controller;
use App\Models\Mitra;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Ticket;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()->can('access.dinas'), 403);

        $selectedYear = (int) $request->input('year', now()->year);
        $selectedMonth = $request->filled('month') ? (int) $request->input('month') : (int) now()->month;
        $selectedMitraId = $request->input('mitra_id');

        // 1. Get all Dinas partners (for filter dropdown & stats)
        $dinasMitras = Mitra::where('category', 'dinas')->orderBy('display_name')->get();
        $dinasMitraIds = $dinasMitras->pluck('id')->toArray();

        // Target mitra IDs (either all dinas or selected one)
        $targetMitraIds = $selectedMitraId ? [$selectedMitraId] : $dinasMitraIds;

        // 2. Base Order Query (Paid orders belonging to Dinas Mitras)
        $baseOrderQuery = Order::whereIn('mitra_id', $targetMitraIds)
            ->where('status', 'paid');

        // 2a. Today's Revenue (PAD Hari Ini)
        $todayRevenue = (clone $baseOrderQuery)
            ->whereDate('paid_at', Carbon::today())
            ->sum('total_amount');

        // 2b. Month's Revenue (PAD Bulan Ini)
        $monthRevenue = (clone $baseOrderQuery)
            ->whereYear('paid_at', $selectedYear)
            ->whereMonth('paid_at', $selectedMonth)
            ->sum('total_amount');

        // 2c. Year's Revenue (PAD Tahun Berjalan)
        $yearRevenue = (clone $baseOrderQuery)
            ->whereYear('paid_at', $selectedYear)
            ->sum('total_amount');

        // 2d. Lifetime Revenue (Total PAD Sepanjang Masa)
        $totalLifetimeRevenue = (clone $baseOrderQuery)
            ->sum('total_amount');

        // 3. Ticket Volume Statistics
        $baseTicketQuery = Ticket::whereIn('mitra_id', $targetMitraIds);

        $todayTicketsCount = (clone $baseTicketQuery)
            ->whereDate('created_at', Carbon::today())
            ->count();

        $monthTicketsCount = (clone $baseTicketQuery)
            ->whereYear('created_at', $selectedYear)
            ->whereMonth('created_at', $selectedMonth)
            ->count();

        $yearTicketsCount = (clone $baseTicketQuery)
            ->whereYear('created_at', $selectedYear)
            ->count();

        $checkedInVisitorsCount = (clone $baseTicketQuery)
            ->where('status', 'used')
            ->whereYear('used_at', $selectedYear)
            ->whereMonth('used_at', $selectedMonth)
            ->count();

        // 4. Monthly Trend Data for Chart.js (Jan - Dec)
        $monthlyRevenueData = [];
        $monthlyTicketsData = [];
        $monthLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

        for ($m = 1; $m <= 12; $m++) {
            $rev = (clone $baseOrderQuery)
                ->whereYear('paid_at', $selectedYear)
                ->whereMonth('paid_at', $m)
                ->sum('total_amount');

            $tix = (clone $baseTicketQuery)
                ->whereYear('created_at', $selectedYear)
                ->whereMonth('created_at', $m)
                ->count();

            $monthlyRevenueData[] = (float) $rev;
            $monthlyTicketsData[] = $tix;
        }

        // 5. Daily Trend Data for selected month (Days 1 - 31)
        $daysInMonth = Carbon::create($selectedYear, $selectedMonth, 1)->daysInMonth;
        $dailyRevenueData = [];
        $dailyLabels = [];

        for ($d = 1; $d <= $daysInMonth; $d++) {
            $dateStr = sprintf('%04d-%02d-%02d', $selectedYear, $selectedMonth, $d);
            $dailyLabels[] = (string) $d;

            $rev = (clone $baseOrderQuery)
                ->whereDate('paid_at', $dateStr)
                ->sum('total_amount');

            $dailyRevenueData[] = (float) $rev;
        }

        // 6. Ranking / Contribution by Dinas Destination
        $destinationRankings = Mitra::whereIn('id', $dinasMitraIds)
            ->withCount(['tickets' => fn($q) => $q->whereYear('created_at', $selectedYear)])
            ->get()
            ->map(function ($mitra) use ($selectedYear) {
                $revenue = Order::where('mitra_id', $mitra->id)
                    ->where('status', 'paid')
                    ->whereYear('paid_at', $selectedYear)
                    ->sum('total_amount');

                $visitors = Ticket::where('mitra_id', $mitra->id)
                    ->where('status', 'used')
                    ->whereYear('used_at', $selectedYear)
                    ->count();

                return [
                    'mitra' => $mitra,
                    'revenue' => (float) $revenue,
                    'tickets_count' => $mitra->tickets_count,
                    'visitors_count' => $visitors,
                ];
            })
            ->sortByDesc('revenue')
            ->values();

        // 7. Recent 5 Ticket Orders from Dinas
        $recentOrders = (clone $baseOrderQuery)
            ->with(['mitra', 'items.tickets', 'user'])
            ->latest('paid_at')
            ->take(5)
            ->get();

        return view('dinas.dashboard', compact(
            'dinasMitras',
            'selectedYear',
            'selectedMonth',
            'selectedMitraId',
            'todayRevenue',
            'monthRevenue',
            'yearRevenue',
            'totalLifetimeRevenue',
            'todayTicketsCount',
            'monthTicketsCount',
            'yearTicketsCount',
            'checkedInVisitorsCount',
            'monthLabels',
            'monthlyRevenueData',
            'monthlyTicketsData',
            'dailyLabels',
            'dailyRevenueData',
            'destinationRankings',
            'recentOrders'
        ));
    }
}
