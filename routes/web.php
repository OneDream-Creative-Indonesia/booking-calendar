<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Models\Project;
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
    FrameSettingController,
    AdminFrameController,
};
use Illuminate\Support\Facades\Artisan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
// =====================================================
// 🧍 ROUTES: PUBLIC BOOKING PAGE
// =====================================================
Route::redirect('/', '/booking', 301);
Route::get('/booking', [BookingController::class, 'home']); // Custom frontend booking view
Route::get('/grid', function() {
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

//photobooth
Route::get('/photobooth', \App\Livewire\TicketingForms::class)->name('ticketing-forms');

//edit grid
Route::get('/edit', function() {
     return view('edit');
    });

//sound grid
Route::get('/antrian', function() {
     return view('antrian');
    });
// =====================================================
// 🔊 ROUTES: API SISTEM ANTREAN
// =====================================================
Route::get('/api/antrian/get_queue', function() {
    try {
        // Ambil semua data dari tabel ticketings menggunakan DB Facade bawaan Laravel
        $queue = DB::table('ticketings')->orderBy('id', 'asc')->get();
        return response()->json(['success' => true, 'data' => $queue, 'history' => []]);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()]);
    }
});

Route::post('/api/antrian/update_status', function(Request $request) {
    $id = $request->input('id');
    if ($id) {
        try {
            // HAPUS DATA: Hilangkan data dari database setelah berhasil dipanggil
            DB::table('ticketings')->where('id', $id)->delete();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    return response()->json(['success' => false, 'message' => 'ID tidak ditemukan']);
});
// Rute untuk menampilkan data JSON frame ke Editor
Route::get('/api/get-frames', [FrameSettingController::class, 'index']);
Route::get('/admin/frame-manager', function() {
    return view('admin_frame');
})->middleware('auth'); 

Route::get('/api/admin/get-frames', [\App\Http\Controllers\AdminFrameController::class, 'getFrames']);
Route::post('/api/admin/create-folder', [\App\Http\Controllers\AdminFrameController::class, 'createFolder']);
Route::delete('/api/admin/delete-folder/{id}', [\App\Http\Controllers\AdminFrameController::class, 'deleteFolder']);
Route::post('/api/admin/save-frame', [\App\Http\Controllers\AdminFrameController::class, 'saveFrame']);
Route::get('/', function (\Illuminate\Http\Request $request) {
    if ($request->has('album')) {
        $project = \App\Models\Project::where('project_code', $request->query('album'))->firstOrFail();
        return view('project-gallery', [
            'project' => $project
        ]);
    }
    
    return view('welcome');
})->middleware('auth'); 