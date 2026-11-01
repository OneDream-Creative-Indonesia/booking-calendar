<x-filament-panels::page>

    <!-- Tombol Bulk Delete -->
    @if(count($selectedTickets) > 0)
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
            $totalOrang = $event->ticketings->sum('jumlah');
            $totalCetak = $event->ticketings->sum('cetak');
            $totalAdditional = $event->ticketings->sum('additional');
            
            $hargaStrip = (float) ($event->strip_price ?? 0);
            $hargaAdd = $event->is_additional_active ? (float) ($event->additional_price ?? 0) : 0;
            
            $totalPendapatan = ($hargaStrip * $totalCetak) + ($hargaAdd * $totalAdditional);
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
                        </p>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    
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

                    <!-- TOMBOL EXPORT CSV -->
                    <x-filament::button 
                        wire:click="exportCsv({{ $event->id }})"
                        color="info"
                        size="sm"
                        icon="heroicon-o-arrow-down-tray"
                    >
                        Export CSV
                    </x-filament::button>

                    <!-- TOMBOL ACTIVE/INACTIVE -->
                    <x-filament::button 
                        wire:click="toggleEventActive({{ $event->id }})"
                        color="{{ $event->is_active ? 'success' : 'gray' }}"
                        size="sm"
                    >
                        {{ $event->is_active ? 'Active' : 'Inactive' }}
                    </x-filament::button>

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
                                        <x-filament::icon-button
                                            icon="heroicon-m-minus"
                                            color="gray"
                                            size="xs"
                                            wire:click="updateCounter({{ $ticket->id }}, 'jumlah', -1)"
                                        />
                                        <span class="w-6 text-center font-semibold text-gray-800 dark:text-gray-200">
                                            {{ $ticket->jumlah ?? 0 }}
                                        </span>
                                        <x-filament::icon-button
                                            icon="heroicon-m-plus"
                                            color="gray"
                                            size="xs"
                                            wire:click="updateCounter({{ $ticket->id }}, 'jumlah', 1)"
                                        />
                                    </div>
                                </td>

                                <!-- KOLOM CETAK (+ / -) -->
                                <td class="p-3 text-center">
                                    <div class="inline-flex items-center justify-center gap-1.5">
                                        <x-filament::icon-button
                                            icon="heroicon-m-minus"
                                            color="gray"
                                            size="xs"
                                            wire:click="updateCounter({{ $ticket->id }}, 'cetak', -1)"
                                        />
                                        <span class="w-6 text-center font-semibold text-gray-800 dark:text-gray-200">
                                            {{ $ticket->cetak ?? 0 }}
                                        </span>
                                        <x-filament::icon-button
                                            icon="heroicon-m-plus"
                                            color="gray"
                                            size="xs"
                                            wire:click="updateCounter({{ $ticket->id }}, 'cetak', 1)"
                                        />
                                    </div>
                                </td>

                                <!-- KOLOM ADDITIONAL (+ / -) -->
                                <td class="p-3 text-center">
                                    <div class="inline-flex items-center justify-center gap-1.5">
                                        <x-filament::icon-button
                                            icon="heroicon-m-minus"
                                            color="gray"
                                            size="xs"
                                            wire:click="updateCounter({{ $ticket->id }}, 'additional', -1)"
                                        />
                                        <span class="w-6 text-center font-semibold text-gray-800 dark:text-gray-200">
                                            {{ $ticket->additional ?? 0 }}
                                        </span>
                                        <x-filament::icon-button
                                            icon="heroicon-m-plus"
                                            color="gray"
                                            size="xs"
                                            wire:click="updateCounter({{ $ticket->id }}, 'additional', 1)"
                                        />
                                    </div>
                                </td>

                                <td class="p-3 text-gray-600 dark:text-gray-400">{{ $ticket->telpon }}</td>
                                <td class="p-3 text-gray-600 dark:text-gray-400 uppercase">{{ $ticket->transaction_type }}</td>
                                <td class="p-3 text-gray-600 dark:text-gray-400 font-bold">{{ $ticket->queue_number }}</td>
                                
                                <td class="p-3 text-center">
                                    <input type="checkbox" wire:click="toggleStatus({{ $ticket->id }}, 'is_foto')" {{ $ticket->is_foto ? 'checked' : '' }} class="rounded border-gray-300 text-green-600 focus:ring-green-600 cursor-pointer w-5 h-5">
                                </td>
                                <td class="p-3 text-center">
                                    <input type="checkbox" wire:click="toggleStatus({{ $ticket->id }}, 'is_export')" {{ $ticket->is_export ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 focus:ring-blue-600 cursor-pointer w-5 h-5">
                                </td>
                                <td class="p-3 text-center">
                                    <input type="checkbox" wire:click="toggleStatus({{ $ticket->id }}, 'is_print')" {{ $ticket->is_print ? 'checked' : '' }} class="rounded border-gray-300 text-purple-600 focus:ring-purple-600 cursor-pointer w-5 h-5">
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

                                        <!-- ICON EDIT TIAP LIST -->
                                        <x-filament::icon-button
                                            href="{{ \App\Filament\Resources\TicketingResource::getUrl('edit', ['record' => $ticket->id]) }}"
                                            tag="a"
                                            icon="heroicon-o-pencil-square"
                                            color="warning"
                                            size="sm"
                                            tooltip="Edit Data"
                                        />

                                        <!-- ICON HAPUS TIAP LIST -->
                                        <x-filament::icon-button
                                            icon="heroicon-o-trash"
                                            color="danger"
                                            size="sm"
                                            tooltip="Hapus Data"
                                            wire:click="deleteTicket({{ $ticket->id }})"
                                            wire:confirm="Yakin ingin menghapus antrean {{ $ticket->nama }}?"
                                        />
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