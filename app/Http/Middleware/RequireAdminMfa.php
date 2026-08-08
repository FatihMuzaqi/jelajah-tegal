<?php

namespace App\Http\Middleware;

use Closure;

class RequireAdminMfa
{
    public function handle($r, Closure $next)
    {
        // Fitur MFA sementara dinonaktifkan untuk kemudahan akses
        return $next($r);
    }
}
