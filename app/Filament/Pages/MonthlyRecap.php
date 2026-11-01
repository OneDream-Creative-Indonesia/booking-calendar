<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\Storage;

class MonthlyRecap extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-arrow-down';
    protected static ?string $navigationLabel = 'E-Statement Booking';
    protected static ?string $title = 'Rekap Booking Bulanan';
    protected static ?string $navigationGroup = 'Laporan';

    protected static string $view = 'filament.pages.monthly-recap';

    public function getRecapFiles(): array
    {
        // Pastikan folder recaps ada
        if (!Storage::disk('local')->exists('recaps')) {
            return [];
        }

        $files = Storage::disk('local')->files('recaps');
        $recaps = [];

        foreach ($files as $file) {
            $filename = basename($file);
            $info = pathinfo($filename);
            
            if (isset($info['extension']) && $info['extension'] !== 'xml') {
                continue;
            }

            if (preg_match('/rekap_booking_(\d{4})_(\d+)/', $filename, $matches)) {
                $year = $matches[1];
                $month = date('F', mktime(0, 0, 0, $matches[2], 10)); 
                
                $recaps[] = [
                    'title' => "Rekap Pemesanan - {$month} {$year}",
                    'filename' => $filename,
                    'path' => $file,
                    'size' => round(Storage::disk('local')->size($file) / 1024, 2) . ' KB',
                    'timestamp' => Storage::disk('local')->lastModified($file),
                ];
            }
        }

        usort($recaps, function ($a, $b) {
            return $b['timestamp'] <=> $a['timestamp'];
        });

        return $recaps;
    }

    public function downloadRecap($path)
    {
        if (Storage::disk('local')->exists($path)) {
            return Storage::disk('local')->download($path);
        }
    }
}