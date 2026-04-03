<div class="flex flex-col items-center justify-center min-h-screen p-4 text-gray-900 dark:bg-gray-900 dark:text-white">
    
    @if (!$isSuccess)
        <div class="w-full max-w-3xl p-8 bg-white rounded-lg shadow dark:bg-gray-800">
             <h2 class="mb-4 font-bold text-center text-xl sm:text-2xl md:text-3xl">
                Silahkan Masukan Data Kamu
            </h2>

            <form wire:submit.prevent="submit" class="space-y-6">
                {{ $this->form }}

                @if ($transaction_type === 'qris')
                    <div class="flex justify-center">
                       <img src="../../assets/img/IMG_6642.png" alt="QRIS Image" class="w-1/2 h-auto">
                    </div>
                @endif

                <div class="flex flex-wrap items-center justify-start gap-4">
                    <x-filament::button type="Submit" style="background-color: #1759CA;">
                        {{ __('Submit') }}
                    </x-filament::button>
                </div>
            </form>
        </div>

  @else
        <section id="page-sukses" class="page active" style="display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; width: 100%; padding: 2rem 1rem;">
            
            <div class="success-container" style="display: flex; flex-direction: column; align-items: center; max-width: 500px; width: 100%;">
                
                <div class="success-icon" style="display: flex; justify-content: center; width: 100%; margin-bottom: 1.5rem;">
                    <img src="{{ asset('img/Pinpin-01.png') }}" alt="Mascot Snap Fun" style="height: 170px; width: auto; object-fit: contain;">
                </div>
                
                <h1 style="font-weight: 800; font-size: clamp(1.8rem, 5vw, 2.2rem); margin-top: 0.5rem; line-height: 1.2;">
                    Pastikan data kamu sudah benar!
                </h1>
                
                <p style="line-height: 1.6; color: #666; font-size: 0.95rem; margin-bottom: 2rem;margin-top: 2rem; padding: 0 10px;">
                    Data yang kamu kirim, Pinpin jamin keamanannya, Pinpin butuh data kamu buat kirim soft file fotonya yaw! Ouh iya soft file paling lama 
                    Pinpin kirim besok, dan<strong> bisa diakses hanya dalam waktu 14 hari</strong>, jadi jangan lupa langsung di download yaaaa!!!
                </p>

                <button wire:click="confirmToWA" class="whatsapp btn btn-primary" style="background-color: #1759CA; color: white; padding: 1.1rem 2.5rem; border-radius: 50px; border: none; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 10px; font-size: 1rem; transition: transform 0.2s;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="white"><path d="M12.012 2c-5.508 0-9.987 4.479-9.987 9.987 0 1.763.462 3.421 1.264 4.859l-1.289 4.714 4.825-1.265c1.401.764 2.993 1.198 4.686 1.198 5.508 0 9.988-4.479 9.988-9.987 0-5.508-4.48-9.987-9.987-9.987zm4.567 14.221c-.247.694-1.233 1.258-1.701 1.332-.423.067-.972.115-2.732-.614-2.251-.933-3.702-3.224-3.815-3.374-.112-.149-.915-1.216-.915-2.316 0-1.1.574-1.642.779-1.865.206-.223.449-.279.598-.279.15 0 .299.001.43.007.135.006.317-.052.497.382.186.45.637 1.551.693 1.663.056.112.093.242.019.391-.074.149-.112.242-.224.372l-.336.392c-.112.13-.23.272-.099.497.13.224.58 1.05 1.25 1.646.861.767 1.587 1.004 1.811 1.116.224.112.354.093.484-.056.13-.149.56-.653.71-.876.149-.224.298-.186.497-.112.199.074 1.266.597 1.49.709.224.112.373.167.429.261.056.093.056.541-.191 1.235z"/></svg>
                    Konfirmasi via WhatsApp
                </button>
            </div>
        </section>
    @endif
</div>

<script>
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('redirectToWA', (event) => {
            // Membuka WhatsApp di tab baru sesuai alur template kamu
            window.open(event.url, '_blank');
        });
    });
</script>