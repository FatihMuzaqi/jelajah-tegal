<?php

use App\Http\Controllers\Admin\AccommodationModerationController;
use App\Http\Controllers\Admin\CulinaryModerationController;
use App\Http\Controllers\Admin\EventModerationController;
use App\Http\Controllers\Admin\FeatureRequestController;
use App\Http\Controllers\Admin\KycReviewController;
use App\Http\Controllers\Admin\MitraController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\RentalModerationController;
use App\Http\Controllers\Admin\TourismModerationController;
use App\Http\Controllers\Admin\VoucherController;
use App\Http\Controllers\Admin\WithdrawalController;
use App\Http\Controllers\Admin\BankAccountVerificationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Mitra\KycController;
use App\Http\Controllers\Admin\AuditController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'active.user', 'permission:access.admin', 'admin.mfa'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'admin'])->name('dashboard');
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/audit', [AuditController::class, 'index'])->name('audit.index');
    Route::get('/mitras', [MitraController::class, 'index'])->name('mitras.index');
    Route::get('/mitras/create', [MitraController::class, 'create'])->name('mitras.create');
    Route::post('/mitras', [MitraController::class, 'store'])->name('mitras.store');
    Route::patch('/mitras/{mitra}/status', [MitraController::class, 'status'])->name('mitras.status');
    Route::get('/kyc', [KycReviewController::class, 'index'])->name('kyc.index');
    Route::patch('/kyc/{document}', [KycReviewController::class, 'update'])->name('kyc.update');
    Route::get('/kyc/{document}/download', [KycController::class, 'download'])->name('kyc.download');
    Route::get('/feature-requests', [FeatureRequestController::class, 'index'])->name('features.index');
    Route::patch('/feature-requests/{featureRequest}', [FeatureRequestController::class, 'update'])->name('features.update');
    Route::get('/tourism', [TourismModerationController::class, 'index'])->name('tourism.index');
    Route::get('/tourism/{tourism}', [TourismModerationController::class, 'show'])->name('tourism.show');
    Route::patch('/tourism/{tourism}', [TourismModerationController::class, 'update'])->name('tourism.update');
    Route::patch('/tourism-reviews/{review}', [TourismModerationController::class, 'review'])->name('tourism.reviews.update');
    Route::get('/accommodation', [AccommodationModerationController::class, 'index'])->name('accommodation.index');
    Route::get('/accommodation/{accommodation}', [AccommodationModerationController::class, 'show'])->name('accommodation.show');
    Route::patch('/accommodation/{accommodation}', [AccommodationModerationController::class, 'update'])->name('accommodation.update');
    Route::patch('/accommodation-reviews/{review}', [AccommodationModerationController::class, 'review'])->name('accommodation.reviews.update');
    foreach ([
        'culinary' => CulinaryModerationController::class,
        'event' => EventModerationController::class,
        'rental' => RentalModerationController::class,
    ] as $domain => $controller) {
        Route::get('/'.$domain, [$controller, 'index'])->name($domain.'.index');
        Route::get('/'.$domain.'/{item}', [$controller, 'show'])->name($domain.'.show');
        Route::patch('/'.$domain.'/{item}', [$controller, 'update'])->name($domain.'.update');
        Route::patch('/'.$domain.'-reviews/{review}', [$controller, 'review'])->name($domain.'.reviews.update');
    }
    Route::get('/vouchers', [VoucherController::class, 'index'])->name('vouchers.index');
    Route::get('/vouchers/create', [VoucherController::class, 'create'])->name('vouchers.create');
    Route::post('/vouchers', [VoucherController::class, 'store'])->name('vouchers.store');
    Route::patch('/vouchers/{voucher}/status', [VoucherController::class, 'status'])->name('vouchers.status');
    Route::post('/payments/{payment}/capture', [PaymentController::class, 'capture'])->name('payments.capture');
    Route::post('/payments/{payment}/sync', [PaymentController::class, 'sync'])->name('payments.sync');
    Route::get('/withdrawals', [WithdrawalController::class, 'index'])->name('withdrawals.index');
    Route::get('/withdrawals/{withdrawal}', [WithdrawalController::class, 'show'])->name('withdrawals.show');
    Route::patch('/withdrawals/{withdrawal}', [WithdrawalController::class, 'transition'])->name('withdrawals.transition');
    Route::patch('/bank-accounts/{account}/verification', [BankAccountVerificationController::class, 'update'])->name('bank-accounts.verification');
});
