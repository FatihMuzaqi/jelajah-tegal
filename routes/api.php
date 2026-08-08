<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MidtransWebhookController;

Route::get('/v1/status', fn () => ['status' => 'ok', 'service' => 'lokantara']);
Route::post('/v1/payments/midtrans/webhook', MidtransWebhookController::class)->middleware('throttle:120,1')->name('api.midtrans.webhook');
