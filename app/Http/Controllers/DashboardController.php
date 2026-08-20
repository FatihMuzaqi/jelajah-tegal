<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\CatalogEntity;
use App\Models\FeatureFlag;
use App\Models\Mitra;
use App\Models\MitraFeatureRequest;
use App\Models\MitraKycDocument;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DashboardController extends Controller
{
    public function consumer(Request $r)
    {
        $u = $r->user();

        // 1. Consumer Real Metrics
        $totalOrders = \App\Models\Order::where('user_id', $u->id)->count();
        $paidOrdersCount = \App\Models\Order::where('user_id', $u->id)
            ->where(function ($q) {
                $q->whereIn('payment_status', ['paid', 'settlement', 'capture'])
                  ->orWhereIn('status', ['paid', 'confirmed', 'completed']);
            })->count();

        $aiPackagesCount = \App\Models\Invoice::where('user_id', $u->id)->count();
        $paidItinerariesCount = \App\Models\Invoice::where('user_id', $u->id)->where('status', 'paid')->whereNotNull('metadata->days')->count();
        $latestItinerary = \App\Models\Invoice::where('user_id', $u->id)->where('status', 'paid')->whereNotNull('metadata->days')->latest('paid_at')->first();
        $renterDocsCount = \App\Models\RenterDocument::where('user_id', $u->id)->count();

        // 2. Recent orders (max 5)
        $recentOrders = \App\Models\Order::where('user_id', $u->id)
            ->with(['mitra', 'items.tickets'])
            ->latest()
            ->limit(5)
            ->get();

        // 3. Recommended tourism items in Tegal
        $popularTourism = CatalogEntity::whereHas('serviceType', fn ($q) => $q->where('code', 'tourism'))
            ->where('status', 'published')
            ->with(['region', 'category', 'media', 'location'])
            ->latest()
            ->limit(4)
            ->get();

        return view('consumer.dashboard', compact(
            'u',
            'totalOrders',
            'paidOrdersCount',
            'aiPackagesCount',
            'paidItinerariesCount',
            'latestItinerary',
            'renterDocsCount',
            'recentOrders',
            'popularTourism'
        ));
    }

    public function mitra(Request $r)
    {
        $m = $this->activeMitra($r);
        $profileFields = ['display_name', 'description', 'contact_email', 'contact_phone', 'region_id', 'address', 'logo_media_id', 'banner_media_id'];
        $completed = collect($profileFields)->filter(fn ($field) => filled($m->{$field}))->count();
        $latestKyc = $m->kycDocuments()->latest()->first();
        $operational = [
            ['label' => 'Katalog draft', 'available' => Schema::hasTable('catalog_entities'), 'value' => Schema::hasTable('catalog_entities') ? CatalogEntity::where('mitra_id', $m->id)->where('status', 'draft')->count() : null],
            ['label' => 'Menunggu moderasi', 'available' => Schema::hasTable('catalog_entities'), 'value' => Schema::hasTable('catalog_entities') ? CatalogEntity::where('mitra_id', $m->id)->whereIn('status', ['submitted', 'under_review'])->count() : null],
            ['label' => 'Published', 'available' => Schema::hasTable('catalog_entities'), 'value' => Schema::hasTable('catalog_entities') ? CatalogEntity::where('mitra_id', $m->id)->where('status', 'published')->count() : null],
            ['label' => 'Rejected', 'available' => Schema::hasTable('catalog_entities'), 'value' => Schema::hasTable('catalog_entities') ? CatalogEntity::where('mitra_id', $m->id)->where('status', 'rejected')->count() : null],
            ['label' => 'Order', 'available' => Schema::hasTable('orders'), 'value' => null],
            ['label' => 'Saldo available', 'available' => Schema::hasTable('mitra_balances'), 'value' => null],
            ['label' => 'Saldo held', 'available' => Schema::hasTable('mitra_balances'), 'value' => null],
            ['label' => 'Withdrawal', 'available' => Schema::hasTable('withdrawal_claims'), 'value' => null],
            ['label' => 'Review terbaru', 'available' => Schema::hasTable('reviews'), 'value' => null],
        ];

        return $this->view('mitra', $r, [['label' => 'Kelengkapan profil', 'value' => round(($completed / count($profileFields)) * 100).'%', 'tone' => 'primary'], ['label' => 'Status KYC', 'value' => str($latestKyc?->status ?? 'belum dikirim')->headline(), 'tone' => $latestKyc?->status === 'approved' ? 'success' : 'warning'], ['label' => 'Fitur aktif', 'value' => $m->features()->where('status', 'enabled')->count(), 'tone' => 'success'], ['label' => 'Anggota aktif', 'value' => $m->members()->where('status', 'active')->count(), 'tone' => 'info']], AuditLog::where('mitra_id', $m->id)->latest('created_at')->limit(6)->get(), $m, [], $m->members()->with('user')->limit(8)->get(), ['operational' => $operational]);
    }

    public function gatekeeper(Request $r)
    {
        $m = $this->activeMitra($r);
        $isSuperAdmin = $r->user()->hasGlobalRole(['super-admin', 'admin']);
        $memberQuery = $r->user()->mitraMemberships()->where('mitra_id', $m->id);

        if ($isSuperAdmin && ! $memberQuery->exists()) {
            $active = $m->gatekeeperAssignments()->whereNull('revoked_at')->count();
            $assignments = $m->gatekeeperAssignments()->latest()->limit(8)->get();
        } else {
            $members = $memberQuery->pluck('id');
            $active = $m->gatekeeperAssignments()->whereIn('member_id', $members)->whereNull('revoked_at')->count();
            $assignments = $m->gatekeeperAssignments()->whereIn('member_id', $members)->latest()->limit(8)->get();
        }

        return $this->view('gatekeeper', $r, [['label' => 'Assignment aktif', 'value' => $active, 'tone' => 'primary']], collect(), $m, [], $assignments);
    }

    public function admin(Request $r)
    {
        $stats = [['label' => 'Pengguna', 'value' => User::count(), 'tone' => 'primary'], ['label' => 'Mitra aktif', 'value' => Mitra::where('status', 'active')->count(), 'tone' => 'success'], ['label' => 'KYC menunggu', 'value' => MitraKycDocument::whereIn('status', ['submitted', 'under_review'])->count(), 'tone' => 'warning'], ['label' => 'Request fitur', 'value' => MitraFeatureRequest::where('status', 'requested')->count(), 'tone' => 'info']];

        return $this->view('admin', $r, $stats, AuditLog::latest('created_at')->limit(6)->get(), null, $this->userStatusChart(), Mitra::with('owner')->latest()->limit(8)->get());
    }

    public function superAdmin(Request $r)
    {
        $stats = [['label' => 'Role', 'value' => Role::count(), 'tone' => 'primary'], ['label' => 'Permission', 'value' => Permission::count(), 'tone' => 'info'], ['label' => 'Feature flag aktif', 'value' => FeatureFlag::where('status', 'enabled')->count(), 'tone' => 'success'], ['label' => 'Mitra', 'value' => Mitra::count(), 'tone' => 'warning']];

        return $this->view('super-admin', $r, $stats, AuditLog::latest('created_at')->limit(6)->get(), null, $this->userStatusChart(), Role::latest()->limit(8)->get());
    }

    private function activeMitra(Request $r): Mitra
    {
        return Mitra::findOrFail($r->session()->get('active_mitra_id'));
    }

    private function userStatusChart(): array
    {
        $rows = User::select('status', DB::raw('count(*) total'))->groupBy('status')->get();

        return ['labels' => $rows->pluck('status')->values(), 'series' => $rows->pluck('total')->map(fn ($v) => (int) $v)->values()];
    }

    private function view(string $surface, Request $r, array $stats, $activity, $mitra = null, array $chart = [], $rows = null, array $extras = [])
    {
        $rows ??= collect();

        return view('dashboards.base', array_merge(compact('surface', 'stats', 'activity', 'mitra', 'chart', 'rows'), $extras));
    }
}
