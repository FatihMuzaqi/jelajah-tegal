<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SuperAdmin\FeatureFlagController;
use App\Http\Controllers\SuperAdmin\RoleController;
use App\Http\Controllers\SuperAdmin\SettingsController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'active.user', 'permission:access.super-admin', 'admin.mfa'])->prefix('super-admin')->name('super-admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'superAdmin'])->name('dashboard');
    Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
    Route::get('/permissions', [RoleController::class, 'permissions'])->name('permissions.index');
    Route::get('/flags', [FeatureFlagController::class, 'index'])->name('flags.index');
    Route::patch('/flags/{flag}', [FeatureFlagController::class, 'toggle'])->name('flags.toggle');
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
});
