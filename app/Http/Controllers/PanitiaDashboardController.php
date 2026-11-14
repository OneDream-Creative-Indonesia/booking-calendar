<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Ticketing;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class PanitiaDashboardController extends Controller
{
    public static function ensureColumnsExist(): void
    {
        try {
            if (Schema::hasTable('events')) {
                if (!Schema::hasColumn('events', 'panitia_pin')) {
                    Schema::table('events', function ($table) {
                        $table->string('panitia_pin', 32)->nullable();
                    });
                }
                if (!Schema::hasColumn('events', 'revenue_share_percent')) {
                    Schema::table('events', function ($table) {
                        $table->decimal('revenue_share_percent', 5, 2)->default(0);
                    });
                }
            }
        } catch (\Throwable $e) {
            // Ignore if DB is read-only or not yet migrated
        }
    }

    protected function resolveEvent(?string $slug): ?Event
    {
        self::ensureColumnsExist();

        if ($slug) {
            $event = Event::where('slug', $slug)->orderBy('id', 'desc')->first();
            if (!$event && is_numeric($slug)) {
                $event = Event::find((int) $slug);
            }
            if (!$event && str_contains($slug, '-')) {
                $firstPart = explode('-', $slug)[0];
                if (is_numeric($firstPart)) {
                    $event = Event::find((int) $firstPart);
                }
            }
            return $event;
        }

        return Event::where('is_active', true)->orderBy('id', 'desc')->first()
            ?? Event::orderBy('tanggal_event', 'desc')->orderBy('id', 'desc')->first();
    }

    protected function isUnlocked(Request $request, Event $event): bool
    {
        $pin = trim((string) ($event->panitia_pin ?? ''));
        if ($pin === '') {
            return true;
        }

        // Admin atau user internal yang sedang login bebas akses
        if (auth()->check() && (auth()->user()->can('view_any_ticketing') || auth()->user()->hasAnyRole(['super_admin', 'event', 'event_viewer']))) {
            return true;
        }

        // Cek query parameter ?pin=... untuk kemudahan share link otomatis
        if ($request->filled('pin') && hash_equals($pin, trim((string) $request->query('pin')))) {
            $request->session()->put('panitia_unlocked_' . $event->id, true);
            return true;
        }

        return (bool) $request->session()->get('panitia_unlocked_' . $event->id, false);
    }

    public function show(Request $request, ?string $slug = null)
    {
        $event = $this->resolveEvent($slug);

        if (!$event) {
            abort(404, 'Event Photobooth tidak ditemukan.');
        }

        $unlocked = $this->isUnlocked($request, $event);
        $initialData = $unlocked ? $this->buildPayload($event) : null;

        return view('panitia-dashboard', [
            'event' => $event,
            'unlocked' => $unlocked,
            'initialData' => $initialData,
            'slugKey' => $event->slug ?: (string) $event->id,
        ]);
    }

    public function unlock(Request $request, string $slug)
    {
        $event = $this->resolveEvent($slug);

        if (!$event) {
            abort(404, 'Event tidak ditemukan.');
        }

        $pin = trim((string) ($event->panitia_pin ?? ''));
        $inputPin = trim((string) $request->input('pin', ''));

        if ($pin === '' || hash_equals($pin, $inputPin)) {
            $request->session()->put('panitia_unlocked_' . $event->id, true);
            return redirect()->route('panitia.dashboard', ['slug' => $event->slug ?: $event->id]);
        }

        return back()->withErrors(['pin' => 'Kode PIN Panitia tidak sesuai. Silakan coba lagi.']);
    }

    public function realtimeData(Request $request, ?string $slug = null)
    {
        $event = $this->resolveEvent($slug);

        if (!$event) {
            return response()->json([
                'success' => false,
                'message' => 'Event tidak ditemukan.',
            ], 404);
        }

        if (!$this->isUnlocked($request, $event)) {
            return response()->json([
                'success' => false,
                'locked' => true,
                'message' => 'Akses membutuhkan PIN Panitia.',
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $this->buildPayload($event),
        ]);
    }

    public function exportCsv(Request $request, string $slug)
    {
        $event = $this->resolveEvent($slug);

        if (!$event) {
            abort(404, 'Event tidak ditemukan.');
        }

        if (!$this->isUnlocked($request, $event)) {
            return redirect()->route('panitia.dashboard', ['slug' => $slug]);
        }

        $tickets = Ticketing::where('event_id', $event->id)->orderBy('id', 'asc')->get();
        $namaEvent = Str::slug($event->nama_event ?: 'Event');
        $fileName = 'Laporan-Panitia-' . $namaEvent . '-' . now()->format('Y-m-d_His') . '.csv';

        return response()->streamDownload(function () use ($tickets, $event) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            $stripPrice = (float) ($event->strip_price ?? 0);
            $addPrice = $event->is_additional_active ? (float) ($event->additional_price ?? 0) : 0;

            fputcsv($handle, [
                'No. Antrean',
                'Nama Pengunjung',
                'Jumlah Orang',
                'Jumlah Cetak (Strip)',
                'Jumlah Additional',
                'Metode Pembayaran',
                'Pendapatan Cetak',
                'Pendapatan Additional',
                'Total Pendapatan',
                'Status Antrean',
                'Sesi Foto',
                'Export File',
                'Cetak Foto',
                'Waktu Registrasi',
            ]);

            $totalOrang = 0;
            $totalCetak = 0;
            $totalAdd = 0;
            $grandTotal = 0;

            foreach ($tickets as $ticket) {
                $orang = (int) ($ticket->jumlah ?? 0);
                $cetak = (int) ($ticket->cetak ?? 0);
                $add = (int) ($ticket->additional ?? 0);
                $revCetak = $stripPrice * $cetak;
                $revAdd = $addPrice * $add;
                $subtotal = $revCetak + $revAdd;

                $totalOrang += $orang;
                $totalCetak += $cetak;
                $totalAdd += $add;
                $grandTotal += $subtotal;

                fputcsv($handle, [
                    $ticket->queue_number ?? '#' . $ticket->id,
                    $ticket->nama,
                    $orang,
                    $cetak,
                    $add,
                    strtoupper($ticket->transaction_type ?? '-'),
                    $revCetak,
                    $revAdd,
                    $subtotal,
                    $ticket->status ?? 'menunggu',
                    $ticket->is_foto ? 'Selesai' : 'Belum',
                    $ticket->is_export ? 'Selesai' : 'Belum',
                    $ticket->is_print ? 'Selesai' : 'Belum',
                    $ticket->created_at ? $ticket->created_at->format('Y-m-d H:i:s') : '-',
                ]);
            }

            fputcsv($handle, []);
            fputcsv($handle, [
                'TOTAL KESELURUHAN',
                $tickets->count() . ' Transaksi',
                $totalOrang,
                $totalCetak,
                $totalAdd,
                '',
                $stripPrice * $totalCetak,
                $addPrice * $totalAdd,
                $grandTotal,
                '',
                '',
                '',
                '',
                '',
            ]);

            $sharePercent = (float) ($event->revenue_share_percent ?? 0);
            if ($sharePercent > 0) {
                $panitiaShare = round($grandTotal * ($sharePercent / 100));
                fputcsv($handle, [
                    "ESTIMASI BAGI HASIL PANITIA ({$sharePercent}%)",
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    $panitiaShare,
                ]);
            }

            fclose($handle);
        }, $fileName, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    protected function buildPayload(Event $event): array
    {
        $tickets = Ticketing::where('event_id', $event->id)
            ->orderBy('id', 'desc')
            ->get();

        $stripPrice = (float) ($event->strip_price ?? 0);
        $isAdditionalActive = (bool) ($event->is_additional_active ?? false);
        $additionalPrice = $isAdditionalActive ? (float) ($event->additional_price ?? 0) : 0;
        $sharePercent = (float) ($event->revenue_share_percent ?? 0);

        $totalTransaksi = $tickets->count();
        $totalPengunjung = (int) $tickets->sum('jumlah');
        $totalCetak = (int) $tickets->sum('cetak');
        $totalAdditional = (int) $tickets->sum('additional');

        $pendapatanCetak = $stripPrice * $totalCetak;
        $pendapatanAdditional = $additionalPrice * $totalAdditional;
        $totalPendapatan = $pendapatanCetak + $pendapatanAdditional;

        $pendapatanQris = 0;
        $pendapatanTunai = 0;
        $transaksiQris = 0;
        $transaksiTunai = 0;
        $antreanMenunggu = 0;
        $antreanDipanggil = 0;
        $sudahFoto = 0;
        $sudahPrint = 0;

        $items = [];

        foreach ($tickets as $ticket) {
            $cetak = (int) ($ticket->cetak ?? 0);
            $add = (int) ($ticket->additional ?? 0);
            $orang = (int) ($ticket->jumlah ?? 0);
            $subtotal = ($stripPrice * $cetak) + ($additionalPrice * $add);
            $method = strtolower(trim((string) ($ticket->transaction_type ?? '')));

            if ($method === 'qris') {
                $pendapatanQris += $subtotal;
                $transaksiQris++;
            } else {
                $pendapatanTunai += $subtotal;
                $transaksiTunai++;
            }

            $status = strtolower(trim((string) ($ticket->status ?? 'menunggu')));
            if ($status === 'dipanggil') {
                $antreanDipanggil++;
            } else {
                $antreanMenunggu++;
            }

            if ($ticket->is_foto) {
                $sudahFoto++;
            }
            if ($ticket->is_print) {
                $sudahPrint++;
            }

            $items[] = [
                'id' => $ticket->id,
                'queue_number' => $ticket->queue_number ?: ('#' . $ticket->id),
                'nama' => $ticket->nama,
                'jumlah' => $orang,
                'cetak' => $cetak,
                'additional' => $add,
                'transaction_type' => strtoupper($ticket->transaction_type ?: '-'),
                'status' => $status ?: 'menunggu',
                'is_foto' => (bool) $ticket->is_foto,
                'is_export' => (bool) $ticket->is_export,
                'is_print' => (bool) $ticket->is_print,
                'subtotal' => $subtotal,
                'waktu' => $ticket->created_at ? Carbon::parse($ticket->created_at)->format('H:i:s') : '-',
                'tanggal' => $ticket->created_at ? Carbon::parse($ticket->created_at)->format('d M Y') : '-',
            ];
        }

        $bagiHasilPanitia = $sharePercent > 0 ? round($totalPendapatan * ($sharePercent / 100)) : 0;
        $bagiHasilSnapfun = $totalPendapatan - $bagiHasilPanitia;

        return [
            'event' => [
                'id' => $event->id,
                'nama_event' => $event->nama_event,
                'slug' => $event->slug ?: (string) $event->id,
                'tanggal_event' => $event->tanggal_event ? Carbon::parse($event->tanggal_event)->translatedFormat('d F Y') : '-',
                'is_active' => (bool) $event->is_active,
                'strip_price' => $stripPrice,
                'is_additional_active' => $isAdditionalActive,
                'additional_price' => $additionalPrice,
                'revenue_share_percent' => $sharePercent,
            ],
            'stats' => [
                'total_transaksi' => $totalTransaksi,
                'total_pengunjung' => $totalPengunjung,
                'total_cetak' => $totalCetak,
                'total_additional' => $totalAdditional,
                'pendapatan_cetak' => $pendapatanCetak,
                'pendapatan_additional' => $pendapatanAdditional,
                'total_pendapatan' => $totalPendapatan,
                'pendapatan_qris' => $pendapatanQris,
                'pendapatan_tunai' => $pendapatanTunai,
                'transaksi_qris' => $transaksiQris,
                'transaksi_tunai' => $transaksiTunai,
                'bagi_hasil_percent' => $sharePercent,
                'bagi_hasil_panitia' => $bagiHasilPanitia,
                'bagi_hasil_snapfun' => $bagiHasilSnapfun,
                'antrean_menunggu' => $antreanMenunggu,
                'antrean_dipanggil' => $antreanDipanggil,
                'sudah_foto' => $sudahFoto,
                'sudah_print' => $sudahPrint,
            ],
            'items' => $items,
            'updated_at' => now()->format('H:i:s'),
        ];
    }
}
