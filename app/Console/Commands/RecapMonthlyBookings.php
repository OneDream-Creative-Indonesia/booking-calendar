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
    protected $description = 'Otomatis rekap semua booking sebelum bulan ini ke format XML dan hapus data lama';

    public function handle()
    {
        // Tentukan batas waktu: Awal bulan ini (Tanggal 1, jam 00:00:00)
        // Semua data sebelum waktu ini akan direkap & dihapus
        $startOfCurrentMonth = Carbon::now()->startOfMonth();

        // Ambil semua data booking yang lebih lama dari awal bulan ini
        $oldBookings = Booking::where('created_at', '<', $startOfCurrentMonth)->get();

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
                // Sesuaikan 'customer_name' dengan nama kolom yang ada di tabel bookings Anda
                $item->addChild('CustomerName', htmlspecialchars($booking->customer_name ?? 'N/A')); 
                $item->addChild('BookingDate', $booking->created_at->format('Y-m-d H:i:s'));
            }

            // Nama file contoh: rekap_booking_2026_08.xml
            $fileName = "rekap_booking_{$year}_{$month}.xml";
            
            // Simpan ke storage/app/recaps/
            Storage::disk('local')->put("recaps/{$fileName}", $xml->asXML());

            // Hapus data booking HANYA untuk bulan yang barusan sukses dibuatkan XML-nya
            $bookingIds = $bookings->pluck('id')->toArray();
            Booking::whereIn('id', $bookingIds)->delete();

            $this->info("Rekap XML berhasil dibuat dan diarsipkan: {$fileName}");
        }

        $this->info("Pembersihan selesai! Hanya data bulan ini yang tersisa di database.");
    }
}