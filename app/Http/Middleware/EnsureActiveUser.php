<?php

namespace App\Http\Middleware;

use Closure;

class EnsureActiveUser
{
    public function handle($request, Closure $next)
    {
        abort_if($request->user()?->status !== 'active', 403);

        return $next($request);
    }
}
