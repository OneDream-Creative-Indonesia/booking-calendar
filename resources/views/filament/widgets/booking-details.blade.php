<div class="grid grid-cols-1 gap-6">
    <x-filament::card>
        <h2 class="text-lg font-semibold">Booking Information</h2>
        <p><strong>Name:</strong> {{ $booking->name }}</p>
        <p><strong>WhatsApp:</strong> {{ $booking->whatsapp }}</p>
    </x-filament::card>

    <x-filament::card>
        <h2 class="text-lg font-semibold">Schedule</h2>
        <p><strong>Date:</strong> {{ $booking->date }}</p>
        <p><strong>Time:</strong> {{ $booking->time }}</p>
        <p><strong>Status:</strong> {{ $booking->status }}</p>
    </x-filament::card>
</div>
