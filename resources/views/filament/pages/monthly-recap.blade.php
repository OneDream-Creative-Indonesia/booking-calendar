<x-filament-panels::page>
    {{-- Pilih periode & tarik data --}}
    <x-filament::section>
        <div class="flex flex-wrap items-end gap-3">
            <label class="text-sm">
                <span class="block mb-1 font-medium">Bulan</span>
                <x-filament::input.wrapper>
                    <x-filament::input.select wire:model="selectedMonth">
                        @foreach($this->getMonthOptions() as $num => $label)
                            <option value="{{ $num }}">{{ $label }}</option>
                        @endforeach
                    </x-filament::input.select>
                </x-filament::input.wrapper>
            </label>
            <label class="text-sm">
                <span class="block mb-1 font-medium">Tahun</span>
                <x-filament::input.wrapper>
                    <x-filament::input.select wire:model="selectedYear">
                        @foreach($this->getYearOptions() as $y)
                            <option value="{{ $y }}">{{ $y }}</option>
                        @endforeach
                    </x-filament::input.select>
                </x-filament::input.wrapper>
            </label>
            <x-filament::button wire:click="pullData" icon="heroicon-m-cloud-arrow-down">Tarik Data</x-filament::button>
            <x-filament::button wire:click="toggleInfographic" color="gray" icon="heroicon-m-chart-bar">
                {{ $showInfographic ? 'Sembunyikan Infografis' : 'Lihat Infografis' }}
            </x-filament::button>
            <span class="ml-auto text-sm text-gray-500">Periode ditampilkan: <strong>{{ $this->getPeriodLabel() }}</strong></span>
        </div>
    </x-filament::section>

    {{-- Infografis --}}
    @if($showInfographic)
        @php
            $info = $this->getInfographic();
            $maxPkg = max(1, collect($info['packages'])->max('bookings') ?? 1);
            $maxRev = max(1, collect($info['packages'])->max('revenue') ?? 1);
            $maxPeople = max(1, collect($info['packages'])->max('people') ?? 1);
        @endphp
        <x-filament::section heading="Infografis {{ $this->getPeriodLabel() }}" collapsible>
            <div class="grid grid-cols-2 gap-4 md:grid-cols-4">
                @foreach([
                    ['Total Pengunjung', number_format($info['visitors'], 0, ',', '.') . ' orang'],
                    ['Jumlah Booking', $info['bookings']],
                    ['Total Pendapatan', \App\Services\BookingReportService::rupiah($info['revenue'])],
                    ['Dibatalkan', $info['cancelled']],
                ] as [$label, $value])
                    <div class="p-4 rounded-xl ring-1 ring-gray-950/5 dark:ring-white/10">
                        <div class="text-xs text-gray-500">{{ $label }}</div>
                        <div class="mt-1 text-xl font-bold">{{ $value }}</div>
                    </div>
                @endforeach
            </div>

            <div class="grid gap-6 mt-6 md:grid-cols-3">
                @foreach([
                    ['Rekap Paket Dipilih (booking)', 'bookings', $maxPkg, fn ($v) => $v . ' booking'],
                    ['Rekap Jumlah Orang per Paket', 'people', $maxPeople, fn ($v) => $v . ' orang'],
                    ['Rekap Pendapatan per Paket', 'revenue', $maxRev, fn ($v) => \App\Services\BookingReportService::rupiah($v)],
                ] as [$title, $key, $max, $fmt])
                    <div>
                        <h4 class="mb-3 text-sm font-semibold">{{ $title }}</h4>
                        @forelse($info['packages'] as $p)
                            <div class="mb-3">
                                <div class="flex justify-between text-xs">
                                    <span>{{ $p['name'] }}</span><span class="font-medium">{{ $fmt($p[$key]) }}</span>
                                </div>
                                <div class="h-2 mt-1 bg-gray-200 rounded dark:bg-gray-700">
                                    <div class="h-2 rounded bg-primary-500" style="width: {{ round($p[$key] / $max * 100) }}%; background:#f59e0b"></div>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500">Belum ada data.</p>
                        @endforelse
                    </div>
                @endforeach
            </div>
        </x-filament::section>
    @endif

    {{-- Tabel booking (filter, kolom bisa di-hide, centang untuk hapus) --}}
    {{ $this->table }}

    {{-- Arsip XML bulanan (disembunyikan secara default) --}}
    @php $recaps = $this->getRecapFiles(); @endphp
    @if(count($recaps))
        <x-filament::section heading="Arsip Rekap XML" collapsible collapsed>
            <div class="grid grid-cols-1 gap-3 md:grid-cols-2 lg:grid-cols-3">
                @foreach($recaps as $recap)
                    <div class="flex items-center justify-between p-3 rounded-xl ring-1 ring-gray-950/5 dark:ring-white/10">
                        <div>
                            <div class="font-medium">{{ $recap['title'] }}</div>
                            <div class="text-xs text-gray-500">{{ $recap['size'] }}</div>
                        </div>
                        <x-filament::button size="sm" wire:click="downloadRecap('{{ $recap['path'] }}')" icon="heroicon-m-arrow-down-tray">XML</x-filament::button>
                    </div>
                @endforeach
            </div>
        </x-filament::section>
    @endif
</x-filament-panels::page>
