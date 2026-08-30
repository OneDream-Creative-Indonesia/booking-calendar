<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Wajib di Laravel untuk request POST via AJAX/Fetch -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Display Antrean Photobooth</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        body { margin: 0; overflow: hidden; background-color: black; }
        
        /* CLASS UNTUK MEMUTAR LAYAR WEB 90 DERAJAT (Searah Jarum Jam) */
        .rotate-cw {
            width: 100vh !important;
            height: 100vw !important;
            transform: rotate(90deg) translateY(-100%);
            transform-origin: top left;
        }

        /* CLASS UNTUK MEMUTAR LAYAR WEB -90 DERAJAT (Berlawanan Jarum Jam / Bawaan Baru) */
        .rotate-ccw {
            width: 100vh !important;
            height: 100vw !important;
            transform: rotate(-90deg) translateX(-100%);
            transform-origin: top left;
        }
    </style>
</head>
<body class="w-screen h-screen relative">

    <!-- Efek Bel sebelum suara panggilan Text-to-Speech -->
    <audio id="audio-bell" src="https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3" preload="auto"></audio>

    <!-- WRAPPER UTAMA: Ini yang akan diputar oleh CSS -->
    <!-- DEFAULT: rotate-ccw (Kebalikan/180 derajat dari posisi sebelumnya) -->
    <div id="main-wrapper" class="absolute top-0 left-0 flex flex-col bg-white rotate-ccw transition-transform duration-500">
        
        <!-- Tombol Rahasia Pemutar Layar (Pojok Kiri Atas) -->
        <button onclick="toggleRotation(event)" class="absolute top-2 left-2 z-50 text-gray-300 hover:text-blue-500 bg-white/50 p-2 rounded-full cursor-pointer focus:outline-none shadow-sm backdrop-blur-sm">
            <i data-lucide="rotate-cw" class="w-6 h-6"></i>
        </button>

        <!-- BAGIAN ATAS: INFORMASI ANTREAN (Bisa ditap untuk panggil selanjutnya) -->
        <div onclick="callNext()" class="bg-white w-full flex items-center justify-between px-6 md:px-10 py-4 sm:py-6 lg:py-8 cursor-pointer select-none active:bg-gray-50 transition-colors relative z-10 shadow-[0_10px_40px_rgba(0,0,0,0.15)] flex-shrink-0 gap-2">
            
            <!-- Kolom Kiri: Yang lagi foto & Selanjutnya -->
            <div class="flex flex-col justify-center gap-2 sm:gap-3 flex-1 min-w-0">
                <p class="text-gray-500 text-lg sm:text-xl md:text-2xl font-medium tracking-wide">Yang lagi foto...</p>
                
                <!-- Nomor Saat Ini -->
                <div>
                    <div class="bg-[#245DCD] text-white font-black text-4xl sm:text-6xl md:text-7xl rounded-xl sm:rounded-2xl px-6 sm:px-8 py-3 md:py-4 inline-block shadow-lg tracking-wider">
                        <span id="current-queue">SF---</span>
                    </div>
                </div>
                
                <!-- Antrean Selanjutnya -->
                <div class="flex flex-wrap items-center gap-2 sm:gap-3 md:gap-4 text-base sm:text-lg md:text-2xl mt-1 md:mt-2">
                    <span class="text-gray-700 font-medium whitespace-nowrap">Selanjutnya</span>
                    <span class="bg-[#F6A820] text-black font-black px-3 sm:px-4 py-1.5 rounded-lg shadow-md whitespace-nowrap" id="next-queue">SF---</span>
                </div>
            </div>

            <!-- Kolom Kanan: Sisa Antrean -->
            <div class="bg-[#CC3444] text-white rounded-2xl sm:rounded-3xl flex flex-col items-center justify-center shadow-xl py-6 sm:py-8 px-3 flex-shrink-0 w-auto min-w-[140px] sm:min-w-[180px] aspect-auto">
                <span class="text-6xl sm:text-7xl md:text-8xl font-black leading-none tracking-tighter" id="queue-count">0</span>
                <span class="text-sm sm:text-base md:text-lg font-medium tracking-wide mt-1 md:mt-2 text-center whitespace-nowrap">antrian lagi</span>
            </div>
        </div>

        <!-- BAGIAN BAWAH: TEMPAT VIDEO IKLAN (FLEX-1 MENGISI PENUH SISA LAYAR) -->
        <div class="flex-1 bg-black relative w-full flex items-center justify-center pointer-events-none overflow-hidden">
            
            <!-- Indikator Loading Video Awal -->
            <div id="video-loader" class="absolute inset-0 flex flex-col items-center justify-center text-white/50 z-0">
                <i data-lucide="loader-2" class="w-10 h-10 animate-spin mb-2"></i>
                <span class="text-sm font-medium tracking-widest uppercase">Memuat Video...</span>
            </div>

            <!-- Video Player -->
            <video 
                id="promo-video"
                autoplay 
                loop 
                muted 
                playsinline
                class="w-full h-full object-contain relative z-10 opacity-0 transition-opacity duration-1000"
            ></video>
        </div>
    </div>

    <!-- JavaScript -->
    <script>
        lucide.createIcons();

        // State Aplikasi
        let queue = [];
        let voices = [];
        let currentUtterance = null; 

        // Elemen DOM
        const currentQueueEl = document.getElementById('current-queue');
        const nextQueueEl = document.getElementById('next-queue');
        const queueCountEl = document.getElementById('queue-count');
        const mainWrapper = document.getElementById('main-wrapper');
        const videoEl = document.getElementById('promo-video');
        const videoLoader = document.getElementById('video-loader');

        // TRIK ANTI JARINGAN LEMAH: Load video ke RAM (Blob)
        async function loadVideoToMemory() {
            try {
                const videoPath = '{{ asset("img/sfanimasi.mp4") }}'; 
                
                const response = await fetch(videoPath);
                if (!response.ok) throw new Error("Video tidak ditemukan");
                
                const blob = await response.blob();
                const localVideoUrl = URL.createObjectURL(blob);
                
                videoEl.src = localVideoUrl;
                
                videoEl.oncanplay = () => {
                    videoLoader.style.display = 'none';
                    videoEl.style.opacity = '1';
                };
            } catch (error) {
                console.error("Gagal caching video, fallback ke src biasa:", error);
                videoEl.src = '{{ asset("img/sfanimasi.mp4") }}';
                videoLoader.style.display = 'none';
                videoEl.style.opacity = '1';
            }
        }
        
        loadVideoToMemory();

        // Fungsi Memutar Layar Secara Dinamis
        function toggleRotation(event) {
            event.stopPropagation(); 
            if (mainWrapper.classList.contains('rotate-ccw')) {
                mainWrapper.classList.remove('rotate-ccw');
                mainWrapper.classList.add('rotate-cw'); 
            } else {
                mainWrapper.classList.remove('rotate-cw');
                mainWrapper.classList.add('rotate-ccw'); 
            }
        }

        // Pancing TTS Engine
        document.body.addEventListener('click', () => {
            if ('speechSynthesis' in window && !window.speechSynthesis.pending) {
                const test = new SpeechSynthesisUtterance("");
                test.volume = 0;
                window.speechSynthesis.speak(test);
            }
        }, { once: true });

        // Setup Suara TTS
        if ('speechSynthesis' in window) {
            const loadVoices = () => { voices = window.speechSynthesis.getVoices(); };
            loadVoices();
            if (window.speechSynthesis.onvoiceschanged !== undefined) {
                window.speechSynthesis.onvoiceschanged = loadVoices;
            }
        }

        function getQueueNumber(person) {
            if (!person) return '-';
            return person.queue_number || 'TBA'; 
        }
        
        function renderDisplay() {
            const remaining = Math.max(0, queue.length - 1);
            queueCountEl.textContent = remaining;
            
            if (queue.length === 0) {
                currentQueueEl.textContent = 'SF---';
                nextQueueEl.textContent = 'SF---';
            } else {
                currentQueueEl.textContent = getQueueNumber(queue[0]);
                if (queue.length > 1) {
                    nextQueueEl.textContent = getQueueNumber(queue[1]);
                } else {
                    nextQueueEl.textContent = 'Kosong';
                }
            }
        }

        async function fetchDatabase() {
            try {
                // AMBIL SLUG DARI URL
                const segments = window.location.pathname.split('/').filter(Boolean);
                const slug = segments[segments.length - 1];
                
                // BUAT ENDPOINT DINAMIS BERDASARKAN SLUG
                let endpoint = '/api/antrian/get_queue';
                if (slug && slug !== 'antrian') {
                    endpoint = `/api/antrian/get_queue/${slug}`;
                }

                const response = await fetch(endpoint);
                if (!response.ok) return; 
                
                const result = await response.json();
                
                if (result.success) {
                    const oldTop = queue.length > 0 ? queue[0] : null;
                    const newQueue = result.data;
                    
                    if (oldTop && newQueue.length > 0) {
                        const newTop = newQueue[0];
                        if (oldTop.id !== newTop.id) {
                            queue = newQueue;
                            renderDisplay();
                            speakName(getQueueNumber(newTop));
                            return; 
                        }
                    } else if (oldTop && newQueue.length === 0) {
                        queue = newQueue;
                        renderDisplay();
                        return;
                    }
                    
                    queue = newQueue; 
                    renderDisplay();
                }
            } catch (error) {
                console.error("Error fetching data:", error);
            }
        }

        async function markAsCalled(id) {
            try {
                const csrfMeta = document.querySelector('meta[name="csrf-token"]');
                const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '';
                
                await fetch('/api/antrian/update_status', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                    body: JSON.stringify({ id: id })
                });
            } catch (error) {}
        }

        function speakName(queueNum) {
            if (!('speechSynthesis' in window)) return;
            
            window.speechSynthesis.cancel();
            
            let spelledNumber = queueNum.split('').join(' ');
            const textToSpeak = `Nomor antrean, ${spelledNumber}. Silakan masuk ke area foto.`;
            
            currentUtterance = new SpeechSynthesisUtterance(textToSpeak);
            currentUtterance.lang = 'id-ID';
            
            const idVoice = voices.find(v => v.lang === 'id-ID' || v.lang === 'id_ID');
            if (idVoice) currentUtterance.voice = idVoice;

            currentUtterance.rate = 0.9; 
            currentUtterance.pitch = 1.0; 
            currentUtterance.volume = 1;

            // Efek bel diputar sebelum suara TTS
            const bell = document.getElementById('audio-bell');
            if (bell) {
                bell.currentTime = 0;
                bell.play().then(() => {
                    setTimeout(() => {
                        window.speechSynthesis.speak(currentUtterance);
                    }, 1200);
                }).catch(e => {
                    // Fallback: kalau bel ditahan browser, langsung ngomong aja
                    window.speechSynthesis.speak(currentUtterance);
                });
            } else {
                window.speechSynthesis.speak(currentUtterance);
            }
        }

        window.callNext = function() {
            // Kalau antrean beneran kosong baru di-return
            if (queue.length === 0) return; 

            // Majuin antrean
            const currentFinished = queue.shift(); 
            markAsCalled(currentFinished.id);

            if (queue.length > 0) {
                const nextPerson = queue[0]; 
                const personNumber = getQueueNumber(nextPerson);
                
                renderDisplay();
                speakName(personNumber); // Panggil suara untuk antrean baru
            } else {
                // Udah nggak ada antrean, kosongkan layar tanpa suara
                renderDisplay();
            }
        }

        window.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                callNext();
            }
        });

        fetchDatabase(); 
        setInterval(fetchDatabase, 3000);

    </script>
</body>
</html>