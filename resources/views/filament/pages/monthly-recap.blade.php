<x-filament-panels::page>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($this->getRecapFiles() as $recap)
            <x-filament::section>
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold">{{ $recap['title'] }}</h3>
                        <p class="text-sm text-gray-500">Ukuran: {{ $recap['size'] }}</p>
                    </div>
                    
                    <x-filament::button 
                        wire:click="downloadRecap('{{ $recap['path'] }}')" 
                        icon="heroicon-m-arrow-down-tray"
                        color="primary">
                        Unduh XML
                    </x-filament::button>
                </div>
            </x-filament::section>
        @empty
            <div class="col-span-full p-6 text-center bg-white rounded-xl shadow-sm dark:bg-gray-900 border border-gray-200 dark:border-white/10">
                <div class="flex flex-col items-center justify-center">
                    <x-heroicon-o-document-text class="w-12 h-12 text-gray-400 mb-2"/>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Belum Ada Rekap</h3>
                    <p class="text-gray-500">Data rekap bulanan (E-Statement) akan muncul di sini secara otomatis setiap awal bulan.</p>
                </div>
            </div>
        @endforelse
    </div>
</x-filament-panels::page>