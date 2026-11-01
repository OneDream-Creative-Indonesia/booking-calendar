<div class="flex flex-col items-center p-4 text-gray-900 dark:bg-gray-900 dark:text-white">
    
    @if (!$isSuccess)
        <!-- HALAMAN FORM INPUT -->
        <div class="w-full max-w-3xl p-8 bg-white rounded-lg shadow dark:bg-gray-800">
            <h2 class="mb-4 font-bold text-center text-xl sm:text-2xl md:text-3xl">
                Silahkan Masukan Data Kamu
            </h2>

            <form wire:submit.prevent="submit" class="space-y-6">
                {{ $this->form }}

                @if ($transaction_type === 'qris')
                @endif

            <div class="w-full mt-4">
                    <button 
                        type="submit" 
                        wire:loading.attr="disabled"
                        wire:target="submit"
                        class="w-full relative overflow-hidden rounded-lg shadow-sm transition-all hover:opacity-90 active:scale-95 disabled:cursor-not-allowed"
                    >
                        <!-- Placeholder transparan untuk mengunci ukuran tombol agar tidak bergeser -->
                        <div class="invisible flex items-center justify-center gap-2 px-6 py-3 text-lg font-bold">
                            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="4"></circle></svg>
                            <span>Memproses...</span>
                        </div>

                        <!-- Tampilan Normal (Warna Biru) -->
                        <div wire:loading.remove wire:target="submit" class="absolute inset-0 flex items-center justify-center text-white" style="background-color: #004fbf;">
                            <span class="text-lg font-bold">{{ __('Kirim') }}</span>
                        </div>
                        
                        <!-- Tampilan Loading (Warna Kuning #ffc20e, Teks Hitam) -->
                        <div wire:loading wire:target="submit" class="absolute inset-0" style="background-color: #ffc20e; color: #111827;">
                            <div class="flex w-full h-full items-center justify-center gap-2">
                                <svg class="w-5 h-5 animate-spin text-gray-900" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span class="text-lg font-bold">Memproses...</span>
                            </div>
                        </div>
                    </button>
                </div>
            </form>
        </div>

    @else
        <!-- HALAMAN QR CODE & ANTREAN (LANGSUNG MUNCUL SETELAH SUBMIT) -->
        <section id="page-menunggu" class="page active" style="display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; width: 100%; padding: 2rem 1rem;">
            
            <div class="success-container" style="display: flex; flex-direction: column; align-items: center; max-width: 500px; width: 100%;">
                
                <div class="success-icon" style="display: flex; justify-content: center; width: 100%; margin-bottom: 1.5rem;">
                    <img src="{{ asset('img/Pinpin-01.png') }}" alt="Mascot Snap Fun" style="height: 170px; width: auto; object-fit: contain;">
                </div>
                
                <h1 style="font-weight: 800; font-size: clamp(1.8rem, 5vw, 2.2rem); margin-top: 0.5rem; line-height: 1.2; color: #333;">
                    Maaciw, udah mau nunggu..
                </h1>

                <!-- TAMPILAN NOMOR ANTREAN CUSTOMER -->
                <div style="background-color: #CE004F; border-radius: 12px; padding: 1.2rem 2.5rem; margin-top: 1.5rem; display: inline-block; box-shadow: 0 4px 10px rgba(206, 0, 79, 0.2);">
                    <p style="margin: 0; font-size: 0.9rem; color: #ffffff; font-weight: 600; text-transform: uppercase; opacity: 0.9;">Nomor Antrean Kamu</p>
                    <h2 style="margin: 5px 0 0 0; font-size: 3rem; font-weight: 900; color: #ffffff; letter-spacing: 2px;">{{ $this->queue_number }}</h2>
                </div>

                <!-- TAMPILAN QR CODE & LINK GALERI SEND.SNAPFUNSTUDIO.ID -->
                @if($this->album_id)
                    @php
                        $galleryUrl = ($this->drive_link && str_starts_with($this->drive_link, 'http'))
                            ? $this->drive_link
                            : 'https://send.snapfunstudio.id/gallery/' . $this->album_id;
                    @endphp

                    <div style="background-color: #ffffff; border: 1px solid #e5e7eb; border-radius: 16px; padding: 1.25rem; margin-top: 1.25rem; width: 100%; max-width: 350px; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06); display: flex; flex-direction: column; align-items: center;">
                        <p style="margin: 0 0 0.75rem 0; font-size: 0.85rem; font-weight: 700; color: #4b5563; text-transform: uppercase; letter-spacing: 0.5px;">
                            QR Code & Link Hasil Foto
                        </p>

                        <div style="background-color: #ffffff; padding: 0.5rem; border-radius: 12px; border: 1px solid #f3f4f6; margin-bottom: 0.75rem;">
                            <img 
                                src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&margin=8&data={{ urlencode($galleryUrl) }}" 
                                alt="QR Code Galeri {{ $this->queue_number }}" 
                                style="width: 180px; height: 180px; object-fit: contain; display: block;"
                            >
                        </div>

                        <p style="margin: 0 0 0.85rem 0; font-size: 0.8rem; color: #6b7280;">
                            ID Folder: <strong style="color: #111827; font-family: monospace; font-size: 0.9rem;">{{ $this->album_id }}</strong>
                        </p>

                        <a 
                            href="{{ $galleryUrl }}" 
                            target="_blank" 
                            rel="noopener noreferrer"
                            style="background-color: #004fbf; color: #ffffff; text-decoration: none; font-weight: 700; font-size: 0.95rem; padding: 0.75rem 1.25rem; border-radius: 10px; width: 100%; display: inline-flex; align-items: center; justify-content: center; gap: 8px; box-shadow: 0 2px 6px rgba(0, 79, 191, 0.25); transition: opacity 0.2s;"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" style="width: 18px; height: 18px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                            </svg>
                            Klik Link Hasil Foto
                        </a>
                    </div>
                @endif

                <p style="margin-top: 1.5rem; margin-bottom: 1rem; font-size: 1.1rem; color: #555;">
                    Sabar yaw. Kamu akan foto setelah :
                </p>

                <!-- FITUR LIVE ANTREAN CUSTOMER -->
                <div wire:poll.3s class="w-full mb-2 flex justify-center">
                    @php
                        // Memanggil fungsi tanpa error variabel
                        $statusAntrean = $this->checkQueueStatus();
                    @endphp

                    @if($statusAntrean)
                        @if($statusAntrean['isCalled'])
                            <div style="background-color: #28a745; border-radius: 15px; padding: 1.5rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1); margin-bottom: 1rem; color: white; width: 100%; max-width: 350px;">
                                <h2 style="font-weight: 800; font-size: 1.8rem; margin: 0; line-height: 1;">GILIRAN KAMU!</h2>
                                <p style="font-size: 1rem; margin-top: 0.5rem; margin-bottom: 0;">Ayo bergegas menuju area Photobooth sekarang.</p>
                            </div>
                        @else
                            <div style="background-color: #cb314d; border-radius: 15px; padding: 1.5rem 0; box-shadow: 0 4px 6px rgba(0,0,0,0.1); margin-bottom: 1rem; display: flex; color: white; width: 100%; max-width: 350px;">
                                <!-- Antrean Section -->
                                <div style="flex: 1; border-right: 2px solid rgba(255,255,255,0.5); display: flex; flex-direction: column; justify-content: center; align-items: center;">
                                    <span style="font-size: 4rem; font-weight: 800; line-height: 1; margin-bottom: -5px;">{{ $statusAntrean['peopleAhead'] }}</span>
                                    <span style="font-size: 1.1rem; font-weight: 500;">antrean lagi</span>
                                </div>
                                <!-- Menit Section -->
                                <div style="flex: 1; display: flex; flex-direction: column; justify-content: center; align-items: center;">
                                    <span style="font-size: 4rem; font-weight: 800; line-height: 1; margin-bottom: -5px;">{{ $statusAntrean['peopleAhead'] * 5 }}</span>
                                    <span style="font-size: 1.1rem; font-weight: 500;">menit lagi</span>
                                </div>
                            </div>
                            
                            <span class="hidden queue-tracker" data-ahead="{{ $statusAntrean['peopleAhead'] }}"></span>
                        @endif
                    @endif
                </div>
                
                <p style="line-height: 1.6; color: #666; font-size: 1rem; margin-bottom: 2rem; margin-top: 1.5rem; padding: 0 20px;">
                    Jangan lupa buat pantau terus, biar sesi foto kamu ga terlewat.
                </p>

            </div>
        </section>
    @endif
</div>

<script>
    document.addEventListener('livewire:initialized', () => {
        if ("Notification" in window) {
            if (Notification.permission !== "granted" && Notification.permission !== "denied") {
                Notification.requestPermission();
            }
        }

        let notificationSent = false;

        const observer = new MutationObserver((mutations) => {
            const tracker = document.querySelector('.queue-tracker');
            if (tracker) {
                const ahead = parseInt(tracker.getAttribute('data-ahead'));
                
                if (ahead === 1 && !notificationSent) {
                    if ("Notification" in window && Notification.permission === "granted") {
                        new Notification("Giliran Kamu Sebentar Lagi!", {
                            body: "Hanya sisa 1 antrean lagi. Harap bersiap menuju photobooth.",
                            icon: "{{ asset('img/Pinpin-01.png') }}"
                        });
                        notificationSent = true;
                    }
                } else if (ahead > 1) {
                    notificationSent = false;
                }
            }
        });

        const livewireComponent = document.querySelector('[wire\\:id]');
        if (livewireComponent) {
            observer.observe(livewireComponent, { childList: true, subtree: true });
        }
    });
</script>