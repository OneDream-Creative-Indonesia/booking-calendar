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
    protected $description = 'Otomatis rekap booking bulan lalu ke format XML dan hapus data lama';

    public function handle()
    {
        $lastMonth = Carbon::now()->subMonth();
        $year = $lastMonth->year;
        $month = $lastMonth->month;

        $bookings = Booking::whereYear('created_at', $year)
                           ->whereMonth('created_at', $month)
                           ->get();

        if ($bookings->isEmpty()) {
            $this->info("Tidak ada data booking untuk bulan {$month}-{$year}.");
            return;
        }

        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><Bookings/>');

        foreach ($bookings as $booking) {
            $item = $xml->addChild('Booking');
            $item->addChild('ID', $booking->id);
            $item->addChild('CustomerName', htmlspecialchars($booking->customer_name ?? 'N/A'));
            $item->addChild('BookingDate', $booking->created_at->format('Y-m-d H:i:s'));
        }

        $fileName = "rekap_booking_{$year}_{$month}.xml";
        Storage::disk('local')->put("recaps/{$fileName}", $xml->asXML());

        Booking::whereYear('created_at', $year)
               ->whereMonth('created_at', $month)
               ->delete();

        $this->info("Rekap XML berhasil dibuat dan diarsipkan: {$fileName}");
    }
}
