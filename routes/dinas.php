<?php

use App\Http\Controllers\Dinas\DashboardController;
use App\Http\Controllers\Dinas\DestinationMonitoringController;
use App\Http\Controllers\Dinas\TicketSalesController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'active.user', 'permission:access.dinas'])
    ->prefix('dinas')
    ->name('dinas.')
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/ticket-sales', [TicketSalesController::class, 'index'])->name('ticket-sales.index');
        Route::get('/ticket-sales/export', [TicketSalesController::class, 'export'])->name('ticket-sales.export');
        Route::get('/destinations', [DestinationMonitoringController::class, 'index'])->name('destinations.index');
    });
