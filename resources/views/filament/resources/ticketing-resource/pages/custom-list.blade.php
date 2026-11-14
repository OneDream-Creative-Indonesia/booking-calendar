<x-filament-panels::page>
<div wire:poll.5s>
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
                            {{ $event->nama_event }} - {{ \Carbon\Carbon::parse($event->tanggal_event)->translatedFormat('d F Y') }}
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
                    <div x-data>
                        <x-filament::button 
                            type="button"
                            color="gray" 
                            size="sm"
                            icon="heroicon-o-link"
                            outlined
                            x-on:click="
                                navigator.clipboard.writeText(@js($panitiaUrl));
                                new FilamentNotification()
                                    .title('Link Live Panitia berhasil disalin!')
                                    .body(@js($panitiaUrl . ($panitiaPin ? ' (PIN: ' . $panitiaPin . ')' : '')))
                                    .success()
                                    .duration(3000)
                                    .send();
                            "
                        >
                            Salin Link Panitia
                        </x-filament::button>
                    </div>

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
                                            x-on:click="
                                                navigator.clipboard.writeText(@js($ticket->nama));
                                                copied = true;
                                                new FilamentNotification()
                                                    .title('Nama disalin: ' + @js($ticket->nama))
                                                    .success()
                                                    .duration(1500)
                                                    .send();
                                                setTimeout(() => copied = false, 1500);
                                            "
                                            class="text-gray-400 hover:text-primary-600 dark:hover:text-primary-400 transition cursor-pointer p-1 rounded-md hover:bg-gray-100 dark:hover:bg-white/10"
                                            title="Salin Nama"
                                        >
                                            <x-filament::icon
                                                x-show="!copied"
                                                icon="heroicon-o-clipboard-document"
                                                class="w-4 h-4"
                                            />
                                            <x-filament::icon
                                                x-show="copied"
                                                x-cloak
                                                icon="heroicon-o-check"
                                                class="w-4 h-4 text-success-600"
                                            />
                                        </button>
                                    </div>
                                </td>
                                
                                <!-- KOLOM ORANG (+ / -) -->
                                <td class="p-3 text-center">
                                    <div class="inline-flex items-center justify-center gap-1.5">
                                        @if($canUpdateTicket)
                                        <x-filament::icon-button
                                            icon="heroicon-m-minus"
                                            color="gray"
                                            size="xs"
                                            wire:click="updateCounter({{ $ticket->id }}, 'jumlah', -1)"
                                        />
                                        @endif
                                        <span class="w-6 text-center font-semibold text-gray-800 dark:text-gray-200">
                                            {{ $ticket->jumlah ?? 0 }}
                                        </span>
                                        @if($canUpdateTicket)
                                        <x-filament::icon-button
                                            icon="heroicon-m-plus"
                                            color="gray"
                                            size="xs"
                                            wire:click="updateCounter({{ $ticket->id }}, 'jumlah', 1)"
                                        />
                                        @endif
                                    </div>
                                </td>

                                <!-- KOLOM CETAK (+ / -) -->
                                <td class="p-3 text-center">
                                    <div class="inline-flex items-center justify-center gap-1.5">
                                        @if($canUpdateTicket)
                                        <x-filament::icon-button
                                            icon="heroicon-m-minus"
                                            color="gray"
                                            size="xs"
                                            wire:click="updateCounter({{ $ticket->id }}, 'cetak', -1)"
                                        />
                                        @endif
                                        <span class="w-6 text-center font-semibold text-gray-800 dark:text-gray-200">
                                            {{ $ticket->cetak ?? 0 }}
                                        </span>
                                        @if($canUpdateTicket)
                                        <x-filament::icon-button
                                            icon="heroicon-m-plus"
                                            color="gray"
                                            size="xs"
                                            wire:click="updateCounter({{ $ticket->id }}, 'cetak', 1)"
                                        />
                                        @endif
                                    </div>
                                </td>

                                <!-- KOLOM ADDITIONAL (+ / -) -->
                                <td class="p-3 text-center">
                                    <div class="inline-flex items-center justify-center gap-1.5">
                                        @if($canUpdateTicket)
                                        <x-filament::icon-button
                                            icon="heroicon-m-minus"
                                            color="gray"
                                            size="xs"
                                            wire:click="updateCounter({{ $ticket->id }}, 'additional', -1)"
                                        />
                                        @endif
                                        <span class="w-6 text-center font-semibold text-gray-800 dark:text-gray-200">
                                            {{ $ticket->additional ?? 0 }}
                                        </span>
                                        @if($canUpdateTicket)
                                        <x-filament::icon-button
                                            icon="heroicon-m-plus"
                                            color="gray"
                                            size="xs"
                                            wire:click="updateCounter({{ $ticket->id }}, 'additional', 1)"
                                        />
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
                                            <div x-show="status === 'menunggu'">
                                                <x-filament::icon-button
                                                    icon="heroicon-o-speaker-wave"
                                                    color="info"
                                                    size="sm"
                                                    tooltip="Panggil Antrean"
                                                    x-on:click="
                                                        status = 'dipanggil';
                                                        $wire.panggilAntrean({{ $ticket->id }});
                                                    "
                                                />
                                            </div>

                                            <div x-show="status !== 'menunggu'" x-cloak>
                                                <x-filament::icon-button
                                                    icon="heroicon-o-check-circle"
                                                    color="success"
                                                    size="sm"
                                                    tooltip="Sudah Dipanggil"
                                                    disabled
                                                />
                                            </div>
                                        </div>

@if($canUpdateTicket)
                                        <!-- ICON EDIT TIAP LIST -->
                                        <x-filament::icon-button
                                            href="{{ \App\Filament\Resources\TicketingResource::getUrl('edit', ['record' => $ticket->id]) }}"
                                            tag="a"
                                            icon="heroicon-o-pencil-square"
                                            color="warning"
                                            size="sm"
                                            tooltip="Edit Data"
                                        />
@endif

@if($canDeleteTicket)
                                        <!-- ICON HAPUS TIAP LIST -->
                                        <x-filament::icon-button
                                            icon="heroicon-o-trash"
                                            color="danger"
                                            size="sm"
                                            tooltip="Hapus Data"
                                            wire:click="deleteTicket({{ $ticket->id }})"
                                            wire:confirm="Yakin ingin menghapus antrean {{ $ticket->nama }}?"
                                        />
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
</div>

    <x-filament-actions::modals />
</x-filament-panels::page>