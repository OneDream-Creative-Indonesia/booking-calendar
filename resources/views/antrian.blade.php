<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Wajib di Laravel untuk request POST via AJAX/Fetch -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sistem Pemanggil Pelanggan</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="bg-slate-50 text-slate-800 font-sans min-h-screen">

    <!-- Header -->
    <header class="bg-blue-600 text-white p-4 shadow-md">
        <div class="max-w-6xl mx-auto flex items-center gap-3">
            <i data-lucide="volume-2" class="w-7 h-7"></i>
            <h1 class="text-2xl font-bold tracking-wide">Sistem Pemanggil Pelanggan</h1>
        </div>
    </header>

    <main class="max-w-6xl mx-auto p-4 md:p-6 mt-4">
        
        <div id="tts-warning" class="hidden mb-6 p-4 bg-red-100 text-red-700 rounded-lg flex items-center gap-3 border border-red-200">
            <i data-lucide="info" class="w-6 h-6 flex-shrink-0"></i>
            <p><strong>Peringatan:</strong> Browser Anda tidak mendukung fitur Text-to-Speech. Suara tidak akan berfungsi.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            
            <!-- Kolom Kiri -->
            <div class="lg:col-span-4 flex flex-col gap-6">
                <!-- Panel Integrasi Database -->
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-2 text-blue-600">
                            <i data-lucide="database" class="w-5 h-5"></i>
                            <h2 class="text-lg font-semibold">Koneksi Database</h2>
                        </div>
                        <div id="status-badge" class="flex items-center gap-1.5 px-2.5 py-1 bg-green-100 text-green-700 rounded-full text-xs font-medium border border-green-200 transition-colors duration-300">
                            <i data-lucide="wifi" class="w-3 h-3 animate-pulse" id="wifi-icon"></i>
                            <span id="status-text">Terhubung</span>
                        </div>
                    </div>
                    
                    <div class="bg-slate-50 p-3 rounded-lg border border-slate-100 flex items-center justify-between">
                        <span class="text-xs text-slate-500 flex items-center gap-1">
                            <i data-lucide="activity" class="w-3.5 h-3.5"></i> Sync terakhir:
                        </span>
                        <span id="last-fetch-time" class="text-xs font-mono font-medium text-slate-700">-</span>
                    </div>

                    <!-- Area Pesan Error Database -->
                    <div id="db-error-msg" class="hidden mt-3 text-xs text-red-600 bg-red-50 p-2.5 rounded-lg border border-red-200 break-words font-mono">
                        <!-- Pesan error SQL akan tampil di sini -->
                    </div>
                </div>

                <!-- Panel Daftar Antrean (Menunggu) -->
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 flex-grow flex flex-col h-[400px]">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-2 text-blue-600">
                            <i data-lucide="users" class="w-5 h-5"></i>
                            <h2 class="text-lg font-semibold">Menunggu (<span id="queue-count">0</span>)</h2>
                        </div>
                    </div>
                    
                    <div id="queue-list" class="overflow-y-auto flex-grow pr-2 space-y-2">
                        <!-- List antrean akan dimuat oleh JS -->
                    </div>
                </div>
            </div>

            <!-- Kolom Kanan -->
            <div class="lg:col-span-8 flex flex-col gap-6">
                <!-- Layar Utama -->
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-8 flex flex-col items-center justify-center min-h-[350px] text-center relative overflow-hidden">
                    
                    <div id="speaking-indicator" class="hidden absolute top-6 right-6 flex items-center gap-2 text-green-600 animate-pulse">
                        <i data-lucide="mic" class="w-6 h-6"></i>
                        <span class="font-semibold text-sm tracking-widest uppercase">Sedang Memanggil</span>
                    </div>

                    <h3 class="text-slate-500 text-xl font-medium mb-4 uppercase tracking-widest">
                        Panggilan Saat Ini
                    </h3>
                    
                    <div class="mb-8">
                        <h2 id="current-name" class="text-4xl text-slate-300 font-bold max-w-full px-4 break-words">
                            Belum ada panggilan
                        </h2>
                    </div>

                    <button id="btn-recall" class="hidden flex items-center gap-2 text-slate-500 hover:text-blue-600 transition-colors bg-slate-100 hover:bg-blue-50 py-2 px-6 rounded-full font-medium">
                        <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                        Ulangi Panggilan
                    </button>
                </div>

                <!-- Tombol Aksi Utama -->
                <button id="btn-call-next" disabled class="relative overflow-hidden w-full py-5 rounded-xl flex items-center justify-center gap-4 text-2xl font-bold text-white transition-all transform active:scale-[0.98] shadow-lg bg-slate-300 cursor-not-allowed shadow-none">
                    <i data-lucide="play" class="w-8 h-8 fill-current"></i>
                    Panggil Selanjutnya
                    <span id="enter-hint" class="hidden absolute bottom-2 right-4 text-xs font-normal opacity-70 flex items-center gap-1 bg-black/20 px-2 py-1 rounded-md">
                        Tekan <kbd class="font-mono bg-white/20 px-1 rounded">Enter</kbd>
                    </span>
                </button>

                <!-- Panel Riwayat (Sudah Dipanggil) -->
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 mt-2">
                    <div class="flex items-center gap-2 text-slate-600 mb-4 border-b border-slate-100 pb-2">
                        <i data-lucide="check-circle" class="w-5 h-5 text-green-500"></i>
                        <h2 class="text-lg font-semibold">Sudah Dipanggil (Sesi Ini)</h2>
                    </div>
                    <div id="history-list" class="flex flex-wrap gap-2 max-h-32 overflow-y-auto pr-2">
                        <!-- Riwayat akan dimuat oleh JS -->
                    </div>
                </div>
            </div>

        </div>
    </main>

    <!-- JavaScript untuk interaksi UI & Panggilan -->
    <script>
        // Inisialisasi ikon
        lucide.createIcons();

        // State Aplikasi
        let queue = [];
        let historyList = [];
        let currentPerson = null;
        let isSpeaking = false;
        let voices = [];
        let pollingInterval;

        // Elemen DOM
        const queueListEl = document.getElementById('queue-list');
        const queueCountEl = document.getElementById('queue-count');
        const historyListEl = document.getElementById('history-list');
        const currentNameEl = document.getElementById('current-name');
        const btnCallNext = document.getElementById('btn-call-next');
        const btnRecall = document.getElementById('btn-recall');
        const speakingIndicator = document.getElementById('speaking-indicator');
        const enterHint = document.getElementById('enter-hint');
        const lastFetchTimeEl = document.getElementById('last-fetch-time');
        
        // Setup Suara
        if (!('speechSynthesis' in window)) {
            document.getElementById('tts-warning').classList.remove('hidden');
        } else {
            const loadVoices = () => { voices = window.speechSynthesis.getVoices(); };
            loadVoices();
            if (window.speechSynthesis.onvoiceschanged !== undefined) {
                window.speechSynthesis.onvoiceschanged = loadVoices;
            }
        }

        // Helper untuk memanggil nama orang dengan fleksibel (menghindari error nama kolom)
        function getPersonName(person) {
            if (!person) return '';
            return person.nama || person.name || person.Name || 'Pelanggan';
        }

        // Fungsi Render UI Antrean (Menunggu)
        function renderQueue() {
            queueCountEl.textContent = queue.length;
            
            if (queue.length === 0) {
                queueListEl.innerHTML = `
                    <div class="h-full flex flex-col items-center justify-center text-slate-400 gap-2">
                        <i data-lucide="users" class="w-10 h-10 opacity-50"></i>
                        <p class="text-sm">Antrean kosong</p>
                    </div>`;
                lucide.createIcons();
            } else {
                queueListEl.innerHTML = queue.map((person, index) => `
                    <div class="p-3 bg-slate-50 border border-slate-100 rounded-lg flex items-center gap-3">
                        <div class="bg-blue-100 text-blue-700 font-bold rounded-full w-8 h-8 flex items-center justify-center flex-shrink-0 text-sm">
                            ${index + 1}
                        </div>
                        <span class="font-medium text-slate-700 truncate">${getPersonName(person)}</span>
                    </div>
                `).join('');
            }

            updateCallButton();
        }

        // Fungsi Render UI Riwayat (Sudah Dipanggil)
        function renderHistory() {
            if (historyList.length === 0) {
                historyListEl.innerHTML = `<span class="text-sm text-slate-400 italic">Belum ada data yang dipanggil di sesi ini</span>`;
            } else {
                historyListEl.innerHTML = historyList.map(person => `
                    <div class="px-3 py-1.5 bg-slate-100 border border-slate-200 rounded-full text-sm font-medium text-slate-600 flex items-center gap-1.5 mb-1 mr-1 inline-flex">
                        <i data-lucide="check" class="w-3.5 h-3.5 text-green-500"></i>
                        ${getPersonName(person)}
                    </div>
                `).join('');
                lucide.createIcons();
            }
        }

        // Update tampilan tombol panggil
        function updateCallButton() {
            if (queue.length === 0 || isSpeaking) {
                btnCallNext.disabled = true;
                btnCallNext.className = "relative overflow-hidden w-full py-5 rounded-xl flex items-center justify-center gap-4 text-2xl font-bold text-white transition-all transform shadow-lg bg-slate-300 cursor-not-allowed shadow-none";
                enterHint.classList.add('hidden');
            } else {
                btnCallNext.disabled = false;
                btnCallNext.className = "relative overflow-hidden w-full py-5 rounded-xl flex items-center justify-center gap-4 text-2xl font-bold text-white transition-all transform active:scale-[0.98] shadow-lg bg-green-500 hover:bg-green-600 hover:shadow-green-500/30";
                enterHint.classList.remove('hidden');
            }
        }

        // Mengatur tampilan error pada badge status
        function setStatusBadge(isSuccess, message = "") {
            const badge = document.getElementById('status-badge');
            const text = document.getElementById('status-text');
            const icon = document.getElementById('wifi-icon');
            const errorContainer = document.getElementById('db-error-msg');

            if (isSuccess) {
                badge.className = "flex items-center gap-1.5 px-2.5 py-1 bg-green-100 text-green-700 rounded-full text-xs font-medium border border-green-200 transition-colors duration-300";
                text.textContent = "Terhubung";
                icon.classList.add('animate-pulse');
                errorContainer.classList.add('hidden'); // Sembunyikan pesan error
            } else {
                badge.className = "flex items-center gap-1.5 px-2.5 py-1 bg-red-100 text-red-700 rounded-full text-xs font-medium border border-red-200 transition-colors duration-300";
                text.textContent = "Error Query";
                icon.classList.remove('animate-pulse');
                
                // Tampilkan pesan error aktual di layar agar mudah didebug
                errorContainer.classList.remove('hidden');
                errorContainer.textContent = message;
                console.error("Database Error:", message);
            }
        }

        // Menarik data dari API LARAVEL
        async function fetchDatabase() {
            try {
                // Menembak ke route API Laravel yang baru kita buat
                const response = await fetch('/api/antrian/get_queue');
                const result = await response.json();
                
                if (result.success) {
                    setStatusBadge(true);
                    
                    queue = result.data; 
                    
                    renderQueue();
                    renderHistory(); 
                    
                    const now = new Date();
                    lastFetchTimeEl.textContent = now.getHours().toString().padStart(2, '0') + ':' + 
                                                  now.getMinutes().toString().padStart(2, '0') + ':' + 
                                                  now.getSeconds().toString().padStart(2, '0');
                } else {
                    setStatusBadge(false, result.message);
                }
            } catch (error) {
                setStatusBadge(false, "Gagal koneksi API/Jaringan");
            }
        }

        // Menghapus data dari database setelah dipanggil
        async function markAsCalled(id) {
            try {
                // Menembak ke route API POST Laravel dan menyertakan CSRF TOKEN
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                
                await fetch('/api/antrian/update_status', {
                    method: 'POST',
                    headers: { 
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken 
                    },
                    body: JSON.stringify({ id: id })
                });
            } catch (error) {
                console.error('Error deleting data:', error);
            }
        }

        // Fungsi berbicara (TTS)
        function speakName(name) {
            if (!('speechSynthesis' in window)) return;
            window.speechSynthesis.cancel();
            
            isSpeaking = true;
            speakingIndicator.classList.remove('hidden');
            btnRecall.disabled = true;
            updateCallButton();
            
            const utterance = new SpeechSynthesisUtterance(`${name}, Ayo Foto!`);
            utterance.lang = 'id-ID';
            
            const idVoice = voices.find(v => v.lang === 'id-ID' || v.lang === 'id_ID');
            if (idVoice) utterance.voice = idVoice;

            utterance.rate = 1.05; 
            utterance.pitch = 1.0; 
            utterance.volume = 1;

            utterance.onend = () => {
                isSpeaking = false;
                speakingIndicator.classList.add('hidden');
                btnRecall.disabled = false;
                updateCallButton();
            };
            
            utterance.onerror = () => {
                isSpeaking = false;
                speakingIndicator.classList.add('hidden');
                btnRecall.disabled = false;
                updateCallButton();
            };

            window.speechSynthesis.speak(utterance);
        }

        // Memanggil orang berikutnya
        function callNext() {
            if (queue.length === 0 || isSpeaking) return;

            const nextPerson = queue[0];
            currentPerson = nextPerson;
            const personName = getPersonName(nextPerson);
            
            // Tampilkan di layar utama
            currentNameEl.textContent = personName;
            currentNameEl.className = "text-6xl md:text-8xl font-black text-blue-700 tracking-tight leading-tight break-words max-w-full px-4";
            btnRecall.classList.remove('hidden');
            
            // Masukkan ke history visual secara realtime
            if (!historyList.some(p => p.id === nextPerson.id)) {
                historyList.unshift(nextPerson);
            }
            renderHistory();

            // Hapus dari antrean lokal dan update DB (Ubah Status -> DELETE)
            queue.shift();
            renderQueue();
            markAsCalled(nextPerson.id);
            
            speakName(personName);
        }

        // Event Listeners
        btnCallNext.addEventListener('click', callNext);
        
        btnRecall.addEventListener('click', () => {
            if (currentPerson && !isSpeaking) speakName(getPersonName(currentPerson));
        });

        // Global Enter Key
        window.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                callNext();
            }
        });

        // Mulai Polling 
        fetchDatabase(); // Panggilan pertama
        pollingInterval = setInterval(fetchDatabase, 3000); // Polling setiap 3 detik

    </script>
</body>
</html>