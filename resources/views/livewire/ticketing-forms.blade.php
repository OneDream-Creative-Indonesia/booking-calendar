<div class="flex flex-col items-center justify-center min-h-screen p-4 text-gray-900 dark:bg-gray-900 dark:text-white">
    @if (Session::has('message'))
        <div id="testimonialPopup" class="fixed z-50 w-full max-w-xs p-4 mb-4 text-gray-500 bg-white rounded-lg shadow top-4 right-4 dark:text-gray-400 dark:bg-gray-800" role="alert">
            <div class="flex items-center justify-between">
                <div class="flex items-center justify-center flex-shrink-0 w-8 h-8 text-green-500 bg-green-100 rounded-lg dark:bg-green-800 dark:text-green-200">
                    <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z"/>
                    </svg>
                    <span class="sr-only">Check icon</span>
                </div>
                <div class="ml-3 text-sm font-normal">{{ Session::get('message') }}</div>
                <button type="button" class="-mx-1.5 -my-1.5 bg-white text-gray-400 hover:text-gray-900 rounded-lg focus:ring-2 focus:ring-gray-300 p-1.5 hover:bg-gray-100 flex items-center justify-center h-8 w-8 dark:text-gray-500 dark:hover:text-white dark:bg-gray-800 dark:hover:bg-gray-700" onclick="closeTestimonialPopup()" aria-label="Close">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                    </svg>
                </button>
            </div>
        </div>
    @endif


        <div class="w-full max-w-3xl p-8 bg-white rounded-lg shadow dark:bg-gray-800">
             <h2 class="mb-4 font-bold text-center md:text-2xl sm:text-xl">
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
</div>

<script>
    setTimeout(() => {
        let popup = document.getElementById("testimonialPopup");
        if (popup) {
            popup.style.display = "none";
        }
    }, 5000);

    function closeTestimonialPopup() {
        let popup = document.getElementById("testimonialPopup");
        if (popup) {
            popup.style.display = "none";
        }
    }
</script>
