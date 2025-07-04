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

// ------------------------------
// ROUTE UNTUK USER BOOKING PAGE
// ------------------------------
Route::get('/', fn () => redirect('/booking', 301)); // Redirect root ke halaman booking
// Route::get('/booking', [BookingController::class, 'showCalendar'])->name('booking.calendar');
Route::get('/booking', [BookingController::class, 'home']); // versi tampilan frontend custom

// API untuk mendapatkan data paket dan mengirim data booking
Route::get('/packages', [PackageController::class, 'index']);
Route::post('/api/submit-booking', [BookingController::class, 'submitBooking']);

// Ambil waktu yang sudah dibooking pada tanggal tertentu
Route::get('/booked-times/{date}', [BookingController::class, 'getBookedTimes']);

//untuk ambil dan code voucher
Route::get('/get-voucher', [VoucherController::class, 'getVoucher']);
Route::get('/check-voucher', [VoucherController::class, 'checkVoucher']);

//ambil background dari backend
Route::get('/backgrounds', [BookingController::class, 'getBackgrounds']);
// Ambil hari dan jam operasional dari backend
Route::get('/operational-hours', [OperationalHourController::class, 'getOperationalHours']);
Route::get('/jam-tutup', [OperationalHourController::class, 'closedDays']);

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

// ------------------------------
// LOGOUT (khusus untuk admin / user login)
// ------------------------------
Route::post('/logout', function () {
    Auth::logout();
    return redirect('/');
})->name('logout');
