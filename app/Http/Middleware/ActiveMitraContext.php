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
        abort_unless($id && $r->user()->mitraMemberships()->where('mitra_id', $id)->where('status', 'active')->exists(), 403);
        $r->session()->put('active_mitra_id', $id);
        $this->context->activate($id);
        try {
            return $next($r);
        } finally {
            $this->context->clear();
        }
    }
}
