<?php

use App\Http\Livewire\BookingWizard;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\SocialiteController;
use App\Http\Controllers\GoogleCalendarController;

// --- Untuk booking user biasa ---
Route::get('/booking', [BookingController::class, 'showCalendar'])->name('booking.calendar');

Route::get('/auth/{provider}/redirect', [SocialiteController::class, 'redirect'])
    ->name('socialite.redirect');
Route::get('/auth/{provider}/callback', [SocialiteController::class, 'callback'])
    ->name('socialite.callback');
// --- Untuk Admin connect ke Google Calendar ---
Route::get('/admin/google/connect', [GoogleCalendarController::class, 'redirectToGoogle'])->name('google.connect');
Route::get('/google/callback', [GoogleCalendarController::class, 'handleGoogleCallback'])->name('google.callback');

// --- Welcome ---
Route::get('/', function () {
    return view('welcome');
});
