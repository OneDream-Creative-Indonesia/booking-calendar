<?php

namespace App\Services;

use App\Models\Booking;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class BookingReportService
{
    public const MONTHS = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
        7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
    ];

    public static function monthName(int $month): string
    {
        return self::MONTHS[$month] ?? (string) $month;
    }

    public function query(int $year, ?int $month = null, bool $statementOnly = false): Builder
    {
        return Booking::query()
            ->whereYear('date', $year)
            ->when($month, fn ($q) => $q->whereMonth('date', $month))
            ->when($statementOnly, fn ($q) => $q->whereNull('statement_hidden_at'));
    }

    /** $statementOnly = true: hanya booking yang tidak disembunyikan dari E-Statement. */
    public function bookings(int $year, ?int $month = null, bool $statementOnly = false): Collection
    {
        return $this->query($year, $month, $statementOnly)
            ->with(['package', 'background', 'voucher', 'bookingAddons.addon'])
            ->orderBy('date')->orderBy('time')
            ->get();
    }

    public static function addonTotal(Booking $b): float
    {
        return (float) $b->bookingAddons->sum('subtotal');
    }

    public static function total(Booking $b): float
    {
        return (float) $b->price + self::addonTotal($b);
    }

    /** Header + baris lengkap untuk Excel / Google Sheets. */
    public function tableRows(Collection $bookings): array
    {
        $rows = [[
            'No', 'ID Booking', 'Tanggal', 'Waktu', 'Nama', 'WhatsApp', 'Email', 'Jenis Booking (Paket)',
            'Background', 'Jumlah Orang', 'Kode Voucher', 'Item Add-on', 'Catatan Keychain',
            'Harga Paket', 'Harga Add-on', 'Total Harga', 'DP Paket',
            'Status', 'Konfirmasi', 'Dibuat Pada',
        ]];

        foreach ($bookings->values() as $i => $b) {
            $addons = $b->bookingAddons
                ->map(fn ($a) => ($a->addon?->name ?? 'Add-on') . ' x' . $a->qty)
                ->implode(', ');

            $rows[] = [
                $i + 1,
                $b->id,
                (string) \Carbon\Carbon::parse($b->date)->format('Y-m-d'),
                substr((string) $b->time, 0, 5),
                $b->name,
                (string) $b->whatsapp,
                $b->email,
                $b->package?->title ?? '-',
                $b->background?->name ?? '-',
                (int) $b->people_count,
                $b->voucher?->code_voucher ?? '-',
                $addons ?: '-',
                $b->keychain_notes ?: '-',
                (int) $b->price,
                (int) self::addonTotal($b),
                (int) self::total($b),
                (int) ($b->package?->downpayment ?? 0),
                ucfirst((string) $b->status),
                $b->confirmation ? 'Ya' : 'Tidak',
                optional($b->created_at)->format('Y-m-d H:i'),
            ];
        }

        return $rows;
    }

    /** Ringkasan (booking batal tidak dihitung ke pengunjung & pendapatan). */
    public function summary(Collection $bookings): array
    {
        $valid = $bookings->reject(fn ($b) => in_array($b->status, ['cancelled', 'canceled']));

        $packages = $valid->groupBy(fn ($b) => $b->package?->title ?? 'Tanpa Paket')
            ->map(fn ($g, $name) => [
                'name' => $name,
                'bookings' => $g->count(),
                'people' => (int) $g->sum('people_count'),
                'revenue' => (float) $g->sum(fn ($b) => self::total($b)),
            ])->sortByDesc('bookings')->values()->all();

        $backgrounds = $valid->filter(fn ($b) => $b->background)
            ->groupBy(fn ($b) => $b->background->name)
            ->map(fn ($g, $name) => ['name' => $name, 'bookings' => $g->count()])
            ->sortByDesc('bookings')->values()->all();

        return [
            'visitors' => (int) $valid->sum('people_count'),
            'bookings' => $valid->count(),
            'cancelled' => $bookings->count() - $valid->count(),
            'revenue' => (float) $valid->sum(fn ($b) => self::total($b)),
            'packages' => $packages,
            'backgrounds' => $backgrounds,
        ];
    }

    public static function rupiah(float|int $n): string
    {
        return 'Rp ' . number_format($n, 0, ',', '.');
    }
}
