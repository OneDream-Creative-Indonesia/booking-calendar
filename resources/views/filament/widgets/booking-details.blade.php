<div class="grid grid-cols-1 gap-6">
    <x-filament::card>
        <h2 class="text-lg font-semibold">Booking Information</h2>
        <p><strong>Name:</strong> {{ $booking->name }}</p>
        <p><strong>Paket:</strong> {{ $booking->package->title }}</p>
        <p><strong>WhatsApp:</strong> {{ $booking->whatsapp }}</p>
        <p><strong>Background:</strong> {{ $booking->background->name }}</p>
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
