<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\GoogleCredential;
use App\Services\GoogleCalendarService;
use Laravel\Socialite\Facades\Socialite;

class GoogleCalendarController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback(Request $request)
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            return redirect()->route('/admin')->with('error', 'Gagal login dengan Google.');
        }

        // Cek apakah user sudah ada
        $user = User::firstOrCreate(
            ['email' => $googleUser->getEmail()],
            [
                'name' => $googleUser->getName(),
                'google_id' => $googleUser->getId(),
                'password' => bcrypt(uniqid()), // kasih random password biar valid
            ]
        );

        auth()->login($user);

        return redirect()->intended(route('filament.admin.pages.dashboard'));
    }
}
