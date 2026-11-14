<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Live Dashboard Panitia – {{ $event->nama_event }}</title>
    <link rel="icon" href="{{ asset('img/logo/Logo Snapfun-01.svg') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: #F3F6FD;
            color: #0F172A;
        }
        @keyframes pulseDot {
            0%, 100% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.35); opacity: 0.55; }
        }
        .live-dot {
            animation: pulseDot 1.6s infinite ease-in-out;
        }
    </style>
</head>
<body class="min-h-screen pb-14">

@if(!$unlocked)
    <!-- ==========================================
         LAYAR PIN AKSES PANITIA
         ========================================== -->
    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl shadow-xl border border-blue-100 max-w-md w-full p-8 text-center">
            <div class="w-16 h-16 rounded-2xl bg-[#1759CA]/10 text-[#1759CA] flex items-center justify-center mx-auto mb-5">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
            </div>
            <span class="inline-block bg-[#FEDD03] text-slate-900 text-xs font-extrabold px-3 py-1 rounded-full tracking-wide uppercase mb-2">
                Akses Khusus Panitia
            </span>
            <h1 class="text-2xl font-extrabold text-slate-900">{{ $event->nama_event }}</h1>
            <p class="text-sm text-slate-500 mt-1 mb-6">
                {{ $event->tanggal_event ? \Carbon\Carbon::parse($event->tanggal_event)->translatedFormat('d F Y') : '-' }}
            </p>

            @if($errors->has('pin'))
                <div class="bg-rose-50 border border-rose-200 text-rose-700 text-xs font-semibold rounded-xl p-3 mb-4">
                    {{ $errors->first('pin') }}
                </div>
            @endif

            <form method="POST" action="{{ route('panitia.unlock', ['slug' => $slugKey]) }}" class="space-y-4">
                @csrf
                <div class="text-left">
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Masukkan Kode PIN Panitia</label>
                    <input
                        type="password"
                        name="pin"
                        required
                        autofocus
                        placeholder="Contoh: 1234"
                        class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:border-[#1759CA] focus:ring-2 focus:ring-[#1759CA]/20 outline-none text-center text-lg font-bold tracking-widest"
                    >
                </div>
                <button
                    type="submit"
                    class="w-full bg-[#1759CA] hover:bg-[#1246A3] text-white font-bold py-3.5 rounded-xl shadow-lg shadow-blue-600/20 transition cursor-pointer"
                >
                    Buka Live Dashboard
                </button>
            </form>
        </div>
    </div>
@else
    <!-- ==========================================
         HEADER DASHBOARD REALTIME PANITIA
         ========================================== -->
    <header class="bg-[#1759CA] text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <div class="flex flex-wrap items-center gap-2 mb-2">
                        <span class="inline-flex items-center gap-1.5 bg-emerald-400/20 border border-emerald-300/40 text-emerald-200 text-xs font-bold px-3 py-1 rounded-full">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 live-dot"></span>
                            LIVE REALTIME PANITIA
                        </span>
                        <span id="event-status-badge" class="inline-flex items-center text-xs font-bold px-2.5 py-1 rounded-full {{ $event->is_active ? 'bg-[#FEDD03] text-slate-900' : 'bg-white/20 text-white' }}">
                            {{ $event->is_active ? 'Pendaftaran Aktif' : 'Pendaftaran Ditutup' }}
                        </span>
                        <span class="text-xs text-blue-100">
                            Update terakhir: <strong id="last-updated-time" class="text-white">{{ $initialData['updated_at'] ?? now()->format('H:i:s') }}</strong>
                        </span>
                    </div>

                    <h1 id="event-title" class="text-2xl sm:text-3xl font-extrabold tracking-tight">
                        {{ $event->nama_event }}
                    </h1>
                    <p class="text-sm text-blue-100 mt-1 flex flex-wrap items-center gap-x-3 gap-y-1">
                        <span>📅 <span id="event-date">{{ $initialData['event']['tanggal_event'] ?? '-' }}</span></span>
                        <span>•</span>
                        <span>💵 Harga Strip: <strong id="info-strip-price" class="text-white">Rp {{ number_format((float)($event->strip_price ?? 0), 0, ',', '.') }}</strong></span>
                        @if($event->is_additional_active)
                            <span>•</span>
                            <span>➕ Harga Additional: <strong id="info-add-price" class="text-white">Rp {{ number_format((float)($event->additional_price ?? 0), 0, ',', '.') }}</strong></span>
                        @endif
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-2.5">
                    <button
                        type="button"
                        onclick="fetchRealtimeData(true)"
                        class="inline-flex items-center gap-2 bg-white/15 hover:bg-white/25 text-white text-xs sm:text-sm font-bold px-4 py-2.5 rounded-xl border border-white/20 transition cursor-pointer"
                    >
                        <svg id="refresh-icon" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        Refresh
                    </button>

                    <a
                        href="{{ route('panitia.export', ['slug' => $slugKey]) }}"
                        class="inline-flex items-center gap-2 bg-[#FEDD03] hover:bg-yellow-300 text-slate-900 text-xs sm:text-sm font-extrabold px-4 py-2.5 rounded-xl shadow-md transition"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        Export CSV
                    </a>
                </div>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-5 space-y-6">
        <!-- ==========================================
             4 KARTU UTAMA DASHBOARD
             ========================================== -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- 1. JUMLAH PENGUNJUNG -->
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200/80 flex items-start justify-between">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Jumlah Pengunjung</span>
                    <div class="mt-2 flex items-baseline gap-1.5">
                        <span id="stat-pengunjung" class="text-3xl font-extrabold text-slate-900">{{ $initialData['stats']['total_pengunjung'] ?? 0 }}</span>
                        <span class="text-sm font-semibold text-slate-500">Orang</span>
                    </div>
                    <p class="text-xs text-slate-500 mt-2">
                        Dari <strong id="stat-transaksi" class="text-slate-700">{{ $initialData['stats']['total_transaksi'] ?? 0 }}</strong> grup/antrean terdaftar
                    </p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-blue-50 text-[#1759CA] flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
            </div>

            <!-- 2. JUMLAH CETAK -->
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200/80 flex items-start justify-between">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Jumlah Cetak</span>
                    <div class="mt-2 flex items-baseline gap-1.5">
                        <span id="stat-cetak" class="text-3xl font-extrabold text-slate-900">{{ $initialData['stats']['total_cetak'] ?? 0 }}</span>
                        <span class="text-sm font-semibold text-slate-500">Strip</span>
                    </div>
                    <p class="text-xs text-slate-500 mt-2">
                        Senilai <strong id="stat-pendapatan-cetak" class="text-slate-700">Rp {{ number_format($initialData['stats']['pendapatan_cetak'] ?? 0, 0, ',', '.') }}</strong>
                    </p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                </div>
            </div>

            <!-- 3. JUMLAH ADDITIONAL -->
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200/80 flex items-start justify-between">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Jumlah Additional</span>
                    <div class="mt-2 flex items-baseline gap-1.5">
                        <span id="stat-additional" class="text-3xl font-extrabold text-slate-900">{{ $initialData['stats']['total_additional'] ?? 0 }}</span>
                        <span class="text-sm font-semibold text-slate-500">Item</span>
                    </div>
                    <p class="text-xs text-slate-500 mt-2">
                        Senilai <strong id="stat-pendapatan-additional" class="text-slate-700">Rp {{ number_format($initialData['stats']['pendapatan_additional'] ?? 0, 0, ',', '.') }}</strong>
                    </p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                </div>
            </div>

            <!-- 4. TOTAL PENDAPATAN -->
            <div class="bg-white rounded-2xl p-5 shadow-sm border-2 border-[#CE004F]/20 flex items-start justify-between">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-[#CE004F]">Total Pendapatan</span>
                    <div class="mt-2">
                        <span id="stat-total-pendapatan" class="text-2xl sm:text-3xl font-extrabold text-[#CE004F]">
                            Rp {{ number_format($initialData['stats']['total_pendapatan'] ?? 0, 0, ',', '.') }}
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 mt-2 flex flex-wrap items-center gap-x-2">
                        <span>QRIS: <strong id="stat-qris-nominal" class="text-slate-700">Rp {{ number_format($initialData['stats']['pendapatan_qris'] ?? 0, 0, ',', '.') }}</strong></span>
                        <span>•</span>
                        <span>Tunai: <strong id="stat-tunai-nominal" class="text-slate-700">Rp {{ number_format($initialData['stats']['pendapatan_tunai'] ?? 0, 0, ',', '.') }}</strong></span>
                    </p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-rose-50 text-[#CE004F] flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- ==========================================
             PANEL BAGI HASIL & PROGRESS ANTREAN
             ========================================== -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            <!-- KALKULATOR / INFO BAGI HASIL PANITIA -->
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200/80 lg:col-span-2">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-4 border-b border-slate-100">
                    <div>
                        <h2 class="text-base font-extrabold text-slate-900 flex items-center gap-2">
                            <span>🤝 Estimasi Bagi Hasil (Revenue Share)</span>
                        </h2>
                        <p class="text-xs text-slate-500">Perhitungan otomatis berdasarkan total pendapatan realtime</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <label for="share-percent-input" class="text-xs font-bold text-slate-600">Porsi Panitia (%):</label>
                        <input
                            id="share-percent-input"
                            type="number"
                            min="0"
                            max="100"
                            step="0.5"
                            value="{{ (float)($initialData['stats']['bagi_hasil_percent'] ?? 0) > 0 ? (float)$initialData['stats']['bagi_hasil_percent'] : 20 }}"
                            oninput="recalculateShare()"
                            class="w-20 px-3 py-1.5 text-sm font-bold text-center rounded-xl border border-slate-300 focus:border-[#1759CA] outline-none"
                        >
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-4">
                    <div class="bg-emerald-50/70 border border-emerald-200/80 rounded-xl p-4">
                        <div class="text-xs font-bold text-emerald-800 uppercase tracking-wider">
                            Porsi Panitia (<span id="label-share-panitia">20</span>%)
                        </div>
                        <div id="val-share-panitia" class="text-2xl font-extrabold text-emerald-700 mt-1">
                            Rp 0
                        </div>
                    </div>
                    <div class="bg-blue-50/70 border border-blue-200/80 rounded-xl p-4">
                        <div class="text-xs font-bold text-blue-800 uppercase tracking-wider">
                            Porsi Snap Fun (<span id="label-share-snapfun">80</span>%)
                        </div>
                        <div id="val-share-snapfun" class="text-2xl font-extrabold text-[#1759CA] mt-1">
                            Rp 0
                        </div>
                    </div>
                </div>
            </div>

            <!-- STATUS PROGRES ANTREAN -->
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200/80 flex flex-col justify-between">
                <div>
                    <h2 class="text-base font-extrabold text-slate-900">📊 Status Operasional</h2>
                    <p class="text-xs text-slate-500 mb-4">Progres layanan di booth saat ini</p>
                </div>
                <div class="grid grid-cols-2 gap-3 text-center">
                    <div class="bg-slate-50 rounded-xl p-3 border border-slate-200/60">
                        <div id="stat-menunggu" class="text-xl font-extrabold text-amber-600">{{ $initialData['stats']['antrean_menunggu'] ?? 0 }}</div>
                        <div class="text-[11px] font-semibold text-slate-500 mt-0.5">Menunggu Foto</div>
                    </div>
                    <div class="bg-slate-50 rounded-xl p-3 border border-slate-200/60">
                        <div id="stat-dipanggil" class="text-xl font-extrabold text-blue-600">{{ $initialData['stats']['antrean_dipanggil'] ?? 0 }}</div>
                        <div class="text-[11px] font-semibold text-slate-500 mt-0.5">Sudah Dipanggil</div>
                    </div>
                    <div class="bg-slate-50 rounded-xl p-3 border border-slate-200/60">
                        <div id="stat-sudah-foto" class="text-xl font-extrabold text-emerald-600">{{ $initialData['stats']['sudah_foto'] ?? 0 }}</div>
                        <div class="text-[11px] font-semibold text-slate-500 mt-0.5">Selesai Foto</div>
                    </div>
                    <div class="bg-slate-50 rounded-xl p-3 border border-slate-200/60">
                        <div id="stat-sudah-print" class="text-xl font-extrabold text-purple-600">{{ $initialData['stats']['sudah_print'] ?? 0 }}</div>
                        <div class="text-[11px] font-semibold text-slate-500 mt-0.5">Selesai Cetak</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ==========================================
             TABEL DATA REGISTRASI REALTIME
             ========================================== -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden">
            <div class="p-4 sm:p-5 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <h2 class="text-lg font-extrabold text-slate-900">Data Registrasi Pengunjung (Realtime)</h2>
                    <p class="text-xs text-slate-500">Data otomatis diperbarui setiap 3 detik tanpa perlu muat ulang halaman</p>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    <input
                        id="search-input"
                        type="text"
                        oninput="renderTable()"
                        placeholder="Cari nama / no. antrean..."
                        class="px-3.5 py-2 text-xs sm:text-sm rounded-xl border border-slate-300 focus:border-[#1759CA] outline-none w-full sm:w-56"
                    >
                    <select
                        id="payment-filter"
                        onchange="renderTable()"
                        class="px-3 py-2 text-xs sm:text-sm rounded-xl border border-slate-300 focus:border-[#1759CA] outline-none bg-white font-semibold text-slate-700"
                    >
                        <option value="ALL">Semua Pembayaran</option>
                        <option value="QRIS">QRIS</option>
                        <option value="TUNAI">TUNAI</option>
                    </select>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 text-xs uppercase font-bold tracking-wider">
                        <tr>
                            <th class="p-3.5 text-center w-24">Antrean</th>
                            <th class="p-3.5">Nama Pengunjung</th>
                            <th class="p-3.5 text-center">Orang</th>
                            <th class="p-3.5 text-center">Cetak (Strip)</th>
                            <th class="p-3.5 text-center">Additional</th>
                            <th class="p-3.5 text-center">Pembayaran</th>
                            <th class="p-3.5 text-center">Progres</th>
                            <th class="p-3.5 text-right">Pendapatan</th>
                            <th class="p-3.5 text-center">Waktu</th>
                        </tr>
                    </thead>
                    <tbody id="registration-tbody" class="divide-y divide-slate-200/80">
                        <!-- Diisi secara dinamis oleh JS -->
                    </tbody>
                    <tfoot class="bg-slate-50 border-t-2 border-slate-200 font-extrabold text-slate-900">
                        <tr>
                            <td colspan="2" class="p-3.5 text-right text-xs uppercase tracking-wider text-slate-500">
                                Total Ditampilkan:
                            </td>
                            <td id="foot-orang" class="p-3.5 text-center">0 Orang</td>
                            <td id="foot-cetak" class="p-3.5 text-center">0 Strip</td>
                            <td id="foot-additional" class="p-3.5 text-center">0 Add</td>
                            <td colspan="2" class="p-3.5 text-right text-xs uppercase tracking-wider text-slate-500">
                                Total Pendapatan:
                            </td>
                            <td id="foot-pendapatan" class="p-3.5 text-right text-base text-[#CE004F]">
                                Rp 0
                            </td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </main>

    <script>
        const slugKey = @json($slugKey);
        let stateData = @json($initialData);

        function formatRupiah(num) {
            return 'Rp ' + Math.round(Number(num || 0)).toLocaleString('id-ID');
        }

        function escapeHtml(str) {
            return String(str ?? '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;');
        }

        function recalculateShare() {
            const totalRev = Number(stateData?.stats?.total_pendapatan || 0);
            const inputEl = document.getElementById('share-percent-input');
            let pct = parseFloat(inputEl?.value || '0');
            if (isNaN(pct) || pct < 0) pct = 0;
            if (pct > 100) pct = 100;

            const panitiaAmt = Math.round(totalRev * (pct / 100));
            const snapfunAmt = totalRev - panitiaAmt;

            document.getElementById('label-share-panitia').textContent = pct;
            document.getElementById('label-share-snapfun').textContent = Math.max(0, +(100 - pct).toFixed(2));
            document.getElementById('val-share-panitia').textContent = formatRupiah(panitiaAmt);
            document.getElementById('val-share-snapfun').textContent = formatRupiah(snapfunAmt);
        }

        function renderStats() {
            if (!stateData || !stateData.stats) return;
            const s = stateData.stats;
            const ev = stateData.event || {};

            document.getElementById('last-updated-time').textContent = stateData.updated_at || '-';
            document.getElementById('stat-pengunjung').textContent = s.total_pengunjung ?? 0;
            document.getElementById('stat-transaksi').textContent = s.total_transaksi ?? 0;
            document.getElementById('stat-cetak').textContent = s.total_cetak ?? 0;
            document.getElementById('stat-pendapatan-cetak').textContent = formatRupiah(s.pendapatan_cetak);
            document.getElementById('stat-additional').textContent = s.total_additional ?? 0;
            document.getElementById('stat-pendapatan-additional').textContent = formatRupiah(s.pendapatan_additional);
            document.getElementById('stat-total-pendapatan').textContent = formatRupiah(s.total_pendapatan);
            document.getElementById('stat-qris-nominal').textContent = formatRupiah(s.pendapatan_qris);
            document.getElementById('stat-tunai-nominal').textContent = formatRupiah(s.pendapatan_tunai);

            document.getElementById('stat-menunggu').textContent = s.antrean_menunggu ?? 0;
            document.getElementById('stat-dipanggil').textContent = s.antrean_dipanggil ?? 0;
            document.getElementById('stat-sudah-foto').textContent = s.sudah_foto ?? 0;
            document.getElementById('stat-sudah-print').textContent = s.sudah_print ?? 0;

            recalculateShare();
        }

        function renderTable() {
            const tbody = document.getElementById('registration-tbody');
            if (!tbody) return;

            const items = stateData?.items || [];
            const query = (document.getElementById('search-input')?.value || '').trim().toLowerCase();
            const paymentFilter = document.getElementById('payment-filter')?.value || 'ALL';

            const filtered = items.filter(item => {
                const matchSearch = !query ||
                    String(item.nama || '').toLowerCase().includes(query) ||
                    String(item.queue_number || '').toLowerCase().includes(query);
                const matchPay = paymentFilter === 'ALL' || String(item.transaction_type || '').toUpperCase() === paymentFilter;
                return matchSearch && matchPay;
            });

            if (filtered.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="9" class="p-8 text-center text-slate-400 font-medium">
                            Belum ada data registrasi yang sesuai.
                        </td>
                    </tr>
                `;
                document.getElementById('foot-orang').textContent = '0 Orang';
                document.getElementById('foot-cetak').textContent = '0 Strip';
                document.getElementById('foot-additional').textContent = '0 Add';
                document.getElementById('foot-pendapatan').textContent = 'Rp 0';
                return;
            }

            let sumOrang = 0;
            let sumCetak = 0;
            let sumAdd = 0;
            let sumSubtotal = 0;

            tbody.innerHTML = filtered.map(item => {
                sumOrang += Number(item.jumlah || 0);
                sumCetak += Number(item.cetak || 0);
                sumAdd += Number(item.additional || 0);
                sumSubtotal += Number(item.subtotal || 0);

                const isQris = String(item.transaction_type).toUpperCase() === 'QRIS';
                const payBadge = isQris
                    ? '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-blue-800">QRIS</span>'
                    : '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">TUNAI</span>';

                const badgeFoto = item.is_foto
                    ? '<span class="px-2 py-0.5 rounded text-[11px] font-bold bg-emerald-100 text-emerald-700">Foto ✓</span>'
                    : '<span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-slate-100 text-slate-400">Foto</span>';

                const badgePrint = item.is_print
                    ? '<span class="px-2 py-0.5 rounded text-[11px] font-bold bg-purple-100 text-purple-700">Print ✓</span>'
                    : '<span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-slate-100 text-slate-400">Print</span>';

                return `
                    <tr class="hover:bg-slate-50/80 transition">
                        <td class="p-3.5 text-center font-extrabold text-[#1759CA]">${escapeHtml(item.queue_number)}</td>
                        <td class="p-3.5 font-bold text-slate-800">${escapeHtml(item.nama)}</td>
                        <td class="p-3.5 text-center font-semibold text-slate-700">${Number(item.jumlah || 0)}</td>
                        <td class="p-3.5 text-center font-semibold text-slate-700">${Number(item.cetak || 0)}</td>
                        <td class="p-3.5 text-center font-semibold text-slate-700">${Number(item.additional || 0)}</td>
                        <td class="p-3.5 text-center">${payBadge}</td>
                        <td class="p-3.5 text-center">
                            <div class="inline-flex items-center gap-1">${badgeFoto}${badgePrint}</div>
                        </td>
                        <td class="p-3.5 text-right font-extrabold text-slate-900">${formatRupiah(item.subtotal)}</td>
                        <td class="p-3.5 text-center text-xs text-slate-500">${escapeHtml(item.waktu)}</td>
                    </tr>
                `;
            }).join('');

            document.getElementById('foot-orang').textContent = `${sumOrang} Orang`;
            document.getElementById('foot-cetak').textContent = `${sumCetak} Strip`;
            document.getElementById('foot-additional').textContent = `${sumAdd} Add`;
            document.getElementById('foot-pendapatan').textContent = formatRupiah(sumSubtotal);
        }

        async function fetchRealtimeData(manual = false) {
            const icon = document.getElementById('refresh-icon');
            if (manual && icon) {
                icon.classList.add('animate-spin');
            }
            try {
                const res = await fetch(`/api/panitia/realtime/${encodeURIComponent(slugKey)}`, {
                    headers: { 'Accept': 'application/json' },
                    cache: 'no-store'
                });
                if (res.status === 403) {
                    window.location.reload();
                    return;
                }
                const json = await res.json();
                if (json && json.success && json.data) {
                    stateData = json.data;
                    renderStats();
                    renderTable();
                }
            } catch (e) {
                console.warn('Gagal mengambil data realtime:', e);
            } finally {
                if (manual && icon) {
                    setTimeout(() => icon.classList.remove('animate-spin'), 400);
                }
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            renderStats();
            renderTable();
            setInterval(() => fetchRealtimeData(false), 3000);
        });
    </script>
@endif

</body>
</html>
