<div style="border-radius: 16px; padding: 1.25rem; box-shadow: 0 4px 15px -3px rgba(0,0,0,0.05); display: flex; flex-direction: column; justify-content: space-between; height: 100%; gap: 1rem;" 
     class="project-card-container bg-white border border-gray-100 dark:bg-gray-900 dark:border-gray-800">

    <!-- Header: Type & Time Remaining -->
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <span style="font-size: 0.75rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em;" 
              class="text-blue-600 dark:text-blue-500">
            {{ $getRecord()->type }}
        </span>
        <span style="font-size: 0.7rem; font-weight: 700; padding: 4px 8px; border-radius: 6px; text-transform: uppercase;" 
              class="text-emerald-600 bg-emerald-50 dark:bg-emerald-500/10 dark:text-emerald-400">
            {{ $getRecord()->time_remaining }}
        </span>
    </div>

    <!-- Main Info: Name & ID -->
    <div style="margin-top: 0.25rem;">
        <h3 style="font-size: 1.25rem; font-weight: 700; margin: 0; line-height: 1.2;" 
            class="text-gray-900 dark:text-white">
            {{ $getRecord()->name }}
        </h3>
        <p style="font-size: 0.8rem; margin-top: 4px; font-weight: 500;" 
           class="text-gray-500 dark:text-gray-400">
            ID: <span style="font-family: monospace; text-transform: uppercase;">{{ $getRecord()->project_code }}</span>
        </p>
    </div>

    <!-- Stats Pill: Photos & Date -->
    <div style="border-radius: 12px; padding: 8px 12px; display: inline-flex; align-items: center; gap: 16px; margin-top: 8px; width: max-content;" 
         class="bg-gray-50 border border-gray-100 dark:bg-gray-800/50 dark:border-gray-700/50">
        
        <div style="display: flex; align-items: center; gap: 6px;" class="text-gray-500 dark:text-gray-400">
            <x-heroicon-o-photo style="width: 18px; height: 18px;" />
            <span style="font-size: 0.85rem; font-weight: 700;">{{ is_array($getRecord()->photos) ? count($getRecord()->photos) : 0 }}</span>
        </div>
        
        <!-- Garis Pemisah (Divider) -->
        <div style="width: 1px; height: 16px;" class="bg-gray-200 dark:bg-gray-700"></div>
        
        <div style="display: flex; align-items: center; gap: 6px;" class="text-gray-500 dark:text-gray-400">
            <x-heroicon-o-clock style="width: 18px; height: 18px;" />
            <span style="font-size: 0.85rem; font-weight: 700;">{{ $getRecord()->created_at->format('d M') }}</span>
        </div>
    </div>

    <!-- Action Buttons Row -->
    <div style="display: flex; align-items: center; gap: 8px; margin-top: 12px;">
        
        <!-- Tombol Copy Link (Kuning) - URL SUDAH DIPERBARUI KE /?album= -->
        <button 
            x-data="{ 
                link: '{{ url('/?album=' . $getRecord()->project_code) }}', 
                copied: false 
            }" 
            x-on:click.stop.prevent="
                window.navigator.clipboard.writeText(link);
                copied = true;
                setTimeout(() => copied = false, 2000);
            "
            style="flex: 1; background-color: #FFD700; color: #000; font-weight: 700; font-size: 0.875rem; border-radius: 10px; padding: 10px 0; display: flex; justify-content: center; align-items: center; cursor: pointer; border: none; transition: background-color 0.2s;"
            onmouseover="this.style.backgroundColor='#F2CC00'"
            onmouseout="this.style.backgroundColor='#FFD700'"
        >
            <x-heroicon-o-square-2-stack style="width: 18px; height: 18px; margin-right: 6px;" x-show="!copied" />
            <x-heroicon-o-check-circle style="width: 18px; height: 18px; margin-right: 6px; color: #15803d;" x-show="copied" x-cloak />
            <span x-text="copied ? 'Tersalin!' : 'Link'"></span>
        </button>
        
        <!-- Tombol Edit (Abu-abu -> Hitam di Dark Mode) -->
        <button 
            wire:click.stop.prevent="mountTableAction('edit', '{{ $getRecord()->getKey() }}')"
            style="width: 42px; height: 42px; border-radius: 10px; display: flex; justify-content: center; align-items: center; cursor: pointer; transition: all 0.2s;"
            class="bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-700"
        >
            <x-heroicon-o-pencil style="width: 20px; height: 20px;" />
        </button>

        <!-- Tombol Delete (Merah) -->
        <button 
            wire:click.stop.prevent="mountTableAction('delete', '{{ $getRecord()->getKey() }}')"
            style="width: 42px; height: 42px; border-radius: 10px; display: flex; justify-content: center; align-items: center; cursor: pointer; transition: all 0.2s;"
            class="bg-red-50 border border-red-100 text-red-500 hover:bg-red-100 dark:bg-red-900/20 dark:border-red-900/50 dark:text-red-400 dark:hover:bg-red-900/40"
        >
            <x-heroicon-o-trash style="width: 20px; height: 20px;" />
        </button>

    </div>
</div>

{{-- CSS Hack super agresif untuk menyembunyikan duplikat action bawaan dari tabel grid Filament --}}
<style>
    /* Menyembunyikan elemen apapun (bungkusan action) yang muncul tepat setelah card kita */
    .project-card-container ~ div {
        display: none !important;
    }
    /* Pencarian kelas universal untuk action column Filament */
    .fi-ta-grid [class*="actions"] {
        display: none !important;
    }
</style>