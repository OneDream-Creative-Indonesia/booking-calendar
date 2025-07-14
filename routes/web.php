<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\{
    BookingController,
    VoucherController,
    PackageController,
    SocialiteController,
    GoogleCalendarController,
    OperationalHourController
};
use Illuminate\Support\Facades\Artisan;

// =====================================================
// 🧍 ROUTES: PUBLIC BOOKING PAGE
// =====================================================
Route::redirect('/', '/booking', 301);
Route::get('/booking', [BookingController::class, 'home']); // Custom frontend booking view

// Paket & Booking
Route::get('/packages', [PackageController::class, 'index']);
Route::post('/api/submit-booking', [BookingController::class, 'submitBooking']);
Route::get('/booked-times/{date}', [BookingController::class, 'getBookedTimes']);

// Voucher
Route::prefix('voucher')->group(function () {
    Route::get('/get', [VoucherController::class, 'getVoucher']);
    Route::get('/check', [VoucherController::class, 'checkVoucher']);
});

// Background
Route::get('/api/backgrounds', [BookingController::class, 'getBackgrounds']);

// Operasional
Route::prefix('operational')->group(function () {
    Route::get('/hours', [OperationalHourController::class, 'getOperationalHours']);
    Route::get('/close-time', [OperationalHourController::class, 'closedDays']);
    Route::get('/time-slots', [OperationalHourController::class, 'getTimeSlots']);
});

// =====================================================
// 🔐 ROUTES: SOCIALITE AUTHENTICATION
// =====================================================
Route::prefix('auth/{provider}')->group(function () {
    Route::get('/redirect', [SocialiteController::class, 'redirect'])->name('socialite.redirect');
    Route::get('/callback', [SocialiteController::class, 'callback'])->name('socialite.callback');
});

// =====================================================
// 🛠 ROUTES: ADMIN / SYNC GOOGLE CALENDAR
// =====================================================
Route::prefix('admin/google')->group(function () {
    Route::get('/connect', [GoogleCalendarController::class, 'redirectToGoogle'])->name('google.connect');
    Route::get('/callback', [GoogleCalendarController::class, 'handleGoogleCallback'])->name('google.callback');
});

//optimize filament

Route::get('/filament-optimize-clear', function () {
    Artisan::call('filament:optimize-clear');
    return '✅ Filament cache cleared.';
});
// =====================================================
// 🚪 ROUTES: LOGOUT
// =====================================================
Route::post('/logout', function () {
    Auth::logout();
    return redirect('/');
})->name('logout');
