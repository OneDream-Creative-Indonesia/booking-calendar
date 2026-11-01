<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Berhasil - Snapfun Studio</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen p-4">

    <section class="flex flex-col items-center justify-center w-full p-8 text-center bg-white rounded-2xl shadow-xl max-w-xl mx-auto border border-gray-100">
        
        <div class="flex justify-center w-full mb-6">
            <img src="{{ asset('img/Pinpin-01.png') }}" alt="Mascot Snap Fun" class="h-28 object-contain">
        </div>
        
        <h1 class="text-3xl font-extrabold text-gray-800 mb-2">
            Booking Berhasil!
        </h1>
        
        <p class="text-gray-600 mb-6 text-sm">
            Halo <span class="font-bold">{{ explode(' - ', $album->name)[0] }}</span>, data booking studio kamu telah tersimpan. Silakan kirimkan bukti pembayaran DP ke admin kami via WhatsApp.
        </p>

        @php
            $galleryUrl = ($album->drive_link && str_starts_with($album->drive_link, 'http'))
                ? $album->drive_link
                : 'https://send.snapfunstudio.id/gallery/' . $album->id;
        @endphp

        <div class="bg-gray-50 border border-gray-200 rounded-2xl p-6 w-full max-w-sm mx-auto shadow-sm flex flex-col items-center">
            <p class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-4">
                QR Code Hasil Foto
            </p>

            <div class="bg-white p-2 rounded-xl border border-gray-200 mb-4 inline-block shadow-sm">
                <img 
                    src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&margin=8&data={{ urlencode($galleryUrl) }}" 
                    alt="QR Code Galeri" 
                    class="w-48 h-48 object-contain"
                >
            </div>

            <p class="text-sm text-gray-500 mb-5">
                ID Folder: <strong class="font-mono text-gray-900 text-lg">{{ $album->id }}</strong>
            </p>

            <a 
                href="{{ $galleryUrl }}" 
                target="_blank" 
                rel="noopener noreferrer"
                class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg transition-colors shadow-md"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                </svg>
                Buka Link Hasil Foto
            </a>
        </div>
        
        <a href="/" class="mt-8 text-sm text-gray-400 hover:text-blue-600 transition-colors">
            &larr; Kembali ke Beranda
        </a>
    </section>

</body>
</html>