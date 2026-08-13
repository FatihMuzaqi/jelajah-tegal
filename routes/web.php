<?php

use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\MitraActivationController;
use App\Http\Controllers\NavigationController;
use App\Http\Controllers\Public\AccommodationController;
use App\Http\Controllers\Public\CulinaryController;
use App\Http\Controllers\Public\EventController;
use App\Http\Controllers\Public\PublicMitraController;
use App\Http\Controllers\Public\PublicPortalController;
use App\Http\Controllers\Public\RentalController;
use App\Http\Controllers\Public\TourismController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PublicPortalController::class, 'home'])->name('home');
Route::get('/tentang', [PublicPortalController::class, 'about'])->name('public.about');
Route::get('/faq', [PublicPortalController::class, 'faq'])->name('public.faq');
Route::get('/kontak', [PublicPortalController::class, 'contact'])->name('public.contact');
Route::get('/kebijakan-privasi', [PublicPortalController::class, 'privacy'])->name('public.privacy');
Route::get('/syarat-ketentuan', [PublicPortalController::class, 'terms'])->name('public.terms');
Route::get('/mitra/activation/{token}', [MitraActivationController::class, 'show'])->name('mitra.activation.show');
Route::post('/mitra/activation/{token}', [MitraActivationController::class, 'store'])->name('mitra.activation.store')->middleware('throttle:6,1');
Route::get('/mitra-profil/{slug}', [PublicMitraController::class, 'show'])->name('public.mitra.show');

// Smart AI Chatbot Assistant Endpoint
Route::post('/chatbot/message', [ChatbotController::class, 'message'])->name('chatbot.message')->middleware('throttle:30,1');

Route::get('/wisata', [TourismController::class, 'index'])->name('tourism.index');
Route::get('/wisata/{slug}', [TourismController::class, 'show'])->name('tourism.show');
Route::get('/penginapan', [AccommodationController::class, 'index'])->name('accommodation.index');
Route::get('/penginapan/{slug}', [AccommodationController::class, 'show'])->name('accommodation.show');
Route::get('/penginapan/{slug}/kamar/{room}', [AccommodationController::class, 'room'])->name('accommodation.rooms.show');
Route::get('/kuliner', [CulinaryController::class, 'index'])->name('culinary.index');
Route::get('/kuliner/{slug}', [CulinaryController::class, 'show'])->name('culinary.show');
Route::get('/event', [EventController::class, 'index'])->name('event.index');
Route::get('/event/{slug}', [EventController::class, 'show'])->name('event.show');
Route::get('/rental', [RentalController::class, 'index'])->name('rental.index');
Route::get('/rental/{slug}', [RentalController::class, 'show'])->name('rental.show');
Route::middleware(['auth', 'verified', 'active.user'])->group(function () {
    Route::post('/wisata/{slug}/favorite', [TourismController::class, 'favorite'])->name('tourism.favorite');
    Route::delete('/wisata/{slug}/favorite', [TourismController::class, 'unfavorite'])->name('tourism.unfavorite');
    Route::post('/wisata/{slug}/reviews', [TourismController::class, 'review'])->name('tourism.reviews.store');
    Route::post('/penginapan/{slug}/favorite', [AccommodationController::class, 'favorite'])->name('accommodation.favorite');
    Route::delete('/penginapan/{slug}/favorite', [AccommodationController::class, 'unfavorite'])->name('accommodation.unfavorite');
    Route::post('/penginapan/{slug}/reviews', [AccommodationController::class, 'review'])->name('accommodation.reviews.store');
    Route::post('/kuliner/{slug}/favorite', [CulinaryController::class, 'favorite'])->name('culinary.favorite');
    Route::delete('/kuliner/{slug}/favorite', [CulinaryController::class, 'unfavorite'])->name('culinary.unfavorite');
    Route::post('/kuliner/{slug}/reviews', [CulinaryController::class, 'review'])->name('culinary.reviews.store');
    Route::post('/kuliner/{slug}/slots/{slot}/reserve', [CulinaryController::class, 'reserve'])->name('culinary.reserve');
    Route::post('/event/{slug}/favorite', [EventController::class, 'favorite'])->name('event.favorite');
    Route::delete('/event/{slug}/favorite', [EventController::class, 'unfavorite'])->name('event.unfavorite');
    Route::post('/event/{slug}/reviews', [EventController::class, 'review'])->name('event.reviews.store');
    Route::post('/rental/{slug}/favorite', [RentalController::class, 'favorite'])->name('rental.favorite');
    Route::delete('/rental/{slug}/favorite', [RentalController::class, 'unfavorite'])->name('rental.unfavorite');
    Route::post('/rental/{slug}/reviews', [RentalController::class, 'review'])->name('rental.reviews.store');
    Route::post('/rental/{slug}/book', [RentalController::class, 'book'])->name('rental.book');
});
require __DIR__.'/auth.php';
Route::middleware(['auth', 'verified', 'active.user'])->group(function () {
    Route::get('/post-login', [NavigationController::class, 'redirect'])->name('post-login');
    Route::get('/surfaces', [NavigationController::class, 'surfaces'])->name('surfaces.select');
    Route::post('/surfaces', [NavigationController::class, 'choose'])->name('surfaces.choose');
    Route::get('/select-mitra', [NavigationController::class, 'mitras'])->name('mitra.select');
    Route::post('/select-mitra', [NavigationController::class, 'chooseMitra'])->name('mitra.choose');
});
foreach (['consumer', 'mitra', 'gatekeeper', 'admin', 'super-admin'] as $routes) {
    require __DIR__.'/'.$routes.'.php';
}
