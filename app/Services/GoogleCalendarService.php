<?php

namespace App\Services;

use App\Models\GoogleCredential;
use Google_Client;
use Google_Service_Calendar;
use Google_Service_Calendar_Event;

class GoogleCalendarService
{
    protected $client;

    public function __construct()
    {
        $this->client = new Google_Client();
        $this->client->setClientId(config('services.google.client_id'));
        $this->client->setClientSecret(config('services.google.client_secret'));
    }

    // Menyegarkan token jika sudah expired
    public function refreshTokenIfNeeded()
    {
        $credential = GoogleCredential::latest()->first();

        // Cek apakah token sudah expired
        if ($this->isTokenExpired($credential->expires_in)) {
            $newToken = $this->refreshAccessToken($credential->refresh_token);
            $credential->update([
                'access_token' => $newToken['access_token'],
                'expires_in' => $newToken['expires_in'],
            ]);
        }
    }

    // Cek apakah token sudah expired
    private function isTokenExpired($expiresIn)
    {
        return $expiresIn < now()->timestamp;
    }

    // Refresh token
    private function refreshAccessToken($refreshToken)
    {
        $this->client->fetchAccessTokenWithRefreshToken($refreshToken);
        return $this->client->getAccessToken();
    }

    // Menambahkan event ke Google Calendar
    public function createEvent($booking)
    {
        $credential = GoogleCredential::latest()->first();
        $this->client->setAccessToken($credential->access_token);

        if ($this->client->isAccessTokenExpired()) {
            $this->refreshTokenIfNeeded();
            $this->client->setAccessToken($credential->access_token);
        }

        $service = new Google_Service_Calendar($this->client);

        $event = new Google_Service_Calendar_Event([
            'summary' => 'Booking for ' . $booking->name,
            'location' => 'Fotostudio',
            'start' => [
                'dateTime' => $booking->date . 'T' . $booking->time . ':00',
                'timeZone' => 'Asia/Jakarta',
            ],
            'end' => [
                'dateTime' => $booking->date . 'T' . (Carbon\Carbon::parse($booking->time)->addMinutes(30))->format('H:i') . ':00',
                'timeZone' => 'Asia/Jakarta',
            ],
            'attendees' => [
                ['email' => $booking->email],
            ],
        ]);

        $service->events->insert('primary', $event);
    }
}
