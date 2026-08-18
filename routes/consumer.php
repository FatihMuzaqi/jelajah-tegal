<?php

use App\Http\Controllers\Consumer\CheckoutController;
use App\Http\Controllers\Consumer\RenterDocumentController;
use App\Http\Controllers\Consumer\VoucherController;
use App\Http\Controllers\Consumer\PaymentController;
use App\Http\Controllers\Consumer\TicketController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'active.user'])->prefix('consumer')->name('consumer.')->group(function () {
    Route::get('/', fn () => redirect()->route('consumer.dashboard'));
    Route::get('/dashboard', [DashboardController::class, 'consumer'])->name('dashboard');
    Route::get('/rute-destinasi', [\App\Http\Controllers\Consumer\TripNavigatorController::class, 'index'])->name('trip-navigator.index');
    Route::get('/renter-documents', [RenterDocumentController::class, 'index'])->name('renter-documents.index');
    Route::post('/renter-documents', [RenterDocumentController::class, 'store'])->name('renter-documents.store');
    Route::get('/renter-documents/{document}/download', [RenterDocumentController::class, 'download'])->name('renter-documents.download');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store')->middleware('throttle:30,1');
    Route::get('/orders', [CheckoutController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [CheckoutController::class, 'show'])->name('orders.show');
    Route::post('/orders/{order}/payment/snap', [PaymentController::class, 'snap'])->name('orders.payment.snap');
    Route::post('/orders/{order}/confirm-direct', [CheckoutController::class, 'confirmDirect'])->name('orders.confirm-direct');
    Route::get('/tickets/{ticket}/qr', [TicketController::class, 'qr'])->name('tickets.qr');
    Route::get('/invoices/{invoice}', [\App\Http\Controllers\Public\TourAssistantCheckoutController::class, 'showInvoice'])->name('invoices.show');
    Route::post('/invoices/{invoice}/payment/snap', [\App\Http\Controllers\Public\TourAssistantCheckoutController::class, 'snap'])->name('invoices.payment.snap');
    Route::post('/invoices/{invoice}/confirm-direct', [\App\Http\Controllers\Public\TourAssistantCheckoutController::class, 'confirmDirect'])->name('invoices.confirm-direct');
    Route::post('/vouchers/claim', [VoucherController::class, 'claim'])->name('vouchers.claim');
});
