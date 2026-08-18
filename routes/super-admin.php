<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SuperAdmin\FeatureFlagController;
use App\Http\Controllers\SuperAdmin\RoleController;
use App\Http\Controllers\SuperAdmin\SettingsController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'active.user', 'permission:access.super-admin', 'admin.mfa'])->prefix('super-admin')->name('super-admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'superAdmin'])->name('dashboard');
    Route::get('/admins', [\App\Http\Controllers\SuperAdmin\AdminManagementController::class, 'index'])->name('admins.index');
    Route::get('/admins/create', [\App\Http\Controllers\SuperAdmin\AdminManagementController::class, 'create'])->name('admins.create');
    Route::post('/admins', [\App\Http\Controllers\SuperAdmin\AdminManagementController::class, 'store'])->name('admins.store');
    Route::get('/admins/{admin}/edit', [\App\Http\Controllers\SuperAdmin\AdminManagementController::class, 'edit'])->name('admins.edit');
    Route::put('/admins/{admin}', [\App\Http\Controllers\SuperAdmin\AdminManagementController::class, 'update'])->name('admins.update');
    Route::patch('/admins/{admin}/toggle-status', [\App\Http\Controllers\SuperAdmin\AdminManagementController::class, 'toggleStatus'])->name('admins.toggle-status');
    Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
    Route::get('/permissions', [RoleController::class, 'permissions'])->name('permissions.index');
    Route::get('/flags', [FeatureFlagController::class, 'index'])->name('flags.index');
    Route::patch('/flags/{flag}', [FeatureFlagController::class, 'toggle'])->name('flags.toggle');
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::get('/chatbot', [\App\Http\Controllers\SuperAdmin\ChatbotSettingController::class, 'index'])->name('chatbot.index');
    Route::post('/chatbot', [\App\Http\Controllers\SuperAdmin\ChatbotSettingController::class, 'update'])->name('chatbot.update');
    Route::post('/chatbot/test-connection', [\App\Http\Controllers\SuperAdmin\ChatbotSettingController::class, 'testConnection'])->name('chatbot.test-connection');
});
