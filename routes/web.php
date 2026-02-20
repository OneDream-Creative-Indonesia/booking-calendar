<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\{
    BookingController,
    VoucherController,
    PackageController,
    SocialiteController,
    GoogleCalendarController,
    OperationalHourController,
    BookingExportController,
    PhotoGridController,
    KeychainController,
    PhotoOrderController,
    FrameController,
    TickettingReport,
};
use Illuminate\Support\Facades\Artisan;

// =====================================================
// 🧍 ROUTES: PUBLIC BOOKING PAGE
// =====================================================
Route::redirect('/', '/booking', 301);
Route::get('/booking', [BookingController::class, 'home']); // Custom frontend booking view
Route::get('/qrcode', function() {
     return view('qrcode');
    });

route::get('/photo-grids', [PhotoGridController::class, 'index']);
route::get('/keychains', [KeychainController::class, 'index']);
route::post('/photo-orders', [PhotoOrderController::class, 'store']);
// Paket & Booking
Route::get('/packages', [PackageController::class, 'index']);
Route::post('/api/submit-booking', [BookingController::class, 'submitBooking']);
Route::get('/booked-times/{date}', [BookingController::class, 'getBookedTimes']);

// Voucher

Route::get('/get-voucher', [VoucherController::class, 'getVoucher']);
Route::get('/check-voucher', [VoucherController::class, 'checkVoucher']);

// Background
Route::get('/api/backgrounds', [BookingController::class, 'getBackgrounds']);

// Operasional
Route::get('/operational-hours', [OperationalHourController::class, 'getOperationalHours']);
Route::get('/jam-tutup', [OperationalHourController::class, 'closedDays']);
Route::get('/api/time-slots', [OperationalHourController::class, 'getTimeSlots']);
Route::get('/blocked-dates', [OperationalHourController::class, 'blockedTimes']);
// ------------------------------
// ROUTE UNTUK AUTH SOCIALITE
// ------------------------------
Route::get('/auth/{provider}/redirect', [SocialiteController::class, 'redirect'])->name('socialite.redirect');
Route::get('/auth/{provider}/callback', [SocialiteController::class, 'callback'])->name('socialite.callback');

// ------------------------------
// ROUTE UNTUK ADMIN / GOOGLE CALENDAR SYNC
// ------------------------------
Route::get('/admin/google/connect', [GoogleCalendarController::class, 'redirectToGoogle'])->name('google.connect');
Route::get('/google/callback', [GoogleCalendarController::class, 'handleGoogleCallback'])->name('google.callback');


// =====================================================
// Export Booking Csv
// =====================================================
Route::get('/export-bookings', [BookingExportController::class, 'exportCsv'])->name('export.bookings');
// =====================================================
// 🚪 ROUTES: LOGOUT
// =====================================================
Route::post('/logout', function () {
    Auth::logout();
    return redirect('/');
})->name('logout');

//Frame
Route::get('/api/frames', [FrameController::class, 'index']);
//ticketing
Route::get('/ticketing/export', [TickettingReport::class, 'exportCsv'])->name('ticketings_reports.export');
