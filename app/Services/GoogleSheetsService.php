<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * Sinkronisasi booking ke Google Spreadsheet (1 spreadsheet per tahun, 1 tab per bulan).
 * Memakai token OAuth Google yang sama dengan Google Drive (scope drive).
 */
class GoogleSheetsService
{
    protected const API = 'https://sheets.googleapis.com/v4/spreadsheets';
    protected const DASHBOARD_TAB = 'Dashboard';

    public function __construct(
        protected GoogleDriveService $drive,
        protected BookingReportService $report,
    ) {
    }

    public function isConnected(): bool
    {
        return $this->drive->isConnected();
    }

    /**
     * Sinkron aman: baris yang sudah ada di spreadsheet TIDAK pernah dihapus.
     * Baris dengan ID yang sama diperbarui, ID baru ditambahkan, dan baris yang
     * sudah dihapus dari database tetap dipertahankan di spreadsheet.
     *
     * @return array{url:string, kept:int, total:int} kept = baris yang hanya ada di spreadsheet
     */
    public function syncMonth(int $year, int $month): array
    {
        $token = $this->drive->getValidAccessToken();
        if (! $token) {
            throw new RuntimeException('Akun Google belum terhubung. Hubungkan dulu lewat ' . url('/gdrive/connect'));
        }

        [$id, $url] = $this->ensureSpreadsheet($token, $year);
        $tabs = $this->tabs($token, $id);

        $tab = BookingReportService::monthName($month);
        if (! isset($tabs[$tab])) {
            $this->batch($token, $id, [['addSheet' => ['properties' => ['title' => $tab]]]]);
        }
        if (! isset($tabs[self::DASHBOARD_TAB])) {
            $this->batch($token, $id, [['addSheet' => ['properties' => ['title' => self::DASHBOARD_TAB, 'index' => 0]]]]);
        }

        // Tab bulan: gabungkan data database dengan baris yang sudah ada (tanpa menghapus).
        $fresh = $this->report->tableRows($this->report->bookings($year, $month, true));
        [$merged, $kept] = $this->merge($fresh, $this->readValues($token, $id, $tab));
        $this->writeValues($token, $id, $tab, $merged);

        // Tab dashboard tahunan
        $this->writeValues($token, $id, self::DASHBOARD_TAB, $this->dashboardRows($year));

        return ['url' => $url, 'kept' => $kept, 'total' => count($merged) - 1];
    }

    /** @return array{0:array,1:int} [baris gabungan, jumlah baris yang hanya ada di spreadsheet] */
    protected function merge(array $fresh, array $existing): array
    {
        $header = array_shift($fresh);
        $byId = [];
        foreach ($fresh as $row) {
            $byId[(string) $row[1]] = $row;
        }

        $rows = [];
        $kept = 0;
        foreach (array_slice($existing, 1) as $row) {
            $rid = isset($row[1]) ? (string) $row[1] : '';
            if ($rid === '') {
                continue;
            }
            if (isset($byId[$rid])) {
                $rows[$rid] = $byId[$rid];
                unset($byId[$rid]);
            } else {
                $rows[$rid] = $row; // sudah dihapus dari database, tetap disimpan
                $kept++;
            }
        }
        foreach ($byId as $rid => $row) {
            $rows[$rid] = $row;
        }

        $rows = array_values($rows);
        usort($rows, fn ($a, $b) => [$a[2] ?? '', $a[3] ?? ''] <=> [$b[2] ?? '', $b[3] ?? '']);
        foreach ($rows as $i => &$row) {
            $row[0] = $i + 1;
        }
        unset($row);

        return [array_merge([$header], $rows), $kept];
    }

    protected function readValues(string $token, string $id, string $tab): array
    {
        $range = rawurlencode("'{$tab}'");
        $res = Http::withoutVerifying()->withToken($token)
            ->get(self::API . "/{$id}/values/{$range}", ['valueRenderOption' => 'UNFORMATTED_VALUE']);

        return $res->successful() ? ($res->json('values') ?? []) : [];
    }

    protected function dashboardRows(int $year): array
    {
        $all = $this->report->bookings($year);
        $total = $this->report->summary($all);

        $rows = [["Dashboard Booking {$year}", '', '', ''], ['Bulan', 'Jumlah Pengunjung', 'Jumlah Booking', 'Pendapatan']];
        foreach (BookingReportService::MONTHS as $m => $name) {
            $s = $this->report->summary($all->filter(fn ($b) => (int) \Carbon\Carbon::parse($b->date)->format('n') === $m));
            if ($s['bookings'] || $s['cancelled']) {
                $rows[] = [$name, $s['visitors'], $s['bookings'], (int) $s['revenue']];
            }
        }
        $rows[] = ['TOTAL', $total['visitors'], $total['bookings'], (int) $total['revenue']];
        $rows[] = ['', '', '', ''];
        $rows[] = ['Jumlah Booking per Paket', 'Booking', 'Orang', 'Pendapatan'];
        foreach ($total['packages'] as $p) {
            $rows[] = [$p['name'], $p['bookings'], $p['people'], (int) $p['revenue']];
        }
        $rows[] = ['', '', '', ''];
        $rows[] = ['Top Favorit Background', 'Booking', '', ''];
        foreach (array_slice($total['backgrounds'], 0, 10) as $bg) {
            $rows[] = [$bg['name'], $bg['bookings'], '', ''];
        }

        return $rows;
    }

    /** @return array{0:string,1:string} [id, url] */
    protected function ensureSpreadsheet(string $token, int $year): array
    {
        $key = "booking_spreadsheet_{$year}";
        $row = DB::table('settings')->where('key', $key)->first();

        if ($row) {
            $id = json_decode($row->value, true)['id'] ?? null;
            if ($id && Http::withoutVerifying()->withToken($token)->get(self::API . "/{$id}", ['fields' => 'spreadsheetId'])->successful()) {
                return [$id, "https://docs.google.com/spreadsheets/d/{$id}/edit"];
            }
        }

        $res = Http::withoutVerifying()->withToken($token)->post(self::API, [
            'properties' => ['title' => "E-Statement Booking {$year}"],
            'sheets' => [['properties' => ['title' => self::DASHBOARD_TAB]]],
        ]);
        if (! $res->successful()) {
            throw new RuntimeException('Gagal membuat spreadsheet: ' . $res->body());
        }

        $id = $res->json('spreadsheetId');
        $url = $res->json('spreadsheetUrl') ?: "https://docs.google.com/spreadsheets/d/{$id}/edit";

        // Pindahkan ke folder utama Drive bila sudah dipilih (opsional).
        $folder = DB::table('settings')->where('key', 'google_upload_folder')->first();
        $folderId = $folder ? (json_decode($folder->value, true)['folder_id'] ?? null) : null;
        if ($folderId) {
            Http::withoutVerifying()->withToken($token)
                ->patch("https://www.googleapis.com/drive/v3/files/{$id}?addParents={$folderId}&removeParents=root");
        }

        DB::table('settings')->updateOrInsert(['key' => $key], ['value' => json_encode(['id' => $id])]);

        return [$id, $url];
    }

    /** @return array<string,int> judul tab => sheetId */
    protected function tabs(string $token, string $id): array
    {
        $res = Http::withoutVerifying()->withToken($token)->get(self::API . "/{$id}", ['fields' => 'sheets.properties']);
        if (! $res->successful()) {
            throw new RuntimeException('Gagal membaca spreadsheet: ' . $res->body());
        }

        $tabs = [];
        foreach ($res->json('sheets') ?? [] as $s) {
            $tabs[$s['properties']['title']] = $s['properties']['sheetId'];
        }
        return $tabs;
    }

    protected function batch(string $token, string $id, array $requests): void
    {
        $res = Http::withoutVerifying()->withToken($token)->post(self::API . "/{$id}:batchUpdate", ['requests' => $requests]);
        if (! $res->successful()) {
            throw new RuntimeException('Gagal memperbarui spreadsheet: ' . $res->body());
        }
    }

    protected function writeValues(string $token, string $id, string $tab, array $rows): void
    {
        $range = rawurlencode("'{$tab}'");

        Http::withoutVerifying()->withToken($token)->post(self::API . "/{$id}/values/{$range}:clear", (object) []);

        // Nomor WhatsApp dikirim sebagai teks agar angka 0 di depan tidak hilang.
        $res = Http::withoutVerifying()->withToken($token)
            ->put(self::API . "/{$id}/values/{$range}!A1?valueInputOption=RAW", ['values' => $rows]);

        if (! $res->successful()) {
            throw new RuntimeException('Gagal menulis data ke spreadsheet: ' . $res->body());
        }
    }
}
