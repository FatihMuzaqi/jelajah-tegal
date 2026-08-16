<?php

namespace App\Providers;

use App\Support\MitraContext;
use App\Services\Payments\MidtransConfiguration;
use LogicException;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->scoped(MitraContext::class, fn() => new MitraContext);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Illuminate\Support\Facades\Gate::before(function ($user, $ability) {
            return $user->hasRole('super-admin') ? true : null;
        });

        if (config('midtrans.enabled')) {
            try {
                app(MidtransConfiguration::class)->assertReady();
            } catch (LogicException $exception) {
                throw new LogicException('Midtrans diaktifkan tetapi konfigurasi invalid. ' . $exception->getMessage(), previous: $exception);
            }
        }
    }
}
