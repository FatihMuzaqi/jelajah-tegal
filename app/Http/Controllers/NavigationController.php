<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class NavigationController extends Controller
{
    private array $map = [
        'super-admin' => 'super-admin.dashboard',
        'admin' => 'admin.dashboard',
        'dinas' => 'dinas.dashboard',
        'gatekeeper' => 'mitra.select',
        'mitra' => 'mitra.select',
        'consumer' => 'consumer.dashboard'
    ];

    public function redirect(Request $r): RedirectResponse
    {
        $allowed = $this->allowed($r);

        // 1. Cek apakah ada URL tujuan sebelum login (misal user sedang membuka halaman detail lalu login)
        $intended = session()->pull('url.intended');
        if ($intended && ! str_contains($intended, 'post-login') && ! str_contains($intended, 'login')) {
            return redirect()->to($intended);
        }

        // 1.5 Cek status jika akun adalah pemilik pendaftaran mitra yang masih pending atau ditolak
        $pendingMitra = $r->user()->ownedMitras()->whereIn('status', ['pending', 'rejected'])->latest()->first();
        if ($pendingMitra && ! $r->user()->hasGlobalRole(['super-admin', 'admin', 'dinas'])) {
            $hasActive = $r->user()->mitraMemberships()->where('status', 'active')->whereHas('mitra', fn($q) => $q->where('status', 'active'))->exists();
            if (! $hasActive) {
                return redirect()->route('mitra.pending-notice');
            }
        }

        // 2. Jika murni Consumer (Wisatawan) -> Arahkan langsung ke Landing Page (Beranda)
        if ($allowed === ['consumer']) {
            return redirect()->route('home')->with('status', 'Selamat datang di Jelajah Tegal, ' . $r->user()->name . '!');
        }

        // 3. Ambil role manajemen bisnis/operasional di luar role consumer
        $managementSurfaces = array_values(array_diff($allowed, ['consumer']));

        // Prioritas khusus: Jika Super Admin -> Langsung ke Dashboard Super Admin
        if (in_array('super-admin', $managementSurfaces)) {
            return redirect()->route('super-admin.dashboard');
        }

        // Jika memiliki tepat 1 role manajemen -> Arahkan langsung ke Dashboard kerja peran tersebut
        if (count($managementSurfaces) === 1) {
            $roleSurface = $managementSurfaces[0];

            if ($roleSurface === 'mitra') {
                $memberships = $r->user()->mitraMemberships()->with('mitra')->where('status', 'active')->get();
                if ($memberships->count() === 1) {
                    $r->session()->put('active_mitra_id', $memberships->first()->mitra_id);
                    return redirect()->route('mitra.dashboard');
                }
                return redirect()->route('mitra.select');
            }

            if ($roleSurface === 'gatekeeper') {
                $memberships = $r->user()->mitraMemberships()->with('mitra')->where('status', 'active')->get();
                if ($memberships->count() === 1) {
                    $r->session()->put('active_mitra_id', $memberships->first()->mitra_id);
                    return redirect()->route('gatekeeper.dashboard');
                }
                return redirect()->route('mitra.select');
            }

            if ($roleSurface === 'dinas') {
                return redirect()->route('dinas.dashboard');
            }

            if ($roleSurface === 'admin') {
                return redirect()->route('admin.dashboard');
            }

            if ($roleSurface === 'super-admin') {
                return redirect()->route('super-admin.dashboard');
            }
        }

        // 4. Jika user memiliki banyak role manajemen sekaligus (misal Super Admin sekaligus Mitra Owner)
        if (count($managementSurfaces) > 1) {
            return redirect()->route('surfaces.select');
        }

        // Default fallback
        return redirect()->route('home');
    }

    public function surfaces(Request $r): View
    {
        return view('surfaces', ['surfaces' => $this->allowed($r)]);
    }

    public function choose(Request $r): RedirectResponse
    {
        $s = $r->validate(['surface' => 'required|string'])['surface'];
        abort_unless(isset($this->map[$s]) && in_array($s, $this->allowed($r)), 403);

        if ($s === 'consumer') {
            return redirect()->route('consumer.dashboard');
        }

        if ($s === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        if ($s === 'super-admin') {
            return redirect()->route('super-admin.dashboard');
        }

        if ($s === 'dinas') {
            return redirect()->route('dinas.dashboard');
        }

        if ($s === 'gatekeeper') {
            session(['target_tenant_surface' => 'gatekeeper']);

            if ($r->user()->hasGlobalRole(['super-admin', 'admin'])) {
                return redirect()->route('mitra.select');
            }

            $memberships = $r->user()->mitraMemberships()->with('mitra')->where('status', 'active')->get();
            if ($memberships->count() === 1) {
                $r->session()->put('active_mitra_id', $memberships->first()->mitra_id);
                return redirect()->route('gatekeeper.dashboard');
            }
            return redirect()->route('mitra.select');
        }

        if ($s === 'mitra') {
            session(['target_tenant_surface' => 'mitra']);

            if ($r->user()->hasGlobalRole(['super-admin', 'admin'])) {
                return redirect()->route('mitra.select');
            }

            $memberships = $r->user()->mitraMemberships()->with('mitra')->where('status', 'active')->get();
            if ($memberships->count() === 1) {
                $r->session()->put('active_mitra_id', $memberships->first()->mitra_id);
                return redirect()->route('mitra.dashboard');
            }
            return redirect()->route('mitra.select');
        }

        return redirect()->route($this->map[$s]);
    }

    public function mitras(Request $r)
    {
        $isSuperAdmin = $r->user()->hasGlobalRole(['super-admin', 'admin']);
        $targetSurface = session('target_tenant_surface', 'mitra');

        if ($isSuperAdmin) {
            $allMitras = \App\Models\Mitra::where('status', 'active')
                ->with(['owner', 'region', 'features.serviceType'])
                ->get();

            return view('mitras', [
                'memberships' => collect(),
                'allMitras' => $allMitras,
                'isSuperAdmin' => true,
                'targetSurface' => $targetSurface,
            ]);
        }

        $memberships = $r->user()->mitraMemberships()->with('mitra')->where('status', 'active')->get();

        if ($memberships->count() === 1) {
            $r->session()->put('active_mitra_id', $memberships->first()->mitra_id);

            if ($targetSurface === 'gatekeeper' || ($r->user()->can('access.gatekeeper') && ! $r->user()->can('access.mitra'))) {
                return redirect()->route('gatekeeper.dashboard');
            }

            return redirect()->route('mitra.dashboard');
        }

        return view('mitras', [
            'memberships' => $memberships,
            'allMitras' => collect(),
            'isSuperAdmin' => false,
            'targetSurface' => $targetSurface,
        ]);
    }

    public function chooseMitra(Request $r): RedirectResponse
    {
        $id = $r->validate(['mitra_id' => 'required|exists:mitras,id'])['mitra_id'];
        $isSuperAdmin = $r->user()->hasGlobalRole(['super-admin', 'admin']);
        $targetSurface = $r->input('target_surface', session('target_tenant_surface', 'mitra'));

        if (! $isSuperAdmin) {
            abort_unless($r->user()->mitraMemberships()->where('mitra_id', $id)->where('status', 'active')->exists(), 403);
        }

        $r->session()->put('active_mitra_id', $id);
        $mitra = \App\Models\Mitra::find($id);

        if ($mitra) {
            $action = $isSuperAdmin ? 'superadmin.tenant_impersonated' : 'mitra.tenant_switched';
            app(\App\Services\AuditLogger::class)->record($action, $mitra);
        }

        if ($targetSurface === 'gatekeeper' || (! $isSuperAdmin && $r->user()->can('access.gatekeeper') && ! $r->user()->can('access.mitra'))) {
            return redirect()->route('gatekeeper.dashboard');
        }

        return redirect()->route('mitra.dashboard');
    }

    private function allowed(Request $r): array
    {
        $out = array_values(array_filter(array_keys($this->map), fn ($s) => $r->user()->can('access.'.$s)));
        $tenant = DB::table('model_has_roles as mr')
            ->join('role_has_permissions as rp', 'rp.role_id', '=', 'mr.role_id')
            ->join('permissions as p', 'p.id', '=', 'rp.permission_id')
            ->where('mr.model_id', $r->user()->id)
            ->whereNotNull('mr.mitra_id')
            ->pluck('p.name');

        foreach (['mitra', 'gatekeeper'] as $s) {
            if ($tenant->contains('access.'.$s) && ! in_array($s, $out)) {
                $out[] = $s;
            }
        }

        // Super Admin & Admin secara otomatis memiliki akses Master Switch ke Mitra, Gatekeeper, dan Dinas
        if ($r->user()->hasGlobalRole(['super-admin', 'admin'])) {
            if (! in_array('mitra', $out)) {
                $out[] = 'mitra';
            }
            if (! in_array('gatekeeper', $out)) {
                $out[] = 'gatekeeper';
            }
            if (! in_array('dinas', $out)) {
                $out[] = 'dinas';
            }
        }

        if ($r->user()->hasRole('dinas-supervisor')) {
            if (! in_array('dinas', $out)) {
                $out[] = 'dinas';
            }
        }

        // Setiap pengguna terautentikasi selalu memiliki akses ke Portal Consumer (Wisatawan)
        if (! in_array('consumer', $out)) {
            $out[] = 'consumer';
        }

        return $out;
    }
}
