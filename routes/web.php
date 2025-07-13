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
Route::get('/api/backgrounds', [BookingController::class, 'getBackgrounds']);
// Ambil hari dan jam operasional dari backend
Route::get('/operational-hours', [OperationalHourController::class, 'getOperationalHours']);
Route::get('/jam-tutup', [OperationalHourController::class, 'closedDays']);
Route::get('/api/time-slots', [OperationalHourController::class, 'getTimeSlots']);
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

Route::get('/debug-storage', function() {
    $info = [
        'storage_path' => storage_path('app/public'),
        'document_root' => $_SERVER['DOCUMENT_ROOT'],
        'storage_exists' => file_exists(storage_path('app/public')),
        'symlink_exists' => file_exists($_SERVER['DOCUMENT_ROOT'] . '/storage'),
        'target_file' => storage_path('app/public/backgrounds/01JZASQ6G119RRGCN3AX2YJMSR.png'),
        'file_exists' => file_exists(storage_path('app/public/backgrounds/01JZASQ6G119RRGCN3AX2YJMSR.png')),
    ];

    return response()->json($info);
});
Route::get('/storage-link', function () {
    $targetFolder = '/home/binamuda/booking-calendar/storage/app/public';
    $linkFolder = '/home/binamuda/public_html/snapfun.onedream.id/storage';

    if (!file_exists($linkFolder)) {
        symlink($targetFolder, $linkFolder);
        return '✅ Symlink berhasil dibuat';
    } else {
        return '⚠️ Symlink sudah ada atau salah path';
    }
});
// ------------------------------
// LOGOUT (khusus untuk admin / user login)
// ------------------------------
Route::post('/logout', function () {
    Auth::logout();
    return redirect('/');
})->name('logout');
