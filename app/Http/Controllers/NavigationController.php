<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class NavigationController extends Controller
{
    private array $map = ['super-admin' => 'super-admin.dashboard', 'admin' => 'admin.dashboard', 'gatekeeper' => 'gatekeeper.dashboard', 'mitra' => 'mitra.select', 'consumer' => 'consumer.dashboard'];

    public function redirect(Request $r): RedirectResponse
    {
        $allowed = $this->allowed($r);
        if (count($allowed) !== 1) {
            return redirect()->route('surfaces.select');
        }

        return redirect()->route($this->map[$allowed[0]]);
    }

    public function surfaces(Request $r): View
    {
        return view('surfaces', ['surfaces' => $this->allowed($r)]);
    }

    public function choose(Request $r): RedirectResponse
    {
        $s = $r->validate(['surface' => 'required|string'])['surface'];
        abort_unless(isset($this->map[$s]) && in_array($s, $this->allowed($r)), 403);

        return redirect()->route($this->map[$s]);
    }

    public function mitras(Request $r)
    {
        $memberships = $r->user()->mitraMemberships()->with('mitra')->where('status', 'active')->get();

        if ($memberships->count() === 1) {
            $r->session()->put('active_mitra_id', $memberships->first()->mitra_id);

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

        return redirect()->route('mitra.dashboard');
    }

    private function allowed(Request $r): array
    {
        $out = array_values(array_filter(array_keys($this->map), fn ($s) => $r->user()->can('access.'.$s)));
        $tenant = DB::table('model_has_roles as mr')->join('role_has_permissions as rp', 'rp.role_id', '=', 'mr.role_id')->join('permissions as p', 'p.id', '=', 'rp.permission_id')->where('mr.model_id', $r->user()->id)->whereNotNull('mr.mitra_id')->pluck('p.name');
        foreach (['mitra', 'gatekeeper'] as $s) {
            if ($tenant->contains('access.'.$s) && ! in_array($s, $out)) {
                $out[] = $s;
            }
        }

        return $out;
    }
}
