<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>{{ $project->name }} - Snap Fun</title>
    <!-- Tailwind CSS & Icons -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <!-- GIF Library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gifshot/0.3.2/gifshot.min.js"></script>
    <style>
        body { 
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; 
            -webkit-tap-highlight-color: transparent;
            background-color: #f9fafb;
            color: #111827;
        }
        
        :root {
            --primary: #355faa;
            --action: #fbdc00;
        }

        /* Utilitas Interaktif */
        .btn-touch { transition: transform 0.1s; }
        .btn-touch:active { transform: scale(0.96); }
        
        /* Scrollbar Halus */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }

        /* Checkbox Custom untuk GIF */
        .gif-checkbox:checked + div {
            border-color: var(--primary);
            background-color: rgba(53, 95, 170, 0.1);
        }
        .gif-checkbox:checked + div .check-indicator {
            opacity: 1;
            transform: scale(1);
        }
    </style>
</head>
<body class="flex flex-col min-h-screen">
    
    <div class="relative flex flex-col h-screen overflow-hidden bg-[#f9fafb] flex-1">
        
        <!-- HEADER MODE GIF (FIXED POSITION, WARNA KUNING) -->
        <div id="gifSelectionHeader" class="hidden absolute top-0 left-0 right-0 bg-[#fbdc00] text-gray-900 px-5 py-4 z-[60] shadow-lg flex items-center justify-between animate-in slide-in-from-top-2">
            <div class="flex items-center gap-3">
                <div class="flex items-center justify-center w-8 h-8 rounded-full bg-[#355faa]/10 shrink-0">
                    <i data-lucide="mouse-pointer-click" size="16" class="text-[#355faa]"></i>
                </div>
                <span class="text-xs font-bold tracking-wide md:text-sm">Pilih beberapa foto untuk membuat GIF</span>
            </div>
            <button onclick="toggleGifMode()" class="p-2 transition-all rounded-full bg-black/5 hover:bg-black/10 btn-touch shrink-0 text-gray-900">
                <i data-lucide="x" size="18"></i>
            </button>
        </div>

        <!-- Header Customer Normal -->
        <header class="absolute top-0 z-20 flex items-center justify-between w-full px-5 py-4 bg-white border-b border-gray-200 shadow-sm shrink-0">
            <div class="overflow-hidden">
                <p class="text-[10px] font-bold text-[#355faa] uppercase tracking-widest mb-0.5">{{ $project->type }}</p>
                <h1 class="text-lg font-bold leading-tight text-gray-900 truncate max-w-[200px]">{{ $project->name }}</h1>
            </div>
            <!-- Tombol Kembali ke Dashboard -->
            <a href="/admin" class="flex items-center justify-center h-10 px-4 gap-2 text-xs font-bold text-[#355faa] transition-colors bg-blue-50 rounded-full hover:bg-blue-100 btn-touch">
                <i data-lucide="layout-dashboard" size="16"></i>
                <span class="hidden sm:block">Dashboard</span>
            </a>
        </header>

        <!-- Galeri Foto -->
        <main class="flex-1 overflow-y-auto pt-20 pb-32 px-4 md:px-8 mt-4 custom-scrollbar bg-[#f9fafb] max-w-7xl mx-auto w-full">
            
            <!-- COUNTDOWN LEBIH MENONJOL -->
            <div class="flex items-center gap-4 p-5 mt-2 mb-8 text-white shadow-lg bg-[#355faa] rounded-2xl shadow-blue-900/10">
                <div class="flex items-center justify-center w-12 h-12 bg-white/20 rounded-xl shrink-0">
                    <i data-lucide="clock" size="24" class="text-[#fbdc00]"></i>
                </div>
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-white/80 mb-1">Waktu mundur untuk link diakses</p>
                    <p class="text-lg font-black tracking-wide md:text-2xl" id="timer" data-expire="{{ $project->expired_at ? $project->expired_at->timestamp : 0 }}">Menghitung...</p>
                </div>
            </div>

            <!-- Grid Foto -->
            <div class="grid grid-cols-2 gap-3 md:grid-cols-4 lg:grid-cols-5" id="galleryGrid">
                @if(is_array($project->photos))
                    @foreach ($project->photos as $idx => $photo)
                    <div class="relative overflow-hidden bg-white border border-gray-100 shadow-sm photo-item aspect-[4/5] rounded-xl group">
                        <img src="{{ Storage::url($photo) }}" class="object-cover w-full h-full transition-transform duration-500 group-hover:scale-110" loading="lazy">
                        
                        <!-- Overlay Download (Normal Mode) -->
                        <div class="absolute inset-0 flex items-end p-3 transition-opacity opacity-0 normal-overlay bg-black/40 group-hover:opacity-100">
                            <a href="{{ Storage::url($photo) }}" download="SnapFun_{{ $project->type }}_{{ $project->name }}_{{ $idx + 1 }}.{{ pathinfo($photo, PATHINFO_EXTENSION) }}" class="flex items-center justify-center w-full gap-2 py-2.5 text-[10px] font-bold tracking-widest text-center uppercase bg-white rounded-lg shadow-lg text-[#355faa] hover:bg-gray-50 btn-touch">
                                <i data-lucide="download" size="14"></i> Unduh
                            </a>
                        </div>

                        <!-- Selection Overlay (GIF Mode) -->
                        <label class="absolute inset-0 hidden cursor-pointer selection-overlay bg-white/0">
                            <input type="checkbox" class="hidden gif-checkbox" value="{{ Storage::url($photo) }}">
                            <div class="absolute inset-0 flex items-start justify-end p-2 transition-all border-4 border-transparent">
                                <div class="flex items-center justify-center w-6 h-6 text-white transition-all transform scale-50 rounded-full shadow-lg opacity-0 check-indicator bg-[#355faa]">
                                    <i data-lucide="check" size="14"></i>
                                </div>
                            </div>
                        </label>
                    </div>
                    @endforeach
                @else
                    <div class="py-10 text-center col-span-full">
                        <p class="text-gray-400">Belum ada foto.</p>
                    </div>
                @endif
            </div>
            
            <!-- Footer -->
            <div class="px-6 pb-12 mt-10 text-center">
                <p class="text-xs font-bold tracking-[0.3em] text-gray-400 uppercase">Snap Fun Studio</p>
            </div>
        </main>

        <!-- Bottom Bar (Actions) -->
        <div class="fixed bottom-0 left-0 right-0 z-30 px-6 pt-4 pb-6 bg-white border-t border-gray-200 shadow-[0_-5px_20px_rgba(0,0,0,0.05)]">
            <!-- Normal Mode -->
            <div id="normalActions" class="flex gap-3 max-w-7xl mx-auto">
                <button onclick="toggleGifMode()" class="flex items-center justify-center flex-1 h-14 gap-2 text-[10px] font-bold tracking-widest text-gray-700 uppercase transition-all bg-white border border-gray-200 shadow-sm rounded-2xl md:text-xs hover:border-[#355faa] hover:text-[#355faa] btn-touch">
                    <i data-lucide="film" size="18"></i> Buat GIF
                </button>
                <button onclick="downloadAll()" class="flex items-center justify-center gap-2 text-xs font-bold tracking-widest text-gray-900 uppercase shadow-lg flex-[2] bg-[#fbdc00] h-14 rounded-2xl md:text-sm shadow-yellow-500/20 btn-touch">
                    <i data-lucide="download-cloud" size="20"></i> Simpan Semua
                </button>
            </div>
            
            <!-- GIF Mode Actions -->
            <div id="gifActions" class="hidden flex gap-3 max-w-7xl mx-auto">
                <button onclick="toggleGifMode()" class="flex-1 text-xs font-bold tracking-widest text-gray-600 uppercase bg-gray-100 h-14 rounded-2xl btn-touch">Batal</button>
                <button onclick="generateGIF()" id="btnGenerateGif" class="flex items-center justify-center gap-2 text-xs font-bold tracking-widest text-white uppercase shadow-lg flex-[2] bg-[#355faa] h-14 rounded-2xl shadow-blue-900/20 btn-touch">
                    Proses GIF (<span id="selectedCount">0</span>)
                </button>
            </div>
        </div>

        <!-- Download Info Modal -->
        <div id="downloadModal" class="fixed inset-0 z-50 items-center justify-center hidden p-6 bg-black/80 backdrop-blur-sm flex">
            <div class="relative w-full max-w-sm p-8 text-center bg-white shadow-2xl rounded-[2rem] animate-in zoom-in duration-300">
                <div class="mx-auto mt-4 mb-6 border-4 border-gray-200 rounded-full animate-spin h-16 w-16 border-t-[#355faa]"></div>
                <h3 class="mb-2 text-xl font-bold text-gray-900">Tunggu Sebentar...</h3>
                <p class="text-sm font-medium leading-relaxed text-gray-500">Sabar ya, foto kamu sedang disiapkan untuk di-download.</p>
            </div>
        </div>

        <!-- GIF Result Modal -->
        <div id="gifModal" class="fixed inset-0 z-50 items-center justify-center hidden p-6 bg-black/80 backdrop-blur-sm flex">
            <div class="w-full max-w-sm p-6 bg-white shadow-2xl rounded-[2rem] animate-in zoom-in duration-300">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-gray-900">GIF Anda Siap!</h3>
                    <button onclick="closeGifModal()" class="text-gray-400 hover:text-gray-600"><i data-lucide="x"></i></button>
                </div>
                <div class="relative flex items-center justify-center overflow-hidden border border-gray-200 mb-4 bg-gray-100 aspect-square rounded-xl">
                    <img id="gifResultImage" class="object-contain w-full h-full">
                    <div id="gifLoading" class="absolute inset-0 flex flex-col items-center justify-center hidden bg-white/80">
                        <div class="w-10 h-10 mb-2 border-4 border-gray-200 rounded-full animate-spin border-t-[#355faa]"></div>
                        <p class="text-[10px] font-bold text-[#355faa] uppercase tracking-widest">Memproses...</p>
                    </div>
                </div>
                <a id="gifDownloadLink" href="#" download="SnapFun_GIF.gif" class="flex items-center justify-center w-full gap-2 py-3 font-bold text-white bg-[#355faa] mb-2 rounded-xl btn-touch">
                    <i data-lucide="download" size="18"></i> Unduh GIF
                </a>
            </div>
        </div>
    </div>

    <!-- JAVASCRIPT LOGIC -->
    <script>
        lucide.createIcons();

        // Data dari Laravel Backend
        const albumData = { 
            paket: '{{ $project->type }}', 
            name: '{{ $project->name }}' 
        };
        const photos = @json(is_array($project->photos) ? $project->photos : []);
        
        // Timer Logic
        const timerEl = document.getElementById('timer');
        if(timerEl && timerEl.dataset.expire > 0) {
            const updateTimer = () => {
                const diff = parseInt(timerEl.dataset.expire) * 1000 - new Date().getTime();
                if(diff < 0) { timerEl.innerText = 'Waktu Habis'; return; }
                const d = Math.floor(diff / (1000 * 60 * 60 * 24));
                const h = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const m = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                timerEl.innerText = `${d} Hari ${h} Jam ${m} Menit`;
            };
            updateTimer();
            setInterval(updateTimer, 60000);
        }

        // Logic Unduh Semua (Download satu per satu untuk kompatibilitas Web)
        async function downloadAll() {
            if (photos.length === 0) {
                alert("Tidak ada foto untuk didownload.");
                return;
            }

            const modal = document.getElementById('downloadModal');
            modal.classList.remove('hidden');

            const baseUrl = '{{ Storage::url("") }}';

            for(let i=0; i<photos.length; i++) {
                const a = document.createElement('a');
                const ext = photos[i].split('.').pop();
                const customName = `SnapFun_${albumData.paket}_${albumData.name}_${i+1}.${ext}`;
                
                a.href = baseUrl + photos[i];
                a.download = customName;
                document.body.appendChild(a); 
                a.click(); 
                document.body.removeChild(a);
                
                // Jeda agar browser tidak mendeteksi sebagai spam popup
                await new Promise(r => setTimeout(r, 600)); 
            }
            
            setTimeout(() => { modal.classList.add('hidden'); }, 1000);
        }

        // Logika GIF
        let gifMode = false;

        function toggleGifMode() {
            gifMode = !gifMode;
            const overlays = document.querySelectorAll('.selection-overlay');
            const normalOverlays = document.querySelectorAll('.normal-overlay');
            const gifHeader = document.getElementById('gifSelectionHeader');
            
            if (gifMode) {
                document.getElementById('normalActions').classList.add('hidden');
                document.getElementById('gifActions').classList.remove('hidden');
                if (gifHeader) gifHeader.classList.remove('hidden');
                overlays.forEach(el => el.classList.remove('hidden'));
                normalOverlays.forEach(el => el.classList.add('hidden')); // Sembunyikan hover unduh
            } else {
                document.getElementById('normalActions').classList.remove('hidden');
                document.getElementById('gifActions').classList.add('hidden');
                if (gifHeader) gifHeader.classList.add('hidden');
                overlays.forEach(el => {
                    el.classList.add('hidden');
                    el.querySelector('input').checked = false; 
                });
                normalOverlays.forEach(el => el.classList.remove('hidden')); // Munculkan hover unduh
                updateSelectedCount();
            }
        }

        document.querySelectorAll('.gif-checkbox').forEach(cb => {
            cb.addEventListener('change', updateSelectedCount);
        });

        function updateSelectedCount() {
            const count = document.querySelectorAll('.gif-checkbox:checked').length;
            document.getElementById('selectedCount').innerText = count;
        }

        function generateGIF() {
            const selectedEls = document.querySelectorAll('.gif-checkbox:checked');
            if (selectedEls.length < 2) {
                alert("Pilih minimal 2 foto untuk membuat GIF.");
                return;
            }

            const images = Array.from(selectedEls).map(el => el.value);
            
            const modal = document.getElementById('gifModal');
            const img = document.getElementById('gifResultImage');
            const loading = document.getElementById('gifLoading');
            const dlBtn = document.getElementById('gifDownloadLink');
            
            modal.classList.remove('hidden');
            img.classList.add('hidden');
            loading.classList.remove('hidden');
            dlBtn.classList.add('hidden');

            const tempImg = new Image();
            tempImg.src = images[0];
            tempImg.onload = function() {
                const aspectRatio = tempImg.naturalWidth / tempImg.naturalHeight;
                const gifW = 600; 
                const gifH = Math.round(gifW / aspectRatio);

                gifshot.createGIF({
                    images: images,
                    gifWidth: gifW,
                    gifHeight: gifH,
                    interval: 0.5, 
                    numFrames: 10, 
                    sampleInterval: 10 
                }, function(obj) {
                    if(!obj.error) {
                        img.src = obj.image;
                        img.classList.remove('hidden');
                        loading.classList.add('hidden');
                        dlBtn.href = obj.image;
                        dlBtn.classList.remove('hidden');
                    }
                });
            };
        }

        function closeGifModal() {
            document.getElementById('gifModal').classList.add('hidden');
        }
    </script>
</body>
</html>