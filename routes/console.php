<?php

use App\Actions\Checkout\ExpirePendingPayments;
use App\Actions\Payments\SyncPaymentStatus;
use App\Models\Payment;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::call(function () {
    app(ExpirePendingPayments::class)->execute();
})->name('release-expired-orders')->everyMinute()->withoutOverlapping();

Schedule::call(function () {
    if (! config('midtrans.enabled')) return;
    Payment::where('provider', 'midtrans')->whereIn('status', ['pending', 'authorized'])->where('updated_at', '<=', now()->subMinutes(2))->select('id')->chunkById(50, function ($payments) {
        foreach ($payments as $payment) {
            try { app(SyncPaymentStatus::class)->execute($payment, null, 'scheduled'); } catch (Throwable $e) { report($e); }
        }
    });
})->name('reconcile-midtrans-payments')->everyTenMinutes()->withoutOverlapping();
