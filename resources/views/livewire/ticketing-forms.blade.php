<div class="flex flex-col items-center p-4 text-gray-900 dark:bg-gray-900 dark:text-white">
    
    @if (!$isSuccess)
        <div class="w-full max-w-3xl p-8 bg-white rounded-lg shadow dark:bg-gray-800">
             <h2 class="mb-4 font-bold text-center text-xl sm:text-2xl md:text-3xl">
                Silahkan Masukan Data Kamu
            </h2>

            <form wire:submit.prevent="submit" class="space-y-6">
                {{ $this->form }}

                @if ($transaction_type === 'qris')
                @endif

                <div class="w-full mt-4">
                    <x-filament::button type="Submit" size="lg" class="w-full justify-center text-lg" style="background-color: #004fbf;">
                        {{ __('Kirim') }}
                    </x-filament::button>
                </div>
            </form>
        </div>

    @elseif (!$isWaiting)
        <section id="page-sukses" class="page active" style="display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; width: 100%; padding: 2rem 1rem;">
            
            <div class="success-container" style="display: flex; flex-direction: column; align-items: center; max-width: 500px; width: 100%;">
                
                <div class="success-icon" style="display: flex; justify-content: center; width: 100%; margin-bottom: 1.5rem;">
                    <img src="{{ asset('img/Pinpin-01.png') }}" alt="Mascot Snap Fun" style="height: 170px; width: auto; object-fit: contain;">
                </div>
                
                <h1 style="font-weight: 800; font-size: clamp(1.8rem, 5vw, 2.2rem); margin-top: 0.5rem; line-height: 1.2;">
                    Pastikan data kamu sudah benar!
                </h1>

                <!-- FITUR LIVE ANTREAN CUSTOMER -->
                <!-- wire:poll.3s akan merefresh blok DIV ini saja setiap 3 detik secara otomatis -->
                <div wire:poll.3s class="w-full mt-6 mb-2">
                    @if($this->queueStatus)
                        
                        @if($this->queueStatus['isCalled'])
                            <!-- JIKA SUDAH DIPANGGIL -->
                            <div style="background-color: #28a745; border-radius: 15px; padding: 1.5rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1); margin-bottom: 1rem; color: white;">
                                <h2 style="font-weight: 800; font-size: 1.8rem; margin: 0; line-height: 1;">GILIRAN KAMU!</h2>
                                <p style="font-size: 1rem; margin-top: 0.5rem; margin-bottom: 0;">Ayo bergegas menuju area Photobooth sekarang.</p>
                            </div>
                        @else
                            <!-- JIKA MASIH MENUNGGU -->
                            <!-- Card Antrean -->
                            <div style="background-color: #d13d56; border-radius: 15px; padding: 1.5rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1); margin-bottom: 1rem; display: flex; justify-content: space-between; align-items: center; color: white; text-align: left;">
                                <div>
                                    <p style="margin: 0; font-size: 1.1rem; line-height: 1.3;">Sabar yaw.</p>
                                    <p style="margin: 0; font-size: 1.1rem; line-height: 1.3;">Kamu akan foto</p>
                                    <p style="margin: 0; font-size: 1.1rem; line-height: 1.3;">setelah :</p>
                                </div>
                                <div style="text-align: center;">
                                    <span style="font-size: 4rem; font-weight: 800; line-height: 1; display: block;">{{ $this->queueStatus['peopleAhead'] }}</span>
                                    <span style="font-size: 0.9rem; font-weight: 500;">antrian lagi.</span>
                                </div>
                            </div>

                            <!-- Card Estimasi Waktu (5 menit per antrean) -->
                            <div style="background-color: #d13d56; border-radius: 15px; padding: 1.5rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1); display: flex; justify-content: space-between; align-items: center; color: white; text-align: left;">
                                <div>
                                    <p style="margin: 0; font-size: 1.1rem; line-height: 1.3;">Atau sekitar</p>
                                </div>
                                <div style="text-align: center;">
                                    <span style="font-size: 4rem; font-weight: 800; line-height: 1; display: block;">{{ $this->queueStatus['peopleAhead'] * 5 }}</span>
                                    <span style="font-size: 0.9rem; font-weight: 500;">menit lagi.</span>
                                </div>
                            </div>
                        @endif

                    @endif
                </div>
                <!-- END FITUR LIVE ANTREAN -->
                
                <p style="line-height: 1.6; color: #666; font-size: 0.95rem; margin-bottom: 2rem; margin-top: 1.5rem; padding: 0 10px;">
                    Data yang kamu kirim, Pinpin jamin keamanannya, Pinpin butuh data kamu buat kirim soft file fotonya yaw! Ouh iya soft file paling lama 
                    Pinpin kirim besok, dan<strong> bisa diakses hanya dalam waktu 14 hari</strong>, jadi jangan lupa langsung di download yaaaa!!!
                </p>

                <button wire:click="confirmToWA" class="whatsapp btn btn-primary" style="background-color: #1759CA; color: white; padding: 1.1rem 2.5rem; border-radius: 50px; border: none; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 10px; font-size: 1rem; transition: transform 0.2s;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="white"><path d="M12.012 2c-5.508 0-9.987 4.479-9.987 9.987 0 1.763.462 3.421 1.264 4.859l-1.289 4.714 4.825-1.265c1.401.764 2.993 1.198 4.686 1.198 5.508 0 9.988-4.479 9.988-9.987 0-5.508-4.48-9.987-9.987-9.987zm4.567 14.221c-.247.694-1.233 1.258-1.701 1.332-.423.067-.972.115-2.732-.614-2.251-.933-3.702-3.224-3.815-3.374-.112-.149-.915-1.216-.915-2.316 0-1.1.574-1.642.779-1.865.206-.223.449-.279.598-.279.15 0 .299.001.43.007.135.006.317-.052.497.382.186.45.637 1.551.693 1.663.056.112.093.242.019.391-.074.149-.112.242-.224.372l-.336.392c-.112.13-.23.272-.099.497.13.224.58 1.05 1.25 1.646.861.767 1.587 1.004 1.811 1.116.224.112.354.093.484-.056.13-.149.56-.653.71-.876.149-.224.298-.186.497-.112.199.074 1.266.597 1.49.709.224.112.373.167.429.261.056.093.056.541-.191 1.235z"/></svg>
                    Konfirmasi Bookingan
                </button>
            </div>
        </section>
    @else
        <!-- HALAMAN MENUNGGU (SETELAH KLIK KONFIRMASI) -->
        <section id="page-menunggu" class="page active" style="display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; width: 100%; padding: 2rem 1rem;">
            
            <div class="success-container" style="display: flex; flex-direction: column; align-items: center; max-width: 500px; width: 100%;">
                
                <div class="success-icon" style="display: flex; justify-content: center; width: 100%; margin-bottom: 1.5rem;">
                    <img src="{{ asset('img/Pinpin-01.png') }}" alt="Mascot Snap Fun" style="height: 170px; width: auto; object-fit: contain;">
                </div>
                
                <h1 style="font-weight: 800; font-size: clamp(1.8rem, 5vw, 2.2rem); margin-top: 0.5rem; line-height: 1.2; color: #333;">
                    Maaciw, udah mau nunggu..
                </h1>

                <!-- TAMPILAN NOMOR ANTREAN CUSTOMER -->
                <div style="background-color: #f3f4f6; border: 2px dashed #ccc; border-radius: 12px; padding: 1rem 2rem; margin-top: 1.5rem; display: inline-block;">
                    <p style="margin: 0; font-size: 0.9rem; color: #666; font-weight: 600; text-transform: uppercase;">Nomor Antrean Kamu</p>
                    <h2 style="margin: 0; font-size: 2.5rem; font-weight: 900; color: #1759CA; letter-spacing: 2px;">{{ $this->queue_number }}</h2>
                </div>
                <!-- TAMBAHAN QR CODE LINK GOOGLE DRIVE -->
                @if($this->drive_link)
                <div style="margin-top: 1.8rem; display: flex; flex-direction: column; align-items: center;">
                    <p style="margin: 0; font-size: 0.95rem; color: #333; font-weight: 700; margin-bottom: 0.8rem;">
                        Scan QR ini untuk akses folder fotomu nanti:
                    </p>
                    
                    <div style="background: white; padding: 12px; border-radius: 16px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); border: 1px solid #eaeaea;">
                        <!-- Generate QR Code dinamis dari Link GDrive -->
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=160x160&data={{ urlencode($this->drive_link) }}&margin=0" alt="QR Code Galeri" style="width: 140px; height: 140px; border-radius: 8px;">
                    </div>
                    
                    <a href="{{ $this->drive_link }}" target="_blank" style="margin-top: 0.8rem; font-size: 0.85rem; color: #1759CA; font-weight: 700; text-decoration: underline;">
                        Atau klik link ini
                    </a>
                </div>
                @endif
                <!-- END TAMBAHAN QR CODE -->

                <p style="margin-top: 1.5rem; margin-bottom: 1rem; font-size: 1.1rem; color: #555;">
                    Sabar yaw. Kamu akan foto setelah :
                </p>

                <!-- FITUR LIVE ANTREAN CUSTOMER -->
                <div wire:poll.3s class="w-full mb-2">
                    @if($this->queueStatus)
                        
                        @if($this->queueStatus['isCalled'])
                            <!-- JIKA SUDAH DIPANGGIL -->
                            <div style="background-color: #28a745; border-radius: 15px; padding: 1.5rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1); margin-bottom: 1rem; color: white;">
                                <h2 style="font-weight: 800; font-size: 1.8rem; margin: 0; line-height: 1;">GILIRAN KAMU!</h2>
                                <p style="font-size: 1rem; margin-top: 0.5rem; margin-bottom: 0;">Ayo bergegas menuju area Photobooth sekarang.</p>
                            </div>
                        @else
                            <!-- JIKA MASIH MENUNGGU (UI GABUNGAN) -->
                            <div style="background-color: #cb314d; border-radius: 15px; padding: 1.5rem 0; box-shadow: 0 4px 6px rgba(0,0,0,0.1); margin-bottom: 1rem; display: flex; color: white; width: 100%; max-width: 350px;">
                                <!-- Antrean Section -->
                                <div style="flex: 1; border-right: 2px solid rgba(255,255,255,0.5); display: flex; flex-direction: column; justify-content: center; align-items: center;">
                                    <span style="font-size: 4rem; font-weight: 800; line-height: 1; margin-bottom: -5px;">{{ $this->queueStatus['peopleAhead'] }}</span>
                                    <span style="font-size: 1.1rem; font-weight: 500;">antrian lagi</span>
                                </div>
                                <!-- Menit Section -->
                                <div style="flex: 1; display: flex; flex-direction: column; justify-content: center; align-items: center;">
                                    <span style="font-size: 4rem; font-weight: 800; line-height: 1; margin-bottom: -5px;">{{ $this->queueStatus['peopleAhead'] * 5 }}</span>
                                    <span style="font-size: 1.1rem; font-weight: 500;">menit lagi</span>
                                </div>
                            </div>
                            
                            <!-- Pemicu Notifikasi (Hidden Element) -->
                            <span class="hidden queue-tracker" data-ahead="{{ $this->queueStatus['peopleAhead'] }}"></span>
                        @endif
                    @endif
                </div>
                <!-- END FITUR LIVE ANTREAN -->
                
                <p style="line-height: 1.6; color: #666; font-size: 1rem; margin-bottom: 2rem; margin-top: 1.5rem; padding: 0 20px;">
                    Jangan lupa buat pantau terus, biar sesi foto kamu ga terlewat.
                </p>

            </div>
        </section>
    @endif
</div>

<script>
    document.addEventListener('livewire:initialized', () => {
        // Meminta izin notifikasi saat komponen pertama kali dimuat
        if ("Notification" in window) {
            if (Notification.permission !== "granted" && Notification.permission !== "denied") {
                Notification.requestPermission();
            }
        }

        // Variabel untuk mencegah notifikasi muncul berulang kali
        let notificationSent = false;

        Livewire.on('redirectToWA', (event) => {
            // Membuka WhatsApp di tab baru
            window.open(event.url, '_blank');
        });

        // Memantau perubahan DOM untuk mengecek sisa antrean
        const observer = new MutationObserver((mutations) => {
            const tracker = document.querySelector('.queue-tracker');
            if (tracker) {
                const ahead = parseInt(tracker.getAttribute('data-ahead'));
                
                // Memicu notifikasi jika sisa antrean 1 dan belum pernah dikirim
                if (ahead === 1 && !notificationSent) {
                    if ("Notification" in window && Notification.permission === "granted") {
                        new Notification("Giliran Kamu Sebentar Lagi!", {
                            body: "Hanya sisa 1 antrean lagi. Harap bersiap menuju photobooth.",
                            icon: "{{ asset('img/Pinpin-01.png') }}"
                        });
                        notificationSent = true;
                    }
                } else if (ahead > 1) {
                    // Reset flag jika antrean entah bagaimana bertambah (edge case)
                    notificationSent = false;
                }
            }
        });

        // Mulai memantau elemen utama Livewire
        const livewireComponent = document.querySelector('[wire\\:id]');
        if (livewireComponent) {
            observer.observe(livewireComponent, { childList: true, subtree: true });
        }
    });
</script>