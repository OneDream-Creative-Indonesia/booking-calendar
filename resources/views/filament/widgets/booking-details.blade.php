@php
    // Menyusun format teks yang akan di-copy
    $copyText = "Booking Information\n" .
        "Name: " . $booking->name . "\n" .
        "Paket: " . $booking->package->title . "\n" .
        "WhatsApp: " . $booking->whatsapp . "\n" .
        "Background: " . $booking->background->name . "\n" .
        "Jumlah Orang: " . $booking->people_count . "\n" .
        "Total Harga: " . $booking->price . "\n" .
        "Voucher: " . ($booking->voucher->code_voucher ?? '-') . "\n\n" .
        "Schedule\n" .
        "Date: " . $booking->date . "\n" .
        "Time: " . $booking->time . "\n" .
        "Status: " . $booking->status;
@endphp

<div x-data="{ 
    textToCopy: @js($copyText),
    copied: false,
    copyToClipboard() {
        navigator.clipboard.writeText(this.textToCopy);
        this.copied = true;
        setTimeout(() => this.copied = false, 2000); // Kembali ke teks awal setelah 2 detik
    }
}">
    <div class="grid grid-cols-1 gap-6">
        <x-filament::card>
            <h2 class="text-lg font-semibold">Booking Information</h2>
            <p><strong>Name:</strong> {{ $booking->name }}</p>
            <p><strong>Paket:</strong> {{ $booking->package->title }}</p>
            <p><strong>WhatsApp:</strong> {{ $booking->whatsapp }}</p>
            <p><strong>Background:</strong> {{ $booking->background->name }}</p>
            <p><strong>Jumlah Orang:</strong> {{ $booking->people_count }}</p>
            <p><strong>Total Harga:</strong> {{ $booking->price }}</p>
            <p><strong>Voucher:</strong> {{ $booking->voucher->code_voucher ?? '-' }}</p>
        </x-filament::card>

        <x-filament::card>
            <h2 class="text-lg font-semibold">Schedule</h2>
            <p><strong>Date:</strong> {{ $booking->date }}</p>
            <p><strong>Time:</strong> {{ $booking->time }}</p>
            <p><strong>Status:</strong> {{ $booking->status }}</p>
        </x-filament::card>
    </div>

    <!-- Tombol Copy di sebelah kanan bawah -->
    <div class="mt-6 flex justify-end">
        <x-filament::button 
            color="gray" 
            icon="heroicon-m-clipboard-document" 
            x-on:click="copyToClipboard"
        >
            <span x-text="copied ? 'Copied!' : 'Copy Info'"></span>
        </x-filament::button>
    </div>
</div>