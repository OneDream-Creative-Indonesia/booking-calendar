<?php

// namespace App\Services;

// use Carbon\Carbon;
// use Google_Client;
// use Google_Service_Calendar;
// use Google_Service_Calendar_Event;
// use App\Models\GoogleCredential;

// class GoogleCalendarService
// {
//     protected $client;

//     public function __construct()
//     {
//         $this->client = new Google_Client();
//         $this->client->setClientId(env('GOOGLE_CLIENT_ID'));
//         $this->client->setClientSecret(env('GOOGLE_CLIENT_SECRET'));
//         $this->client->setRedirectUri(env('GOOGLE_REDIRECT_URI'));
//         $this->client->addScope(Google_Service_Calendar::CALENDAR);

//         $this->authenticate();
//     }

//     protected function authenticate()
//     {
//         $credential = GoogleCredential::first();

//         if ($credential) {
//             $this->client->setAccessToken([
//                 'access_token' => $credential->access_token,
//                 'refresh_token' => $credential->refresh_token,
//                 'expires_in' => $credential->expires_in,
//                 'token_type' => $credential->token_type,
//                 'created' => strtotime($credential->created_at),
//             ]);

//             if ($this->client->isAccessTokenExpired()) {
//                 $newToken = $this->client->fetchAccessTokenWithRefreshToken($credential->refresh_token);

//                 $credential->update([
//                     'access_token' => $newToken['access_token'],
//                     'expires_in' => $newToken['expires_in'],
//                     'token_type' => $newToken['token_type'],
//                 ]);
//             }
//         }
//     }
//     public function getClient()
//     {
//         return $this->client;
//     }

//     public function refreshTokenIfNeeded()
//     {
//         if ($this->client->isAccessTokenExpired()) {
//             $credential = GoogleCredential::first();
//             if ($credential && $credential->refresh_token) {
//                 $newToken = $this->client->fetchAccessTokenWithRefreshToken($credential->refresh_token);

//                 $credential->update([
//                     'access_token' => $newToken['access_token'],
//                     'expires_in' => $newToken['expires_in'],
//                     'token_type' => $newToken['token_type'],
//                 ]);

//                 $this->client->setAccessToken($newToken);
//             }
//         }
//     }
//     public function createEvent($booking)
//     {
//         $calendarService = new Google_Service_Calendar($this->client);

//         if (empty($booking->date) || empty($booking->time)) {
//             throw new \Exception('Date and time are required.');
//         }

//         try {
//             $startTime = Carbon::createFromFormat('Y-m-d H:i', $booking->date . ' ' . $booking->time);
//         } catch (\Exception $e) {
//             throw new \Exception('Invalid date or time format. Expected format: Y-m-d and H:i');
//         }

//         $endTime = $startTime->copy()->addHour();

//         $event = new Google_Service_Calendar_Event([
//             'summary' => 'Booking: ' . $booking->name,
//             'description' => 'Booking details: ' . $booking->package,
//             'start' => [
//                 'dateTime' => $startTime->toIso8601String(),
//                 'timeZone' => 'Asia/Jakarta',
//             ],
//             'end' => [
//                 'dateTime' => $endTime->toIso8601String(),
//                 'timeZone' => 'Asia/Jakarta',
//             ],
//             'attendees' => [
//                 ['email' => $booking->email],
//             ],
//         ]);

//         $calendarService->events->insert('primary', $event);
//     }

// }
