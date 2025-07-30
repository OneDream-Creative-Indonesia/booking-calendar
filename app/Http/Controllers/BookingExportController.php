<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use League\Csv\Writer;

class BookingExportController extends Controller
{
    public function exportCsv()
    {
        $csv = Writer::createFromFileObject(new \SplTempFileObject());

        // Header
        $csv->insertOne([
            'Nama', 'WhatsApp', 'Jumlah Orang', 'Tanggal', 'Waktu',
            'Paket', 'Kode Voucher', 'Background', 'Email', 'Status', 'Total Harga'
        ]);

        // Data
        $bookings = Booking::with(['package', 'voucher', 'background'])->get();

        foreach ($bookings as $booking) {
            $csv->insertOne([
                $booking->name,
                $booking->whatsapp,
                $booking->people_count,
                $booking->date,
                $booking->time,
                $booking->package?->title ?? '-',
                $booking->voucher?->code_voucher ?? '-',
                $booking->background?->name ?? '-',
                $booking->email,
                ucfirst($booking->status),
                'Rp' . number_format($booking->price, 0, ',', '.'),
            ]);
        }

        return response((string) $csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="bookings.csv"',
        ]);
    }
}
