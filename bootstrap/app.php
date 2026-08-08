<?php

use App\Http\Middleware\ActiveMitraContext;
use App\Http\Middleware\EnsureActiveUser;
use App\Http\Middleware\RequireAdminMfa;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Spatie\Permission\Middleware\PermissionMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(web: __DIR__.'/../routes/web.php', api: __DIR__.'/../routes/api.php', commands: __DIR__.'/../routes/console.php', health: '/up')
    ->withMiddleware(function (Middleware $m) {
        $m->alias(['active.user' => EnsureActiveUser::class, 'active.mitra' => ActiveMitraContext::class, 'admin.mfa' => RequireAdminMfa::class, 'permission' => PermissionMiddleware::class]);
    })
    ->withExceptions(function (Exceptions $e) {})->create();
