<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class GoogleDriveService
{
    protected string $clientId;
    protected string $clientSecret;
    protected string $redirectUri;

    public function __construct()
    {
        $this->clientId = config('services.google_drive.client_id');
        $this->clientSecret = config('services.google_drive.client_secret');
        $this->redirectUri = config('services.google_drive.redirect');
    }

    /**
     * Generate URL untuk redirect user ke Google consent screen
     */
    public function getAuthUrl(): string
    {
        $params = [
            'client_id' => $this->clientId,
            'redirect_uri' => $this->redirectUri,
            'response_type' => 'code',
            'access_type' => 'offline',
            'prompt' => 'consent', // paksa Google selalu kasih refresh_token
            'scope' => 'https://www.googleapis.com/auth/drive',
        ];

        return 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query($params);
    }

    /**
     * Tukar authorization code dari Google jadi access_token + refresh_token,
     * lalu simpan ke tabel settings
     */
    public function handleCallback(string $code): bool
    {
        $response = Http::withoutVerifying()->asForm()->post('https://oauth2.googleapis.com/token', [
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'code' => $code,
            'grant_type' => 'authorization_code',
            'redirect_uri' => $this->redirectUri,
        ]);

        if (!$response->successful()) {
            \Log::error('Gagal tukar code GDrive OAuth', ['body' => $response->body()]);
            return false;
        }

        $data = $response->json();

        // Google kadang tidak kirim ulang refresh_token kalau user sudah pernah consent.
        // Kalau ada token lama, pertahankan refresh_token lama jika yang baru tidak dikirim.
        $existing = $this->getStoredTokens();
        $refreshToken = $data['refresh_token'] ?? ($existing['refresh_token'] ?? null);

        $this->saveTokens([
            'access_token' => $data['access_token'],
            'refresh_token' => $refreshToken,
            'expires_at' => Carbon::now()->addSeconds($data['expires_in'])->toDateTimeString(),
        ]);

        return true;
    }

    /**
     * Ambil access_token yang valid, refresh otomatis kalau sudah expired
     */
    public function getValidAccessToken(): ?string
    {
        $tokens = $this->getStoredTokens();

        if (!$tokens || empty($tokens['access_token'])) {
            return null;
        }

        $expiresAt = isset($tokens['expires_at']) ? Carbon::parse($tokens['expires_at']) : null;

        // Kalau belum expired (kasih buffer 60 detik), langsung pakai
        if ($expiresAt && $expiresAt->subSeconds(60)->isFuture()) {
            return $tokens['access_token'];
        }

        // Kalau expired, coba refresh pakai refresh_token
        if (empty($tokens['refresh_token'])) {
            \Log::warning('GDrive access token expired, tidak ada refresh_token. User perlu connect ulang.');
            return null;
        }

        $response = Http::withoutVerifying()->asForm()->post('https://oauth2.googleapis.com/token', [
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'refresh_token' => $tokens['refresh_token'],
            'grant_type' => 'refresh_token',
        ]);

        if (!$response->successful()) {
            \Log::error('Gagal refresh GDrive token', ['body' => $response->body()]);
            return null;
        }

        $data = $response->json();

        $this->saveTokens([
            'access_token' => $data['access_token'],
            'refresh_token' => $tokens['refresh_token'], // refresh_token biasanya tidak berubah
            'expires_at' => Carbon::now()->addSeconds($data['expires_in'])->toDateTimeString(),
        ]);

        return $data['access_token'];
    }

    /**
     * List folder-folder di Drive (root, atau di dalam parent tertentu)
     */
    public function listFolders(?string $parentId = null): array
    {
        $accessToken = $this->getValidAccessToken();
        if (!$accessToken) return [];

        $q = "mimeType='application/vnd.google-apps.folder' and trashed=false";
        $q .= $parentId ? " and '{$parentId}' in parents" : " and 'root' in parents";

        $response = Http::withoutVerifying()->withToken($accessToken)
            ->get('https://www.googleapis.com/drive/v3/files', [
                'q' => $q,
                'fields' => 'files(id, name)',
                'pageSize' => 100,
            ]);

        if (!$response->successful()) {
            \Log::error('Gagal list folder GDrive', ['body' => $response->body()]);
            return [];
        }

        return $response->json()['files'] ?? [];
    }

    /**
     * Simpan folder utama yang dipilih user sebagai tempat upload
     */
    public function saveMainFolder(string $folderId, string $folderName): void
    {
        DB::table('settings')->updateOrInsert(
            ['key' => 'google_upload_folder'],
            ['value' => json_encode(['folder_id' => $folderId, 'folder_name' => $folderName])] // <--- Sisa ini saja
        );
    }

    protected function getStoredTokens(): ?array
    {
        $row = DB::table('settings')->where('key', 'google_oauth_tokens')->first();
        return $row ? json_decode($row->value, true) : null;
    }

    protected function saveTokens(array $tokens): void
    {
        DB::table('settings')->updateOrInsert(
            ['key' => 'google_oauth_tokens'],
           ['value' => json_encode($tokens)],
        );
    }

    public function isConnected(): bool
    {
        return $this->getValidAccessToken() !== null;
    }
}