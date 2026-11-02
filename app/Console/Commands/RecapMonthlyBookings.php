<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use SimpleXMLElement;

class RecapMonthlyBookings extends Command
{
    protected $signature = 'booking:recap-monthly';
    protected $description = 'Otomatis arsipkan semua booking sebelum bulan ini ke format XML (data tidak dihapus; hapus manual via E-Statement Booking)';

    public function handle()
    {
        // Tentukan batas waktu: Awal bulan ini (Tanggal 1, jam 00:00:00)
        // Semua data sebelum waktu ini akan direkap & dihapus
        $startOfCurrentMonth = Carbon::now()->startOfMonth();

        // Ambil semua data booking yang lebih lama dari awal bulan ini
        $oldBookings = Booking::with(['package', 'background', 'voucher'])->where('created_at', '<', $startOfCurrentMonth)->get();

        if ($oldBookings->isEmpty()) {
            $this->info("Tidak ada data booking lama yang perlu direkap.");
            return;
        }

        // Kelompokkan data berdasarkan Tahun dan Bulan
        $groupedBookings = $oldBookings->groupBy(function ($item) {
            return $item->created_at->format('Y_m'); // Format pengelompokan: "2026_08", "2026_07", dll
        });

        foreach ($groupedBookings as $yearMonth => $bookings) {
            // Pecah kembali menjadi tahun dan bulan untuk nama file
            list($year, $month) = explode('_', $yearMonth);

            $xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><Bookings/>');

            foreach ($bookings as $booking) {
                $item = $xml->addChild('Booking');
                $item->addChild('ID', $booking->id);
                foreach ([
                    'CustomerName' => $booking->name,
                    'WhatsApp' => $booking->whatsapp,
                    'Email' => $booking->email,
                    'BookingDate' => $booking->date,
                    'Time' => $booking->time,
                    'Package' => $booking->package?->title,
                    'Background' => $booking->background?->name,
                    'PeopleCount' => $booking->people_count,
                    'Voucher' => $booking->voucher?->code_voucher,
                    'Price' => $booking->price,
                    'Status' => $booking->status,
                    'CreatedAt' => $booking->created_at->format('Y-m-d H:i:s'),
                ] as $tag => $value) {
                    $item->addChild($tag, htmlspecialchars((string) $value));
                }
            }

            // Nama file contoh: rekap_booking_2026_08.xml
            $fileName = "rekap_booking_{$year}_{$month}.xml";
            
            // Simpan ke storage/app/recaps/
            Storage::disk('local')->put("recaps/{$fileName}", $xml->asXML());

            $this->info("Rekap XML berhasil dibuat dan diarsipkan: {$fileName}");
        }

        $this->info("Arsip selesai.");
    }
}