<div class="flex flex-col items-center justify-center min-h-screen p-4 bg-gray-50 dark:bg-gray-900">
    <section class="flex flex-col items-center justify-center w-full p-8 text-center bg-white rounded-xl shadow-lg dark:bg-gray-800 max-w-2xl mx-auto">
        
        <div class="flex justify-center w-full mb-6">
            <img src="{{ asset('img/Pinpin-01.png') }}" alt="Mascot Snap Fun" class="h-32 object-contain">
        </div>
        
        <h1 class="text-3xl font-extrabold text-gray-800 dark:text-gray-100 mb-2">
            Booking Berhasil!
        </h1>
        
        <p class="text-gray-600 dark:text-gray-400 mb-6">
            Halo <span class="font-bold">{{ explode(' - ', $albumData->name)[0] }}</span>, data booking studio kamu telah tersimpan. Silakan kirimkan bukti pembayaran DP ke admin kami via WhatsApp.
        </p>

        @php
            $galleryUrl = ($albumData->drive_link && str_starts_with($albumData->drive_link, 'http'))
                ? $albumData->drive_link
                : 'https://send.snapfunstudio.id/gallery/' . $album_id;
        @endphp

        <div class="bg-gray-50 border border-gray-200 rounded-2xl p-6 w-full max-w-sm mx-auto shadow-sm flex flex-col items-center">
            <p class="text-sm font-bold text-gray-700 uppercase tracking-wide mb-3">
                QR Code Hasil Foto
            </p>

            <div class="bg-white p-2 rounded-xl border border-gray-100 mb-3 inline-block">
                <img 
                    src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&margin=8&data={{ urlencode($galleryUrl) }}" 
                    alt="QR Code Galeri" 
                    class="w-48 h-48 object-contain"
                >
            </div>

            <p class="text-sm text-gray-500 mb-4">
                ID Folder: <strong class="font-mono text-gray-900 text-base">{{ $album_id }}</strong>
            </p>

            <a 
                href="{{ $galleryUrl }}" 
                target="_blank" 
                rel="noopener noreferrer"
                class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 bg-blue-700 hover:bg-blue-800 text-white font-bold rounded-lg transition-colors shadow-sm"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                </svg>
                Buka Link Hasil Foto
            </a>
        </div>
        
        <a href="/" class="mt-6 text-sm text-blue-600 hover:underline">Kembali ke Beranda</a>
    </section>
</div>