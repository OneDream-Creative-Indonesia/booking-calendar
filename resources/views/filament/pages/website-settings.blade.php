<x-filament-panels::page>
    <form wire:submit="save">
        {{ $this->form }}

        <div style="margin-top:1.5rem">
            <x-filament::button type="submit" icon="heroicon-m-check">Simpan Pengaturan</x-filament::button>
            <x-filament::button tag="a" href="/" target="_blank" color="gray" icon="heroicon-m-arrow-top-right-on-square">Lihat Website</x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
