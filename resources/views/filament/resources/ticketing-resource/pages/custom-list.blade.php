<x-filament-panels::page>

    <!-- Tombol Bulk Delete -->
    @if(count($selectedTickets) > 0)
        <div class="p-4 bg-white dark:bg-gray-900 shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10 rounded-xl flex items-center justify-between">
            <span class="font-bold text-gray-700 dark:text-gray-200">{{ count($selectedTickets) }} Tiket Terpilih</span>
            <x-filament::button wire:click="deleteSelected" color="danger" icon="heroicon-o-trash">
                Hapus Terpilih
            </x-filament::button>
        </div>
    @endif

    <!-- LOOPING SEMUA EVENT -->
    @foreach($this->events as $event)
        <div class="bg-white dark:bg-gray-900 shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10 rounded-xl overflow-hidden mb-6">
            
            <!-- JUDUL EVENT, ACTIVE TOGGLE, TOMBOL ANTREAN, & TOMBOL HAPUS -->
            <div class="p-4 bg-gray-50 dark:bg-white/5 border-b border-gray-200 dark:border-white/10 flex items-center justify-between">
                
                <div class="flex items-center gap-3">
                    <input type="checkbox" wire:click="toggleSelectAll({{ $event->id }})" class="rounded border-gray-300 text-primary-600 shadow-sm focus:ring-primary-600 cursor-pointer w-4 h-4">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-gray-200">
                        {{ $event->nama_event }} - {{ \Carbon\Carbon::parse($event->tanggal_event)->translatedFormat('d F Y') }}
                    </h3>
                </div>

                <div class="flex items-center gap-3">
                    
                    <!-- TOMBOL ACTIVE/INACTIVE (BAWAAN FILAMENT) -->
                    <x-filament::button 
                        wire:click="toggleEventActive({{ $event->id }})"
                        color="{{ $event->is_active ? 'success' : 'gray' }}"
                        size="sm"
                    >
                        {{ $event->is_active ? 'Active' : 'Inactive' }}
                    </x-filament::button>

                    <!-- TOMBOL ANTREAN KHUSUS PER EVENT -->
                    <a href="/antrian/{{ $event->slug }}" target="_blank" class="inline-flex items-center justify-center font-medium tracking-tight rounded-lg gap-1.5 px-3 py-1.5 text-sm shadow-sm bg-blue-600 text-white hover:bg-blue-500 focus:ring-blue-600 transition">
                        Antrean 📺
                    </a>

                    <!-- TOMBOL HAPUS EVENT (BAWAAN FILAMENT) -->
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
            
            <!-- TABEL HEADER -->
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-white/10 text-gray-500 dark:text-gray-400 font-semibold">
                        <tr>
                            <th class="p-3 w-10"></th>
                            <th class="p-3">Nama</th>
                            <th class="p-3">Orang</th>
                            <th class="p-3">Cetak</th>
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
                                <td class="p-3 text-gray-800 dark:text-gray-200">{{ $ticket->nama }}</td>
                                <td class="p-3 text-gray-600 dark:text-gray-400">{{ $ticket->jumlah }}</td>
                                <td class="p-3 text-gray-600 dark:text-gray-400">{{ $ticket->cetak }}</td>
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
                                
                                <td class="p-3 text-center">
                                    <!-- TOMBOL PANGGIL ANTREAN (OPTIMISTIC / INSTAN 0ms) -->
                                    <div x-data="{ status: '{{ $ticket->status }}' }">
                                        <button 
                                            x-show="status === 'menunggu'"
                                            type="button"
                                            x-on:click="
                                                status = 'dipanggil';
                                                $wire.panggilAntrean({{ $ticket->id }});
                                            "
                                            class="inline-flex items-center justify-center font-semibold rounded-lg gap-1.5 px-3 py-1.5 text-sm bg-primary-600 text-white hover:bg-primary-500 shadow-sm transition active:scale-95 cursor-pointer"
                                        >
                                            Panggil
                                        </button>

                                        <span 
                                            x-show="status !== 'menunggu'" 
                                            x-cloak
                                            class="bg-gray-100 text-gray-600 border border-gray-200 px-3 py-1.5 rounded-lg text-xs font-bold dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 inline-block"
                                        >
                                            Dipanggil
                                        </span>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="p-6 text-center text-gray-500">Belum ada antrean di event ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endforeach

</x-filament-panels::page>