<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Gatekeeper\EventTicketController;
use App\Http\Controllers\Gatekeeper\TicketController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'active.user', 'active.mitra', 'permission:access.gatekeeper'])->prefix('gatekeeper')->name('gatekeeper.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'gatekeeper'])->name('dashboard');
    Route::get('/{mitra}/dashboard', [DashboardController::class, 'gatekeeper'])->name('context.dashboard');
    Route::get('/event-tickets', [EventTicketController::class, 'index'])->name('event-tickets.index');
    Route::post('/event-tickets/validate', [EventTicketController::class, 'validateTicket'])->name('event-tickets.validate');
    Route::post('/tickets/validate', [TicketController::class, 'validateTicket'])->name('tickets.validate');
    Route::get('/profile', [\App\Http\Controllers\Gatekeeper\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile/password', [\App\Http\Controllers\Gatekeeper\ProfileController::class, 'updatePassword'])->name('profile.password.update');
});
