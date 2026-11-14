<x-filament-panels::page>
@php
    $canUpdateTicket = auth()->user()?->can('update_ticketing');
    $canDeleteTicket = auth()->user()?->can('delete_ticketing');
@endphp

    <!-- Tombol Bulk Delete -->
    @if($canDeleteTicket && count($selectedTickets) > 0)
        <div class="p-4 bg-white dark:bg-gray-900 shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10 rounded-xl flex items-center justify-between mb-4">
            <span class="font-bold text-gray-700 dark:text-gray-200">{{ count($selectedTickets) }} Tiket Terpilih</span>
            <x-filament::button wire:click="deleteSelected" color="danger" icon="heroicon-o-trash">
                Hapus Terpilih
            </x-filament::button>
        </div>
    @endif

    <!-- LOOPING SEMUA EVENT -->
    @foreach($this->events as $event)
        @php
            $totalOrang = (int) $event->ticketings->sum('jumlah');
            $totalCetak = (int) $event->ticketings->sum('cetak');
            $totalAdditional = (int) $event->ticketings->sum('additional');
            $totalTransaksi = $event->ticketings->count();
            
            $hargaStrip = (float) ($event->strip_price ?? 0);
            $hargaAdd = $event->is_additional_active ? (float) ($event->additional_price ?? 0) : 0;
            
            $pendapatanStrip = $hargaStrip * $totalCetak;
            $pendapatanAdd = $hargaAdd * $totalAdditional;
            $totalPendapatan = $pendapatanStrip + $pendapatanAdd;

            $sharePercent = (float) ($event->revenue_share_percent ?? 0);
            $bagiHasilPanitia = $sharePercent > 0 ? round($totalPendapatan * ($sharePercent / 100)) : 0;
            $panitiaSlug = $event->slug ?: $event->id;
            $panitiaUrl = url('/panitia/' . $panitiaSlug);
            $panitiaPin = trim((string) ($event->panitia_pin ?? ''));
        @endphp

        <div class="bg-white dark:bg-gray-900 shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10 rounded-xl overflow-hidden mb-6">
            
            <!-- HEADER FOLDER EVENT -->
            <div class="p-4 bg-gray-50 dark:bg-white/5 border-b border-gray-200 dark:border-white/10 flex flex-wrap items-center justify-between gap-3">
                
                <div class="flex items-center gap-3">
                    <input type="checkbox" wire:click="toggleSelectAll({{ $event->id }})" class="rounded border-gray-300 text-primary-600 shadow-sm focus:ring-primary-600 cursor-pointer w-4 h-4">
                    <div>
                        <h3 class="text-lg font-bold text-gray-800 dark:text-gray-200">
                            {{ $event->nama_event }} - {{ $event->tanggal_event ? \Carbon\Carbon::parse($event->tanggal_event)->translatedFormat('d F Y') : '-' }}
                        </h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            Harga Strip: Rp {{ number_format($hargaStrip, 0, ',', '.') }}
                            @if($event->is_additional_active)
                                | Additional: Rp {{ number_format($hargaAdd, 0, ',', '.') }}
                            @endif
                            @if($sharePercent > 0)
                                | Bagi Hasil Panitia: {{ $sharePercent }}%
                            @endif
                            @if($panitiaPin !== '')
                                | PIN Panitia: <span class="font-semibold text-primary-600 dark:text-primary-400">{{ $panitiaPin }}</span>
                            @endif
                        </p>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    
@if($canUpdateTicket)
                    <!-- TOMBOL EDIT EVENT -->
                    <x-filament::button 
                        wire:click="mountAction('editEvent', { id: {{ $event->id }} })"
                        color="gray"
                        size="sm"
                        icon="heroicon-o-pencil-square"
                        outlined
                    >
                        Edit
                    </x-filament::button>
@endif

                    <!-- TOMBOL EXPORT CSV -->
                    <x-filament::button 
                        wire:click="exportCsv({{ $event->id }})"
                        color="info"
                        size="sm"
                        icon="heroicon-o-arrow-down-tray"
                    >
                        Export CSV
                    </x-filament::button>

@if($canUpdateTicket)
                    <!-- TOMBOL ACTIVE/INACTIVE -->
                    <x-filament::button 
                        wire:click="toggleEventActive({{ $event->id }})"
                        color="{{ $event->is_active ? 'success' : 'gray' }}"
                        size="sm"
                    >
                        {{ $event->is_active ? 'Active' : 'Inactive' }}
                    </x-filament::button>
@else
                    <span class="inline-flex items-center px-2.5 py-1.5 rounded-md text-xs font-semibold {{ $event->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300' : 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-300' }}">
                        {{ $event->is_active ? 'Active' : 'Inactive' }}
                    </span>
@endif

                    <!-- TOMBOL ANTREAN -->
                    <x-filament::button 
                        href="/antrian/{{ $event->slug }}" 
                        tag="a" 
                        target="_blank" 
                        color="gray" 
                        size="sm"
                        icon="heroicon-o-tv"
                        outlined
                    >
                        Antrean
                    </x-filament::button>

                    <!-- TOMBOL LIVE PREVIEW PANITIA -->
                    <x-filament::button 
                        href="{{ $panitiaUrl }}" 
                        tag="a" 
                        target="_blank" 
                        color="warning" 
                        size="sm"
                        icon="heroicon-o-presentation-chart-line"
                    >
                        Live Panitia
                    </x-filament::button>

                    <!-- TOMBOL SALIN LINK PANITIA -->
                    <x-filament::button 
                        type="button"
                        color="gray" 
                        size="sm"
                        icon="heroicon-o-link"
                        outlined
                        data-url="{{ $panitiaUrl }}"
                        data-info="{{ $panitiaUrl . ($panitiaPin ? ' (PIN: ' . $panitiaPin . ')' : '') }}"
                        x-on:click="
                            navigator.clipboard.writeText($el.dataset.url);
                            new FilamentNotification()
                                .title('Link Live Panitia berhasil disalin!')
                                .body($el.dataset.info)
                                .success()
                                .duration(3000)
                                .send();
                        "
                    >
                        Salin Link Panitia
                    </x-filament::button>

@if($canDeleteTicket)
                    <!-- TOMBOL HAPUS EVENT -->
                    <x-filament::button 
                        wire:click="deleteEvent({{ $event->id }})"
                        wire:confirm="Yakin ingin menghapus Event ini beserta seluruh tiketnya? (Folder GDrive dijamin tetap aman)"
                        color="danger"
                        size="sm"
                        icon="heroicon-o-trash"
                        outlined
                    >
                        Hapus
                    </x-filament::button>
@endif

                </div>

            </div>

            <!-- DASHBOARD RINGKASAN REALTIME PER EVENT -->
            <div class="p-4 bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-white/10 grid grid-cols-2 md:grid-cols-4 gap-3">
                <div class="p-3 rounded-xl bg-gray-50 dark:bg-white/5 border border-gray-200/70 dark:border-white/10">
                    <div class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Jumlah Pengunjung</div>
                    <div class="text-xl font-extrabold text-gray-900 dark:text-white mt-1">{{ $totalOrang }} <span class="text-xs font-medium text-gray-500">Orang</span></div>
                    <div class="text-[11px] text-gray-500 dark:text-gray-400 mt-0.5">{{ $totalTransaksi }} Registrasi Antrean</div>
                </div>
                <div class="p-3 rounded-xl bg-gray-50 dark:bg-white/5 border border-gray-200/70 dark:border-white/10">
                    <div class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Jumlah Cetak</div>
                    <div class="text-xl font-extrabold text-gray-900 dark:text-white mt-1">{{ $totalCetak }} <span class="text-xs font-medium text-gray-500">Strip</span></div>
                    <div class="text-[11px] text-gray-500 dark:text-gray-400 mt-0.5">Rp {{ number_format($pendapatanStrip, 0, ',', '.') }}</div>
                </div>
                <div class="p-3 rounded-xl bg-gray-50 dark:bg-white/5 border border-gray-200/70 dark:border-white/10">
                    <div class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Jumlah Additional</div>
                    <div class="text-xl font-extrabold text-gray-900 dark:text-white mt-1">{{ $totalAdditional }} <span class="text-xs font-medium text-gray-500">Add</span></div>
                    <div class="text-[11px] text-gray-500 dark:text-gray-400 mt-0.5">Rp {{ number_format($pendapatanAdd, 0, ',', '.') }}</div>
                </div>
                <div class="p-3 rounded-xl bg-primary-50/60 dark:bg-primary-950/30 border border-primary-200 dark:border-primary-800">
                    <div class="text-xs font-semibold text-primary-600 dark:text-primary-400 uppercase">Total Pendapatan</div>
                    <div class="text-xl font-extrabold text-primary-600 dark:text-primary-400 mt-1">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</div>
                    @if($sharePercent > 0)
                        <div class="text-[11px] font-semibold text-emerald-600 dark:text-emerald-400 mt-0.5">
                            Bagi Hasil Panitia ({{ $sharePercent }}%): Rp {{ number_format($bagiHasilPanitia, 0, ',', '.') }}
                        </div>
                    @else
                        <div class="text-[11px] text-gray-500 dark:text-gray-400 mt-0.5">Update Realtime Otomatis</div>
                    @endif
                </div>
            </div>
            
            <!-- TABEL ANTREAN -->
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-white/10 text-gray-500 dark:text-gray-400 font-semibold">
                        <tr>
                            <th class="p-3 w-10"></th>
                            <th class="p-3">Nama</th>
                            <th class="p-3 text-center">Orang</th>
                            <th class="p-3 text-center">Cetak</th>
                            <th class="p-3 text-center">Additional</th>
                            <th class="p-3">No HP</th>
                            <th class="p-3">Pembayaran</th>
                            <th class="p-3">Antrian</th>
                            <th class="p-3 text-center">Foto</th>
                            <th class="p-3 text-center">Export</th>
                            <th class="p-3 text-center">Print</th>
                            <th class="p-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-white/10">
                        @forelse($event->ticketings as $ticket)
                            <tr class="hover:bg-gray-50 dark:hover:bg-white/5 transition">
                                <td class="p-3">
                                    <input type="checkbox" wire:model.live="selectedTickets" value="{{ $ticket->id }}" class="rounded border-gray-300 text-primary-600 shadow-sm focus:ring-primary-600 cursor-pointer">
                                </td>
                                
                                <!-- KOLOM NAMA + TOMBOL SALIN -->
                                <td class="p-3 text-gray-800 dark:text-gray-200 font-medium">
                                    <div class="inline-flex items-center gap-1.5" x-data="{ copied: false }">
                                        <span>{{ $ticket->nama }}</span>
                                        <button
                                            type="button"
                                            data-nama="{{ $ticket->nama }}"
                                            x-on:click="
                                                navigator.clipboard.writeText($el.dataset.nama);
                                                copied = true;
                                                new FilamentNotification()
                                                    .title('Nama disalin: ' + $el.dataset.nama)
                                                    .success()
                                                    .duration(1500)
                                                    .send();
                                                setTimeout(() => copied = false, 1500);
                                            "
                                            class="text-gray-400 hover:text-primary-600 dark:hover:text-primary-400 transition cursor-pointer p-1 rounded-md hover:bg-gray-100 dark:hover:bg-white/10"
                                            title="Salin Nama"
                                        >
                                            <svg x-show="!copied" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                            </svg>
                                            <svg x-show="copied" x-cloak class="w-4 h-4 text-success-600" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                                
                                <!-- KOLOM ORANG (+ / -) -->
                                <td class="p-3 text-center">
                                    <div class="inline-flex items-center justify-center gap-1.5">
                                        @if($canUpdateTicket)
                                        <button type="button" wire:click="updateCounter({{ $ticket->id }}, 'jumlah', -1)" class="inline-flex items-center justify-center w-6 h-6 rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-gray-200 dark:hover:bg-white/10 transition cursor-pointer">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18 12H6"/></svg>
                                        </button>
                                        @endif
                                        <span class="w-6 text-center font-semibold text-gray-800 dark:text-gray-200">
                                            {{ $ticket->jumlah ?? 0 }}
                                        </span>
                                        @if($canUpdateTicket)
                                        <button type="button" wire:click="updateCounter({{ $ticket->id }}, 'jumlah', 1)" class="inline-flex items-center justify-center w-6 h-6 rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-gray-200 dark:hover:bg-white/10 transition cursor-pointer">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m6-6H6"/></svg>
                                        </button>
                                        @endif
                                    </div>
                                </td>

                                <!-- KOLOM CETAK (+ / -) -->
                                <td class="p-3 text-center">
                                    <div class="inline-flex items-center justify-center gap-1.5">
                                        @if($canUpdateTicket)
                                        <button type="button" wire:click="updateCounter({{ $ticket->id }}, 'cetak', -1)" class="inline-flex items-center justify-center w-6 h-6 rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-gray-200 dark:hover:bg-white/10 transition cursor-pointer">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18 12H6"/></svg>
                                        </button>
                                        @endif
                                        <span class="w-6 text-center font-semibold text-gray-800 dark:text-gray-200">
                                            {{ $ticket->cetak ?? 0 }}
                                        </span>
                                        @if($canUpdateTicket)
                                        <button type="button" wire:click="updateCounter({{ $ticket->id }}, 'cetak', 1)" class="inline-flex items-center justify-center w-6 h-6 rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-gray-200 dark:hover:bg-white/10 transition cursor-pointer">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m6-6H6"/></svg>
                                        </button>
                                        @endif
                                    </div>
                                </td>

                                <!-- KOLOM ADDITIONAL (+ / -) -->
                                <td class="p-3 text-center">
                                    <div class="inline-flex items-center justify-center gap-1.5">
                                        @if($canUpdateTicket)
                                        <button type="button" wire:click="updateCounter({{ $ticket->id }}, 'additional', -1)" class="inline-flex items-center justify-center w-6 h-6 rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-gray-200 dark:hover:bg-white/10 transition cursor-pointer">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18 12H6"/></svg>
                                        </button>
                                        @endif
                                        <span class="w-6 text-center font-semibold text-gray-800 dark:text-gray-200">
                                            {{ $ticket->additional ?? 0 }}
                                        </span>
                                        @if($canUpdateTicket)
                                        <button type="button" wire:click="updateCounter({{ $ticket->id }}, 'additional', 1)" class="inline-flex items-center justify-center w-6 h-6 rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-gray-200 dark:hover:bg-white/10 transition cursor-pointer">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m6-6H6"/></svg>
                                        </button>
                                        @endif
                                    </div>
                                </td>

                                <td class="p-3 text-gray-600 dark:text-gray-400">{{ $ticket->telpon }}</td>
                                <td class="p-3 text-gray-600 dark:text-gray-400 uppercase">{{ $ticket->transaction_type }}</td>
                                <td class="p-3 text-gray-600 dark:text-gray-400 font-bold">{{ $ticket->queue_number }}</td>
                                
                                <td class="p-3 text-center">
                                    <input type="checkbox" wire:click="toggleStatus({{ $ticket->id }}, 'is_foto')" {{ $ticket->is_foto ? 'checked' : '' }} class="rounded border-gray-300 text-green-600 focus:ring-green-600 w-5 h-5 cursor-pointer">
                                </td>
                                <td class="p-3 text-center">
                                    <input type="checkbox" @if($canUpdateTicket) wire:click="toggleStatus({{ $ticket->id }}, 'is_export')" @else disabled @endif {{ $ticket->is_export ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 focus:ring-blue-600 w-5 h-5 {{ $canUpdateTicket ? 'cursor-pointer' : 'cursor-not-allowed opacity-60' }}">
                                </td>
                                <td class="p-3 text-center">
                                    <input type="checkbox" @if($canUpdateTicket) wire:click="toggleStatus({{ $ticket->id }}, 'is_print')" @else disabled @endif {{ $ticket->is_print ? 'checked' : '' }} class="rounded border-gray-300 text-purple-600 focus:ring-purple-600 w-5 h-5 {{ $canUpdateTicket ? 'cursor-pointer' : 'cursor-not-allowed opacity-60' }}">
                                </td>
                                
                                <!-- KOLOM AKSI (ICON BUTTONS) -->
                                <td class="p-3 text-center">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <!-- ICON PANGGIL ANTREAN -->
                                        <div x-data="{ status: '{{ $ticket->status }}' }">
                                            <button
                                                type="button"
                                                x-show="status === 'menunggu'"
                                                title="Panggil Antrean"
                                                x-on:click="
                                                    status = 'dipanggil';
                                                    $wire.panggilAntrean({{ $ticket->id }});
                                                "
                                                class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-sky-600 hover:bg-sky-50 dark:text-sky-400 dark:hover:bg-sky-950/50 transition cursor-pointer"
                                            >
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.114 5.636a9 9 0 010 12.728M16.463 8.288a5.25 5.25 0 010 7.424M6.75 8.25l4.72-4.72a.75.75 0 011.28.53v15.88a.75.75 0 01-1.28.53l-4.72-4.72H4.51c-.88 0-1.704-.507-1.938-1.354A9.01 9.01 0 012.25 12c0-.83.112-1.633.322-2.396C2.806 8.756 3.63 8.25 4.51 8.25H6.75z"/>
                                                </svg>
                                            </button>

                                            <span
                                                x-show="status !== 'menunggu'"
                                                x-cloak
                                                title="Sudah Dipanggil"
                                                class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-emerald-600 dark:text-emerald-400 opacity-75"
                                            >
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                            </span>
                                        </div>

@if($canUpdateTicket)
                                        <!-- ICON EDIT TIAP LIST -->
                                        <a
                                            href="{{ url('/admin/ticketings/' . $ticket->id . '/edit') }}"
                                            title="Edit Data"
                                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-amber-600 hover:bg-amber-50 dark:text-amber-400 dark:hover:bg-amber-950/50 transition"
                                        >
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/>
                                            </svg>
                                        </a>
@endif

@if($canDeleteTicket)
                                        <!-- ICON HAPUS TIAP LIST -->
                                        <button
                                            type="button"
                                            title="Hapus Data"
                                            wire:click="deleteTicket({{ $ticket->id }})"
                                            wire:confirm="Yakin ingin menghapus antrean {{ $ticket->nama }}?"
                                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-rose-600 hover:bg-rose-50 dark:text-rose-400 dark:hover:bg-rose-950/50 transition cursor-pointer"
                                        >
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
                                            </svg>
                                        </button>
@endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="12" class="p-6 text-center text-gray-500">Belum ada antrean di event ini.</td>
                            </tr>
                        @endforelse
                    </tbody>

                    <!-- FOOTER TOTAL DI BAGIAN BAWAH LIST -->
                    <tfoot class="bg-gray-50 dark:bg-white/5 border-t-2 border-gray-200 dark:border-white/10 font-bold text-gray-800 dark:text-gray-200">
                        <tr>
                            <td colspan="2" class="p-3 text-right uppercase text-xs tracking-wider text-gray-500 dark:text-gray-400">
                                Total Keseluruhan:
                            </td>
                            <td class="p-3 text-center">
                                {{ $totalOrang }} Orang
                            </td>
                            <td class="p-3 text-center">
                                {{ $totalCetak }} Cetak
                            </td>
                            <td class="p-3 text-center">
                                {{ $totalAdditional }} Add
                            </td>
                            <td colspan="7" class="p-3 text-right">
                                <span class="text-xs font-normal text-gray-500 dark:text-gray-400 mr-2">
                                    Total Pendapatan:
                                </span>
                                <span class="text-base font-extrabold text-primary-600 dark:text-primary-400">
                                    Rp {{ number_format($totalPendapatan, 0, ',', '.') }}
                                </span>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    @endforeach

    <x-filament-actions::modals />
</x-filament-panels::page>