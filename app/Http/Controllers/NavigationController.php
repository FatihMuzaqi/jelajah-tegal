<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class NavigationController extends Controller
{
    private array $map = ['super-admin' => 'super-admin.dashboard', 'admin' => 'admin.dashboard', 'gatekeeper' => 'mitra.select', 'mitra' => 'mitra.select', 'consumer' => 'consumer.dashboard'];

    public function redirect(Request $r): RedirectResponse
    {
        $allowed = $this->allowed($r);

        // 1. Cek apakah ada URL tujuan sebelum login (misal user sedang membuka halaman detail lalu login)
        $intended = session()->pull('url.intended');
        if ($intended && ! str_contains($intended, 'post-login') && ! str_contains($intended, 'login')) {
            return redirect()->to($intended);
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

        if ($s === 'gatekeeper') {
            $memberships = $r->user()->mitraMemberships()->with('mitra')->where('status', 'active')->get();
            if ($memberships->count() === 1) {
                $r->session()->put('active_mitra_id', $memberships->first()->mitra_id);
                return redirect()->route('gatekeeper.dashboard');
            }
            return redirect()->route('mitra.select');
        }

        if ($s === 'mitra') {
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
        $memberships = $r->user()->mitraMemberships()->with('mitra')->where('status', 'active')->get();

        if ($memberships->count() === 1) {
            $r->session()->put('active_mitra_id', $memberships->first()->mitra_id);

            if ($r->user()->can('access.gatekeeper') && ! $r->user()->can('access.mitra')) {
                return redirect()->route('gatekeeper.dashboard');
            }

            return redirect()->route('mitra.dashboard');
        }

        return view('mitras', compact('memberships'));
    }

    public function chooseMitra(Request $r): RedirectResponse
    {
        $id = $r->validate(['mitra_id' => 'required'])['mitra_id'];
        abort_unless($r->user()->mitraMemberships()->where('mitra_id', $id)->where('status', 'active')->exists(), 403);

        $r->session()->put('active_mitra_id', $id);
        $mitra = \App\Models\Mitra::find($id);
        if ($mitra) {
            app(\App\Services\AuditLogger::class)->record('mitra.tenant_switched', $mitra);
        }

        if ($r->user()->can('access.gatekeeper') && ! $r->user()->can('access.mitra')) {
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

        // Setiap pengguna terautentikasi selalu memiliki akses ke Portal Consumer (Wisatawan)
        if (! in_array('consumer', $out)) {
            $out[] = 'consumer';
        }

        return $out;
    }
}
