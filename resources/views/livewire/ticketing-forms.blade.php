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
                    <x-filament::button type="submit" size="lg" class="w-full justify-center text-lg" style="background-color: #004fbf;">
                        {{ __('Kirim') }}
                    </x-filament::button>
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

                <!-- TAMBAHAN QR CODE LINK GOOGLE DRIVE -->
                @if($this->drive_link)
                <div style="margin-top: 1.8rem; display: flex; flex-direction: column; align-items: center;">
                    <p style="margin: 0; font-size: 0.95rem; color: #333; font-weight: 700; margin-bottom: 0.8rem;">
                        Scan QR ini untuk akses folder fotomu nanti:
                    </p>
                    
                    <div style="background: white; padding: 12px; border-radius: 16px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); border: 1px solid #eaeaea;">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=160x160&data={{ urlencode($this->drive_link) }}&margin=0" alt="QR Code Galeri" style="width: 140px; height: 140px; border-radius: 8px;">
                    </div>
                    
                    <a href="{{ $this->drive_link }}" target="_blank" style="margin-top: 0.8rem; font-size: 0.85rem; color: #1759CA; font-weight: 700; text-decoration: underline;">
                        Atau klik link ini
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