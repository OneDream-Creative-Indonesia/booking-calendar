<?php

namespace App\Http\Controllers;

use App\Models\Ticketing;
use Illuminate\Http\Request;
use League\Csv\Writer;

class TickettingReport extends Controller
{
    public function exportCsv()
    {
        $csv = Writer::createFromFileObject(new \SplTempFileObject());

        $csv->insertOne(['Nama', 'Jumlah Orang', 'No Handphone', 'Jenis Pembayaran']);

        $reports = Ticketing::all();
        foreach ($reports as $report) {

            $csv->insertOne([
                $report->nama,
                $report->jumlah,
                $report->telpon,
                $report->transaction_type,
            ]);
        }

        return response((string) $csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="ticketing_report.csv"',
        ]);
    }
}
