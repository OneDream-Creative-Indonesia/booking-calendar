<?php

use App\Http\Livewire\BookingWizard;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\SocialiteController;
use App\Http\Controllers\GoogleCalendarController;
use App\Http\Controllers\OperationalHourController;
// --- Untuk booking user biasa ---
Route::get('/booking', [BookingController::class, 'showCalendar'])->name('booking.calendar');

Route::get('/auth/{provider}/redirect', [SocialiteController::class, 'redirect'])
    ->name('socialite.redirect');
Route::get('/auth/{provider}/callback', [SocialiteController::class, 'callback'])
    ->name('socialite.callback');
// --- Untuk Admin connect ke Google Calendar ---
Route::get('/admin/google/connect', [GoogleCalendarController::class, 'redirectToGoogle'])->name('google.connect');
Route::get('/google/callback', [GoogleCalendarController::class, 'handleGoogleCallback'])->name('google.callback');
Route::get('/operational-hours', [OperationalHourController::class, 'getOperationalHours']);
Route::get('/jam-tutup', [OperationalHourController::class, 'closedDays']);
// --- Welcome ---
Route::redirect('/', '/booking', 301);
Route::post('/logout', function () {
    Auth::logout();
    return redirect('/');
})->name('logout');
