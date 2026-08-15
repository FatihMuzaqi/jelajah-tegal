<?php

namespace App\Http\Middleware;

use App\Models\Mitra;
use App\Support\MitraContext;
use Closure;

class ActiveMitraContext
{
    public function __construct(private MitraContext $context) {}

    public function handle($r, Closure $next)
    {
        $m = $r->route('mitra') ?: $r->session()->get('active_mitra_id');
        $id = $m instanceof Mitra ? $m->getKey() : $m;
        $isSuperAdmin = $r->user() && ($r->user()->hasRole('super-admin') || $r->user()->hasRole('admin'));

        if (! $id && $r->user()) {
            if ($isSuperAdmin) {
                $firstMitra = Mitra::where('status', 'active')->first();
                if ($firstMitra) {
                    $id = $firstMitra->id;
                }
            } else {
                $firstMembership = $r->user()->mitraMemberships()->where('status', 'active')->first();
                if ($firstMembership) {
                    $id = $firstMembership->mitra_id;
                }
            }
        }

        if ($isSuperAdmin) {
            abort_unless($id && Mitra::where('id', $id)->where('status', 'active')->exists(), 403);
        } else {
            abort_unless($id && $r->user()->mitraMemberships()->where('mitra_id', $id)->where('status', 'active')->exists(), 403);
        }

        $r->session()->put('active_mitra_id', $id);
        $this->context->activate($id);

        if ($r->user()) {
            $r->user()->unsetRelation('roles')->unsetRelation('permissions');
        }
        try {
            return $next($r);
        } finally {
            $this->context->clear();
        }
    }
}
