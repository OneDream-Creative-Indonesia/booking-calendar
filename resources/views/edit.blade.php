<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Snap Edit | Frame Editor</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Fabric.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fabric.js/5.3.1/fabric.min.js"></script>
    
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f9fafb;
            color: #111827;
            overflow: hidden; 
        }

        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #9ca3af; }

        .workspace-bg {
            background-color: #f3f4f6;
            background-image: radial-gradient(#d1d5db 1px, transparent 1px);
            background-size: 24px 24px;
        }

        /* Drag over effect for Canvas */
        .drag-active {
            background-color: rgba(53, 95, 170, 0.05);
            outline: 2px dashed #355faa;
            outline-offset: -10px;
        }

        /* Frame Selection Styles */
        .frame-option input:checked + div {
            border-color: #355faa;
            background-color: #eff6ff;
        }
        .frame-option input:checked + div .check-icon {
            opacity: 1;
            transform: scale(1);
        }

        #active-crop-ui, #active-zoom-ui, #export-modal-overlay {
            animation: fadeIn 0.2s ease-out;
        }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

        #setup-page {
            animation: slideIn 0.3s ease-out;
        }
        @keyframes slideIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }

        /* Canvas Wrapper untuk skala High-Res 300DPI secara Responsif */
        #canvas-wrapper {
            transition: transform 0.1s ease-out;
            background-color: rgba(250, 204, 21, 0.3); /* Kuning Transparan */
            transform-origin: top left;
        }
        
        /* Kontainer DOM statis untuk menghindari scrollbar raksasa */
        #scale-container {
            transition: width 0.1s ease-out, height 0.1s ease-out;
        }

        @media print {
            @page { margin: 0; size: auto; }
            header, .left-sidebar, .right-sidebar, .floating-bar, #active-crop-ui, #active-zoom-ui, #setup-page, #export-modal-overlay { display: none !important; }
            html, body { 
                width: 100%; height: 100%; 
                background-color: white !important; 
                overflow: hidden !important; 
                margin: 0 !important; padding: 0 !important;
            }
            #print-container { 
                display: flex !important; align-items: center; justify-content: center; 
                width: 100%; height: 100%; position: fixed; top: 0; left: 0;
                z-index: 9999; background: white;
            }
            #print-image { width: 100%; height: 100%; object-fit: contain; display: block; }
        }
    </style>
</head>
<body class="h-screen flex flex-col">

    <!-- EXPORT MODAL -->
    <div id="export-modal-overlay" class="fixed inset-0 z-[100] bg-black/60 backdrop-blur-sm hidden items-center justify-center p-4">
        <div class="bg-white rounded-3xl shadow-2xl max-w-md w-full p-8 space-y-6 transform transition-all">
            <div class="text-center">
                <div class="w-16 h-16 bg-blue-50 text-[#355faa] rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <i class="fa-solid fa-file-export text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900">Siap untuk Export?</h3>
                <p class="text-gray-500 text-sm mt-1">File akan di-export dengan resolusi asli Frame (300 DPI).</p>
            </div>

            <div class="space-y-2">
                <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider">Nama Customer</label>
                <input type="text" id="export-customer-name" placeholder="Contoh: Budi Sudarsono" 
                    class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-[#355faa] focus:ring-2 focus:ring-[#355faa]/20 outline-none transition-all text-gray-800 font-medium">
                <p id="export-error" class="text-red-500 text-[10px] hidden font-medium italic">* Mohon masukkan nama customer!</p>
            </div>

            <div class="flex gap-3 pt-2">
                <button onclick="closeExportModal()" class="flex-1 py-3 rounded-xl font-semibold text-gray-600 hover:bg-gray-100 transition-colors">
                    Batal
                </button>
                <button onclick="processFinalExport()" class="flex-1 py-3 rounded-xl font-bold text-white bg-[#355faa] hover:bg-[#2d5191] shadow-lg shadow-blue-200 transition-all flex items-center justify-center gap-2">
                    Export PNG <i class="fa-solid fa-download"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- SETUP PAGE -->
    <div id="setup-page" class="fixed inset-0 z-50 bg-gray-50 overflow-auto">
        <div class="min-h-screen flex flex-col items-center justify-center p-6">
            <div class="max-w-4xl w-full bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden flex flex-col md:flex-row shrink-0 my-auto">
                
                <!-- Setup Header/Info -->
                <div class="w-full md:w-1/3 bg-[#355faa] p-8 text-white flex flex-col justify-between">
                    <div>
                        <div class="w-12 h-12 rounded-xl bg-white/20 flex items-center justify-center mb-6">
                            <i class="fa-solid fa-layer-group text-2xl"></i>
                        </div>
                        <h1 class="text-3xl font-bold mb-2">Snap Edit</h1>
                        <p class="text-white/80 text-sm leading-relaxed">Persiapkan area kerja Anda. Upload foto, referensi grid, lalu pilih template Frame dari sistem.</p>
                    </div>
                    <div class="mt-8 text-xs text-white/50">
                        &copy; 2026 Snap Edit Frame Editor
                    </div>
                </div>

                <!-- Setup Form -->
                <div class="w-full md:w-2/3 p-8 space-y-6">
                    
                    <!-- 1. Photos -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            1. Upload Foto-foto <span class="text-red-500">*</span>
                        </label>
                        <label class="flex flex-col items-center gap-2 p-6 border-2 border-dashed border-gray-200 rounded-xl cursor-pointer hover:bg-gray-50 hover:border-[#355faa] transition-all text-center">
                            <i class="fa-solid fa-images text-2xl text-gray-400"></i>
                            <span id="label-photos-file" class="text-sm text-gray-500">Klik untuk upload foto customer sekaligus</span>
                            <input type="file" id="input-photos-file" accept="image/*" multiple class="hidden" onchange="updateFileLabel(this, 'label-photos-file', true)">
                        </label>
                    </div>

                     <!-- 2. Grid Preview -->
                     <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            2. Import Referensi Nomor Grid (Preview)
                        </label>
                        <label class="flex items-center gap-3 p-3 border border-dashed border-gray-300 rounded-xl cursor-pointer hover:bg-gray-50 transition-colors group">
                            <div class="w-10 h-10 rounded-lg bg-indigo-50 text-indigo-500 flex items-center justify-center group-hover:scale-110 transition-transform">
                                <i class="fa-solid fa-eye"></i>
                            </div>
                            <div class="flex-1">
                                <span id="label-grid-file" class="text-sm text-gray-500">Upload Gambar Grid (Opsional)</span>
                            </div>
                            <input type="file" id="input-grid-file" accept="image/*" class="hidden" onchange="updateFileLabel(this, 'label-grid-file')">
                        </label>
                    </div>

                    <!-- 3. Pilih Frame dari Backend -->
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label class="block text-sm font-semibold text-gray-700">
                                3. Pilih Frame <span class="text-red-500">*</span>
                            </label>
                            <span class="text-[10px] text-gray-400 px-2 py-1 bg-gray-100 rounded-md">Data dari Database</span>
                        </div>
                        <p class="text-xs text-gray-500 mb-3">Pilih frame di dalam folder (Sistem Backend).</p>
                        
                        <!-- Container Frame Folders (Injected by JS via API) -->
                        <div id="frame-folders-container" class="space-y-3 max-h-80 overflow-y-auto pr-2 custom-scrollbar">
                             <!-- Loader -->
                             <div class="text-center p-4 text-gray-500 text-xs">
                                <i class="fa-solid fa-spinner fa-spin mr-2"></i> Mengambil data frame dari database...
                            </div>
                        </div>
                    </div>

                    <div class="pt-4">
                        <button onclick="startEditor()" class="w-full py-4 rounded-xl bg-[#355faa] hover:bg-[#2d5191] text-white font-bold shadow-lg shadow-indigo-200 transition-all active:scale-[0.99] flex items-center justify-center gap-2">
                            Lanjut Editing <i class="fa-solid fa-arrow-right"></i>
                        </button>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- MAIN APP (Initially Hidden) -->
    <div id="app-container" class="hidden h-full flex-col">
        
        <!-- TOP HEADER -->
        <header class="h-14 bg-white border-b border-gray-200 flex items-center justify-between px-6 z-30 relative shadow-sm shrink-0">
            <div class="flex items-center gap-3">
                <button onclick="backToSetup()" class="text-gray-400 hover:text-gray-800 transition-colors mr-2" title="Kembali ke Setup">
                    <i class="fa-solid fa-arrow-left"></i>
                </button>
                <div class="w-8 h-8 rounded-lg flex items-center justify-center shadow-md shadow-[#355faa]/20" style="background-color: #355faa;">
                    <i class="fa-solid fa-layer-group text-white text-sm"></i>
                </div>
                <h1 class="font-semibold text-lg tracking-tight text-gray-900">Snap Edit</h1>
            </div>

            <div class="flex items-center gap-4">
                <div class="text-xs font-medium px-3 py-1.5 bg-gray-100 rounded-lg text-gray-600 border border-gray-200 flex items-center gap-2">
                    <i class="fa-solid fa-crop-simple"></i>
                    <span id="current-canvas-size">Memuat Frame...</span>
                </div>

                <div class="h-6 w-px bg-gray-200 mx-2"></div>

                <button onclick="printCanvas()" class="bg-white px-4 py-1.5 rounded-lg text-sm font-semibold transition-colors border flex items-center gap-2 shadow-sm hover:bg-gray-50" style="color: #355faa; border-color: rgba(53, 95, 170, 0.2);" title="Print (P)">
                    <i class="fa-solid fa-print"></i> Print
                </button>

                <!-- EXPORT BUTTON -->
                <button onclick="openExportModal()" class="text-black hover:opacity-90 px-4 py-1.5 rounded-lg text-sm font-semibold transition-colors shadow-lg shadow-yellow-200" style="background-color: #fbdc00;" title="Export (E)">
                    Export
                </button>
            </div>
        </header>

        <div class="flex-1 flex overflow-hidden">
            
            <!-- LEFT SIDEBAR: GRID & ASSETS -->
            <aside class="w-64 bg-white border-r border-gray-200 flex flex-col z-20 left-sidebar">
                
                <!-- Section 1: Grid Preview -->
                <div class="h-1/2 border-b border-gray-200 flex flex-col">
                     <div class="p-3 border-b border-gray-100 bg-gray-50">
                        <h2 class="text-xs font-bold text-gray-500 uppercase tracking-widest flex items-center gap-2">
                            <i class="fa-solid fa-eye"></i> Referensi Nomor Grid
                        </h2>
                    </div>
                    <div class="flex-1 p-4 bg-gray-100 flex items-center justify-center overflow-hidden relative group">
                        <img id="grid-preview-img" src="" class="max-w-full max-h-full object-contain shadow-sm hidden">
                        <div id="no-grid-msg" class="text-center text-gray-400 text-xs">
                            <i class="fa-solid fa-table-cells text-2xl mb-1"></i><br>
                            Belum Ada Referensi
                        </div>
                    </div>
                </div>

                <!-- Section 2: Uploaded Photos -->
                <div class="h-1/2 flex flex-col">
                    <div class="p-3 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                        <h2 class="text-xs font-bold text-gray-500 uppercase tracking-widest flex items-center gap-2">
                            <i class="fa-regular fa-images"></i> Foto Customer
                        </h2>
                        <button onclick="document.getElementById('add-more-photos').click()" class="w-6 h-6 rounded-md bg-[#355faa] text-white flex items-center justify-center hover:bg-[#2d5191] transition-colors shadow-sm" title="Tambah Foto">
                            <i class="fa-solid fa-plus text-xs"></i>
                        </button>
                        <input type="file" id="add-more-photos" accept="image/*" multiple onchange="appendPhotos(this)" class="hidden">
                    </div>
                    <div id="photo-gallery" class="flex-1 overflow-y-auto p-3 grid grid-cols-2 gap-2 content-start">
                        <!-- Photos will be injected here via JS -->
                        <div class="col-span-2 text-center text-gray-400 text-sm mt-10">
                            Belum ada foto yang diupload.
                        </div>
                    </div>
                </div>
            </aside>

            <!-- CENTER: CANVAS WORKSPACE -->
            <!-- Flex center untuk memastikan scale container selalu presisi di tengah layar -->
            <main class="flex-1 relative workspace-bg overflow-auto p-8 flex items-center justify-center" id="workspace-container">
                
                <!-- DOM Stabilizer agar scrollbar mengukur dengan pas walau di-scale CSS -->
                <div id="scale-container">
                    <div class="shadow-2xl bg-white relative origin-top-left" id="canvas-wrapper">
                        <canvas id="c"></canvas>
                    </div>
                </div>

                <!-- FLOATING ZOOM SLIDER -->
                <div id="active-zoom-ui" class="fixed bottom-24 left-[calc(50%+10rem)] -translate-x-1/2 bg-white/90 backdrop-blur-md border border-gray-200 px-6 py-3 rounded-2xl shadow-xl flex flex-col items-center gap-2 z-40 hidden w-64">
                    <div class="flex justify-between w-full text-xs font-bold text-gray-600">
                        <span><i class="fa-solid fa-minus text-[10px]"></i> Zoom Out</span>
                        <span>Zoom In <i class="fa-solid fa-plus text-[10px]"></i></span>
                    </div>
                    <input type="range" id="floating-zoom-slider" min="0.1" max="3" step="0.05" value="1" oninput="updateGridImageZoom(this.value)" class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer">
                    <span class="text-[10px] text-gray-400">Geser untuk zoom foto dalam grid</span>
                </div>

                <!-- CROP CONFIRMATION OVERLAY -->
                <div id="active-crop-ui" class="fixed bottom-24 left-[calc(50%+10rem)] -translate-x-1/2 bg-gray-900/90 backdrop-blur text-white px-6 py-3 rounded-full shadow-2xl hidden z-50 flex items-center gap-4">
                    <span class="text-sm font-medium">Adjust Selection</span>
                    <div class="h-4 w-px bg-white/20"></div>
                    <button onclick="performCrop()" class="hover:opacity-80 font-bold text-sm flex items-center gap-1" style="color: #355faa;">
                        <i class="fa-solid fa-check"></i> Apply
                    </button>
                    <button onclick="cancelCrop()" class="hover:opacity-80 font-bold text-sm flex items-center gap-1" style="color: #d6314a;">
                        <i class="fa-solid fa-xmark"></i> Cancel
                    </button>
                    <span class="text-xs text-gray-400 ml-2">Press Enter</span>
                </div>

            </main>

            <!-- RIGHT SIDEBAR: PROPERTIES -->
            <aside class="w-72 bg-white border-l border-gray-200 flex flex-col z-20 right-sidebar">
                <div class="p-4 border-b border-gray-100">
                    <h2 class="text-xs font-bold text-gray-400 uppercase tracking-widest">Properties</h2>
                </div>

                <div class="flex-1 overflow-y-auto p-4 space-y-6" id="properties-panel">
                    
                    <div id="no-selection-msg" class="h-full flex flex-col items-center justify-center text-center text-gray-400 space-y-2 opacity-50">
                        <i class="fa-regular fa-object-group text-3xl"></i>
                        <p class="text-sm">Pilih foto untuk mengedit</p>
                    </div>

                    <div id="object-controls" class="hidden space-y-6">
                        
                        <!-- Header Selection Info -->
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg border border-gray-100 flex items-center justify-center text-xl" style="background-color: rgba(53, 95, 170, 0.1); color: #355faa;">
                                <i id="obj-icon" class="fa-solid fa-image"></i>
                            </div>
                            <div>
                                <p id="obj-type-label" class="font-medium text-sm text-gray-900">Photo</p>
                                <p class="text-xs text-gray-500">Selected</p>
                            </div>
                        </div>

                        <!-- ZOOM CONTROL FOR GRID IMAGES -->
                        <div id="zoom-control-panel" class="hidden border border-indigo-100 bg-indigo-50 p-4 rounded-xl">
                            <label class="text-xs font-bold text-indigo-800 mb-2 block flex justify-between">
                                <span>Zoom within Grid</span>
                                <i class="fa-solid fa-magnifying-glass"></i>
                            </label>
                            <input type="range" id="grid-zoom-slider" min="0.1" max="3" step="0.05" value="1" oninput="updateGridImageZoom(this.value)">
                            <div class="flex justify-between mt-1 text-[9px] text-gray-400">
                                <span>0.1x</span>
                                <span>1x</span>
                                <span>3x</span>
                            </div>
                            <p class="text-[10px] text-indigo-400 mt-2">Geser gambar untuk menyesuaikan (Pan)</p>
                        </div>

                        <div id="crop-tools-section">
                            <h3 class="text-xs font-semibold text-gray-400 mb-3 uppercase">Shape & Crop (C)</h3>
                            <div class="grid grid-cols-4 gap-2">
                                <button onclick="initCropMode('circle')" class="aspect-square bg-gray-50 hover:bg-gray-100 rounded-lg flex items-center justify-center text-gray-500 transition-all border border-transparent hover:border-gray-300" title="Circle">
                                    <i class="fa-regular fa-circle"></i>
                                </button>
                                <button onclick="initCropMode('square')" class="aspect-square bg-gray-50 hover:bg-gray-100 rounded-lg flex items-center justify-center text-gray-500 transition-all border border-transparent hover:border-gray-300" title="Square">
                                    <i class="fa-regular fa-square"></i>
                                </button>
                                <button onclick="initCropMode('heart')" class="aspect-square bg-gray-50 hover:bg-gray-100 rounded-lg flex items-center justify-center text-gray-500 transition-all border border-transparent hover:border-gray-300" title="Heart">
                                    <i class="fa-solid fa-heart"></i>
                                </button>
                                <button onclick="removeClip()" class="aspect-square rounded-lg flex items-center justify-center transition-all border border-transparent" style="background-color: rgba(214, 49, 74, 0.1); color: #d6314a;" title="Reset">
                                    <i class="fa-solid fa-ban"></i>
                                </button>
                            </div>
                            <p class="text-[10px] text-gray-400 mt-2 text-center">Double-click image to edit crop</p>
                        </div>

                        <div>
                            <h3 class="text-xs font-semibold text-gray-400 mb-3 uppercase">Arrangement</h3>
                            <div class="grid grid-cols-2 gap-2">
                                <button onclick="bringForward()" class="bg-gray-50 hover:bg-gray-100 py-2 rounded-lg text-sm text-gray-600 hover:text-gray-900 transition-all flex items-center justify-center gap-2 border border-transparent hover:border-gray-200" title="Shortcut: Ctrl + ]">
                                    <i class="fa-solid fa-arrow-up text-xs"></i> Forward
                                </button>
                                <button onclick="sendBackward()" class="bg-gray-50 hover:bg-gray-100 py-2 rounded-lg text-sm text-gray-600 hover:text-gray-900 transition-all flex items-center justify-center gap-2 border border-transparent hover:border-gray-200" title="Shortcut: Ctrl + [">
                                    <i class="fa-solid fa-arrow-down text-xs"></i> Backward
                                </button>
                                <button onclick="bringToFront()" class="bg-gray-50 hover:bg-gray-100 py-2 rounded-lg text-sm text-gray-600 hover:text-gray-900 transition-all flex items-center justify-center gap-2 border border-transparent hover:border-gray-200">
                                    <i class="fa-solid fa-angles-up text-xs"></i> Front
                                </button>
                                <button onclick="sendToBack()" class="bg-gray-50 hover:bg-gray-100 py-2 rounded-lg text-sm text-gray-600 hover:text-gray-900 transition-all flex items-center justify-center gap-2 border border-transparent hover:border-gray-200">
                                    <i class="fa-solid fa-angles-down text-xs"></i> Back
                                </button>
                            </div>
                        </div>

                        <div class="pt-4 border-t border-gray-100 space-y-2">
                            <button id="lockBtn" onclick="toggleLock()" class="w-full py-2.5 rounded-lg text-sm font-medium transition-all bg-gray-50 hover:bg-gray-100 text-gray-600 flex items-center justify-center gap-2 border border-transparent hover:border-gray-200">
                                <i class="fa-solid fa-lock-open"></i> Lock Position (Q)
                            </button>
                            <button onclick="deleteActiveObject()" class="w-full py-2.5 rounded-lg text-sm font-medium transition-all border border-transparent flex items-center justify-center gap-2" style="background-color: rgba(214, 49, 74, 0.1); color: #d6314a;">
                                <i class="fa-solid fa-trash"></i> Remove (D)
                            </button>
                        </div>

                    </div>
                </div>
            </aside>
        </div>
    </div>

    <script>
        // ==========================================
        // 1. FETCH DATA DARI BACKEND LARAVEL API
        // ==========================================
        
        let DATABASE_FRAMES = []; // Akan diisi dari database via API
        let selectedFrameData = null;

        // Event listener saat dokumen selesai dimuat
        document.addEventListener('DOMContentLoaded', async () => {
            await fetchFramesFromAPI();
        });

        // Fungsi utama untuk memanggil data dari backend (Route::get('/api/frames'))
        async function fetchFramesFromAPI() {
            const container = document.getElementById('frame-folders-container');
            
            try {
                // Tampilkan loader saat memuat data
                container.innerHTML = `
                    <div class="text-center p-4 text-gray-500 text-xs">
                        <i class="fa-solid fa-spinner fa-spin mr-2"></i> Mengambil data frame dari database...
                    </div>`;

                const response = await fetch('/api/get-frames', {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                if (!response.ok) {
                    throw new Error('Gagal mengambil data frame dari server');
                }
                
                const responseData = await response.json();
                
                // Menerima data JSON yang digenerate oleh Laravel Controller
                DATABASE_FRAMES = responseData.data ? responseData.data : responseData;

                if (!DATABASE_FRAMES || DATABASE_FRAMES.length === 0) {
                    container.innerHTML = '<div class="text-center p-4 text-gray-500 text-xs">Belum ada tipe frame yang dikonfigurasi di Dashboard.</div>';
                    return;
                }

                renderFolders();

            } catch (error) {
                console.error('Error memuat data API:', error);
                container.innerHTML = `
                    <div class="text-center p-4 text-red-500 text-xs border border-red-200 bg-red-50 rounded-lg">
                        Terjadi kesalahan koneksi saat memuat data frame dari database. <br><br>
                        <button onclick="fetchFramesFromAPI()" class="underline font-bold">Coba Lagi</button>
                    </div>`;
            }
        }

        function toggleFolder(folderId) {
            const content = document.getElementById('folder-content-' + folderId);
            const icon = document.getElementById('folder-icon-' + folderId);
            if (content.classList.contains('hidden')) {
                content.classList.remove('hidden');
                icon.classList.add('rotate-180');
            } else {
                content.classList.add('hidden');
                icon.classList.remove('rotate-180');
            }
        }

        // Render struktur HTML berdasarkan hasil fetching dari backend
        function renderFolders() {
            const container = document.getElementById('frame-folders-container');
            container.innerHTML = '';
            let isFirstFolder = true;

            DATABASE_FRAMES.forEach(folder => {
                const folderId = folder.id;
                
                let optionsHTML = '';
                
                if (folder.frames && folder.frames.length > 0) {
                    optionsHTML = folder.frames.map((frame, index) => {
                        const isChecked = (isFirstFolder && index === 0) ? 'checked' : '';
                        
                        // Fallback gambar SVG transparan jika tidak ada image dari spatie media library
                        const imgSrc = frame.imageUrl || 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIyMDAiIGhlaWdodD0iMjAwIj48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjZjNmNGY2Ii8+PHRleHQgeD0iNTAlIiB5PSI1MCUiIGZvbnQtZmFtaWx5PSJBcmlhbCIgZm9udC1zaXplPSIxMiIgZmlsbD0iI2EwYWJjMCIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZHk9Ii4zZW0iPk5vIEltYWdlPC90ZXh0Pjwvc3ZnPg==';

                        return `
                            <label class="cursor-pointer frame-option relative group block">
                                <input type="radio" name="selected_frame" value="${frame.id}" class="hidden" ${isChecked}>
                                <div class="border border-gray-200 rounded-xl p-2 transition-all hover:border-[#355faa] hover:shadow-md bg-white h-full flex flex-col">
                                    <div class="flex-1 bg-gray-50 rounded-lg overflow-hidden relative flex items-center justify-center mb-2 min-h-[80px]">
                                        <img src="${imgSrc}" class="max-w-full max-h-[100px] object-contain p-1" onerror="this.src='data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIyMDAiIGhlaWdodD0iMjAwIj48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjZjNmNGY2Ii8+PHRleHQgeD0iNTAlIiB5PSI1MCUiIGZvbnQtZmFtaWx5PSJBcmlhbCIgZm9udC1zaXplPSIxMiIgZmlsbD0iI2EwYWJjMCIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZHk9Ii4zZW0iPk5vIEltYWdlPC90ZXh0Pjwvc3ZnPg=='">
                                        <div class="check-icon absolute top-2 right-2 bg-[#355faa] text-white w-5 h-5 rounded-full flex items-center justify-center opacity-0 scale-50 transition-all shadow-md">
                                            <i class="fa-solid fa-check text-[10px]"></i>
                                        </div>
                                    </div>
                                    <p class="text-[10px] font-bold text-gray-800 text-center truncate px-1" title="${frame.name}">${frame.name}</p>
                                    <div class="flex flex-wrap justify-center gap-1 mt-1">
                                        <span class="text-[8px] bg-blue-100 text-blue-700 px-1.5 py-0.5 rounded shadow-sm font-bold">${frame.paperSize || 'N/A'}</span>
                                        <span class="text-[8px] bg-gray-100 text-gray-600 px-1.5 py-0.5 rounded shadow-sm">${frame.orientation || 'N/A'}</span>
                                    </div>
                                </div>
                            </label>
                        `;
                    }).join('');
                } else {
                    optionsHTML = `<div class="col-span-full text-center p-3 text-xs text-gray-400 border border-dashed rounded-xl">Belum ada frame di tipe ini</div>`;
                }

                const folderHTML = `
                    <div class="border border-gray-200 rounded-2xl overflow-hidden bg-white shadow-sm">
                        <button type="button" onclick="toggleFolder('${folderId}')" class="w-full bg-gray-50 hover:bg-gray-100 px-4 py-3 flex items-center justify-between transition-colors">
                            <div class="flex items-center gap-2">
                                <i class="fa-regular fa-folder-open text-[#355faa]"></i>
                                <span class="text-sm font-bold text-gray-700">${folder.name}</span>
                                <span class="bg-gray-200 text-gray-600 text-[10px] px-2 py-0.5 rounded-full font-bold ml-1">${folder.frames ? folder.frames.length : 0} Frame</span>
                            </div>
                            <i id="folder-icon-${folderId}" class="fa-solid fa-chevron-down text-gray-400 text-xs transition-transform ${isFirstFolder ? 'rotate-180' : ''}"></i>
                        </button>
                        <div id="folder-content-${folderId}" class="p-4 bg-white ${isFirstFolder ? '' : 'hidden'}">
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                                ${optionsHTML}
                            </div>
                        </div>
                    </div>
                `;
                container.innerHTML += folderHTML;
                isFirstFolder = false;
            });
        }

        // ==========================================
        // 2. SETUP & CANVAS INITIALIZATION
        // ==========================================
        fabric.Object.NUM_FRACTION_DIGITS = 8; 

        const canvas = new fabric.Canvas('c', {
            preserveObjectStacking: true,
            backgroundColor: null,
            selection: true
        });

        let cropOverlay = null;
        let croppingImage = null;
        let currentCropShape = 'square';
        let savedClipPath = null;
        let activeSlot = null; 
        let photoCounter = 0; 

        // AUTO-FIT CANVAS RESPONSIVE LOGIC
        function autoFitCanvas() {
            const workspace = document.getElementById('workspace-container');
            const wrapper = document.getElementById('canvas-wrapper');
            const container = document.getElementById('scale-container');
            
            if (!workspace || !wrapper || !container) return;

            // Beri jarak margin agar canvas tidak mentok
            const padding = 80;
            const availableW = workspace.clientWidth - padding;
            const availableH = workspace.clientHeight - padding;
            
            const canvasW = canvas.getWidth();
            const canvasH = canvas.getHeight();
            
            if (canvasW === 0 || canvasH === 0) return;

            // Kalkulasi rasio agar canvas muat di layar
            const scaleX = availableW / canvasW;
            const scaleY = availableH / canvasH;
            let scale = Math.min(scaleX, scaleY);
            
            if (scale > 1) scale = 1; // Jangan perbesar jika layar lebih besar dari 300DPI
            
            // Terapkan scale
            wrapper.style.transform = `scale(${scale})`;
            
            // Resize container DOM agar posisi scroll dan center presisi
            const scaledW = canvasW * scale;
            const scaledH = canvasH * scale;
            
            container.style.width = `${scaledW}px`;
            container.style.height = `${scaledH}px`;
            
            // KALKULASI OFFSET AGAR KLIK PRESISI!
            setTimeout(() => {
                canvas.calcOffset();
            }, 50);
        }

        window.addEventListener('resize', () => {
            if (!document.getElementById('app-container').classList.contains('hidden')) {
                autoFitCanvas();
            }
        });

        function updateFileLabel(input, labelId, isMultiple = false) {
            const label = document.getElementById(labelId);
            if (input.files && input.files.length > 0) {
                if(isMultiple) {
                    label.innerText = `${input.files.length} Foto Dipilih`;
                    label.classList.add('text-[#355faa]', 'font-bold');
                } else {
                    label.innerText = input.files[0].name;
                    label.classList.add('text-[#355faa]', 'font-bold');
                }
            }
        }

        function startEditor() {
            const photosInput = document.getElementById('input-photos-file');
            if (!photosInput.files || photosInput.files.length === 0) {
                alert("Mohon upload setidaknya satu foto customer terlebih dahulu.");
                return;
            }

            const selectedRadio = document.querySelector('input[name="selected_frame"]:checked');
            if (!selectedRadio) {
                alert("Silakan pilih salah satu Frame dari Folder.");
                return;
            }
            const selectedFrameId = selectedRadio.value;
            
            // Memanfaatkan data dinamis dari backend (DATABASE_FRAMES)
            for (let folder of DATABASE_FRAMES) {
                const frame = folder.frames ? folder.frames.find(f => f.id === selectedFrameId) : null;
                if (frame) {
                    selectedFrameData = frame;
                    break;
                }
            }

            if (!selectedFrameData) {
                alert("Frame tidak ditemukan.");
                return;
            }

            // INTERNAL RESOLUTION SETTING
            canvas.setWidth(selectedFrameData.width);
            canvas.setHeight(selectedFrameData.height);
            
            // FIX: Set ukuran absolut pada wrapper agar warna kuning transparan presisi mengikuti kanvas
            const wrapper = document.getElementById('canvas-wrapper');
            if (wrapper) {
                wrapper.style.width = selectedFrameData.width + 'px';
                wrapper.style.height = selectedFrameData.height + 'px';
            }

            document.getElementById('current-canvas-size').innerText = `${selectedFrameData.name} - ${selectedFrameData.paperSize} (${selectedFrameData.orientation})`;

            // Generate Slots
            generateSlotsFromFrameData(selectedFrameData);

            // Setup Frame (Background)
            addImageToCanvas(selectedFrameData.imageUrl, 'frame');

            const gridInput = document.getElementById('input-grid-file');
            if (gridInput.files && gridInput.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.getElementById('grid-preview-img');
                    img.src = e.target.result;
                    img.classList.remove('hidden');
                    document.getElementById('no-grid-msg').classList.add('hidden');
                };
                reader.readAsDataURL(gridInput.files[0]);
            }

            const gallery = document.getElementById('photo-gallery');
            gallery.innerHTML = ''; 
            photoCounter = 0; 
            Array.from(photosInput.files).forEach(file => {
                addPhotoToSidebar(file);
            });

            document.getElementById('setup-page').classList.add('hidden');
            document.getElementById('app-container').classList.remove('hidden');
            document.getElementById('app-container').classList.add('flex');
            
            // Delay autoFitCanvas agar ukuran div #workspace-container terbaca sempurna
            setTimeout(() => {
                autoFitCanvas();
            }, 50);
            
            updateUI(); 
        }

        function backToSetup() {
            if(confirm("Kembali ke setup akan mereset kanvas. Lanjutkan?")) {
                window.location.reload();
            }
        }

        function addPhotoToSidebar(file) {
            const gallery = document.getElementById('photo-gallery');
            if(gallery.innerText.includes('Belum ada foto yang diupload')) gallery.innerHTML = '';

            let displayNum = "";
            const match = file.name.match(/(\d+)\.[^.]+$/);
            if (match) {
                displayNum = match[1]; 
            } else {
                photoCounter++; 
                displayNum = photoCounter;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                const div = document.createElement('div');
                div.className = "sidebar-item relative group aspect-square rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden cursor-move hover:border-[#355faa] hover:shadow-md";
                div.draggable = true;
                div.title = file.name;
                
                div.addEventListener('dragstart', (ev) => {
                    ev.dataTransfer.setData("imageSrc", e.target.result);
                });

                div.innerHTML = `
                    <img src="${e.target.result}" class="w-full h-full object-cover cursor-pointer" onclick="handleSidebarPhotoClick('${e.target.result}')">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent flex flex-col justify-end p-2 pointer-events-none">
                         <p class="text-3xl text-white font-bold text-center pb-2 drop-shadow-lg">${displayNum}</p>
                    </div>
                    <button onclick="handleSidebarPhotoClick('${e.target.result}')" class="absolute top-1 right-1 w-6 h-6 bg-white rounded-full text-[#355faa] flex items-center justify-center shadow-md opacity-0 group-hover:opacity-100 transition-opacity z-10 hover:scale-110">
                        <i class="fa-solid fa-plus text-xs"></i>
                    </button>
                `;
                gallery.appendChild(div);
            };
            reader.readAsDataURL(file);
        }

        function handleSidebarPhotoClick(url) {
            // Check jika frame memiliki masking koordinat yang valid 
            const hasMasks = selectedFrameData && selectedFrameData.masks && selectedFrameData.masks.length > 0;

            if (!activeSlot && hasMasks) {
                alert("Silakan pilih kotak masking di kanvas terlebih dahulu untuk memasukkan foto.");
                return;
            }

            if (activeSlot) {
                fillSlotWithImage(url, activeSlot);
                activeSlot.item(0).set({ stroke: '#9ca3af', strokeWidth: 6, fill: '#e5e7eb' });
                activeSlot = null;
                canvas.requestRenderAll();
            } else {
                addImageToCanvas(url, 'image');
            }
        }

        function appendPhotos(input) {
            if (input.files && input.files.length > 0) {
                Array.from(input.files).forEach((file) => {
                    addPhotoToSidebar(file);
                });
            }
            input.value = '';
        }

        // ==========================================
        // 3. CORE EDITOR LOGIC (Masking & Image)
        // ==========================================
        function generateSlotsFromFrameData(frameObj) {
            const existing = canvas.getObjects().filter(o => o.isPlaceholder);
            existing.forEach(o => canvas.remove(o));

            // Generate dari data JSON backend
            if (frameObj && frameObj.masks && Array.isArray(frameObj.masks)) {
                frameObj.masks.forEach(mask => {
                    // Mencegah error jika data di DB tidak lengkap
                    if(mask.x !== undefined && mask.y !== undefined) {
                        createSlot(mask.x, mask.y, mask.w, mask.h);
                    }
                });
            }
            canvas.requestRenderAll();
        }

        function createSlot(x, y, w, h) {
            const rect = new fabric.Rect({
                width: w, height: h,
                fill: '#e5e7eb', 
                stroke: '#9ca3af', 
                strokeWidth: 6, // Tebal border karena kanvas resolusi tinggi
                strokeDashArray: [15, 15],
                originX: 'center', originY: 'center'
            });

            const icon = new fabric.Text('+', {
                fontSize: Math.min(w, h) * 0.2, fill: '#6b7280',
                originX: 'center', originY: 'center',
                fontFamily: 'Arial', fontWeight: 'bold'
            });

            const group = new fabric.Group([rect, icon], {
                left: x + w/2, top: y + h/2,
                originX: 'center', originY: 'center',
                selectable: false, 
                hoverCursor: 'pointer',
                isPlaceholder: true,
                hasControls: false,
                lockMovementX: true, lockMovementY: true,
                name: 'slotGroup'
            });

            canvas.add(group);
            canvas.sendToBack(group); 
        }

        function fillSlotWithImage(url, slotGroup) {
            fabric.Image.fromURL(url, function(img) {
                const slotW = slotGroup.width * slotGroup.scaleX;
                const slotH = slotGroup.height * slotGroup.scaleY;
                const slotX = slotGroup.left;
                const slotY = slotGroup.top;

                const scaleX = slotW / img.width;
                const scaleY = slotH / img.height;
                const scale = Math.max(scaleX, scaleY);

                const clipRect = new fabric.Rect({
                    left: slotX, top: slotY,
                    width: slotW, height: slotH,
                    originX: 'center', originY: 'center',
                    absolutePositioned: true
                });

                img.set({
                    left: slotX, top: slotY,
                    originX: 'center', originY: 'center',
                    scaleX: scale, scaleY: scale,
                    baseScale: scale, 
                    clipPath: clipRect,
                    cornerColor: '#355faa', borderColor: '#355faa',
                    cornerStyle: 'circle', transparentCorners: false, cornerSize: 40,
                    isFrame: false,
                    isGridImage: true, 
                    hasControls: true, 
                    lockRotation: true,
                    lockScalingX: true, lockScalingY: true 
                });

                canvas.add(img);
                
                const topFrame = canvas.getObjects().find(o => o.isFrame);
                if (topFrame) img.moveTo(canvas.getObjects().indexOf(topFrame));
                else img.sendToBack(); 

                slotGroup.visible = false;
                canvas.setActiveObject(img);
                canvas.renderAll();
            });
        }

        function updateGridImageZoom(value) {
            const obj = canvas.getActiveObject();
            if (obj && obj.isGridImage && obj.baseScale) {
                const newScale = obj.baseScale * parseFloat(value);
                obj.scale(newScale);
                canvas.requestRenderAll();
            }
        }

        function addImageToCanvas(url, type, dropEvent = null) {
            if (dropEvent) {
                 const pointer = canvas.getPointer(dropEvent);
                 const objects = canvas.getObjects();
                 const targetSlot = objects.find(obj => obj.isPlaceholder && obj.containsPoint(pointer));

                 if (targetSlot) {
                     fillSlotWithImage(url, targetSlot);
                     return;
                 }
            }

            const hasMasks = selectedFrameData && selectedFrameData.masks && selectedFrameData.masks.length > 0;
            if (type !== 'frame' && hasMasks && !dropEvent) return; 

            // Jika imageUrl dari backend kosong
            if(!url) return; 

            fabric.Image.fromURL(url, function(img) {
                const cvW = canvas.getWidth();
                const cvH = canvas.getHeight();

                if (type === 'frame') {
                    // SETTING KHUSUS FRONTEND: Solid, Locked, Tembus Klik
                    img.set({
                        left: 0, top: 0, originX: 'left', originY: 'top',
                        scaleX: cvW / img.width, 
                        scaleY: cvH / img.height,
                        selectable: false,
                        evented: false,
                        lockMovementX: true, lockMovementY: true,
                        lockScalingX: true, lockScalingY: true, lockRotation: true,
                        hasControls: false, hasBorders: false,
                        opacity: 1, 
                        isFrame: true
                    });
                    canvas.add(img);
                    img.bringToFront(); 
                } else {
                    const scale = (cvW * 0.4) / img.width;
                    let left = cvW / 2;
                    let top = cvH / 2;

                    if (dropEvent) {
                        const pointer = canvas.getPointer(dropEvent);
                        left = pointer.x;
                        top = pointer.y;
                    }

                    img.set({
                        left: left, top: top, originX: 'center', originY: 'center',
                        scaleX: scale, scaleY: scale,
                        cornerColor: '#355faa', borderColor: '#355faa',
                        cornerStyle: 'circle', transparentCorners: false, cornerSize: 40,
                        isFrame: false,
                        isGridImage: false 
                    });
                    canvas.add(img);
                    
                    let topFrame = canvas.getObjects().find(o => o.isFrame);
                    if (topFrame) img.moveTo(canvas.getObjects().indexOf(topFrame));
                    else img.sendToBack();
                    
                    canvas.setActiveObject(img);
                }
                canvas.renderAll();
            });
        }

        // --- Drag and Drop Logic ---
        const workspace = document.getElementById('workspace-container');
        workspace.addEventListener('dragover', (e) => { e.preventDefault(); workspace.classList.add('drag-active'); });
        workspace.addEventListener('dragleave', () => { workspace.classList.remove('drag-active'); });
        workspace.addEventListener('drop', (e) => {
            e.preventDefault();
            workspace.classList.remove('drag-active');
            
            const imageSrc = e.dataTransfer.getData("imageSrc");
            if (imageSrc) {
                const hasMasks = selectedFrameData && selectedFrameData.masks && selectedFrameData.masks.length > 0;
                if (hasMasks) {
                    const pointer = canvas.getPointer(e);
                    const targetSlot = canvas.getObjects().find(obj => obj.isPlaceholder && obj.containsPoint(pointer));
                    if (!targetSlot) {
                        alert("Silahkan geser foto langsung ke dalam kotak masking yang tersedia.");
                        return;
                    }
                }
                addImageToCanvas(imageSrc, 'image', e);
                return;
            }
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                Array.from(files).forEach(file => addPhotoToSidebar(file));
            }
        });

        // --- Export & UI Logic ---
        function openExportModal() {
            const overlay = document.getElementById('export-modal-overlay');
            const input = document.getElementById('export-customer-name');
            const error = document.getElementById('export-error');
            
            input.value = "";
            error.classList.add('hidden');
            input.classList.remove('border-red-500');

            overlay.classList.remove('hidden');
            overlay.classList.add('flex');
            setTimeout(() => input.focus(), 100);
        }

        function closeExportModal() {
            const overlay = document.getElementById('export-modal-overlay');
            overlay.classList.add('hidden');
            overlay.classList.remove('flex');
        }

        function processFinalExport() {
            const input = document.getElementById('export-customer-name');
            const error = document.getElementById('export-error');
            const customerName = input.value.trim();

            if (!customerName) {
                error.classList.remove('hidden');
                input.classList.add('border-red-500');
                input.focus();
                return;
            }

            closeExportModal();
            downloadImage(customerName);
        }

        function downloadImage(customerName = "Default") {
            if(cropOverlay) cancelCrop();
            
            const placeholders = canvas.getObjects().filter(o => o.isPlaceholder);
            placeholders.forEach(p => p.visible = false);

            canvas.discardActiveObject();
            canvas.renderAll();
            
            const link = document.createElement('a');
            link.download = `Snap Fun - Grid - ${customerName}.png`; 
            // Multiplier 1 (300DPI native kanvas)
            link.href = canvas.toDataURL({ format: 'png', multiplier: 1, quality: 1 });
            
            placeholders.forEach(p => p.visible = true);
            canvas.renderAll();

            link.click();
        }

        function printCanvas() {
            if(cropOverlay) cancelCrop();
            const placeholders = canvas.getObjects().filter(o => o.isPlaceholder);
            placeholders.forEach(p => p.visible = false);

            canvas.discardActiveObject();
            canvas.renderAll();

            let container = document.getElementById('print-container') || document.createElement('div');
            container.id = 'print-container';
            container.innerHTML = '';
            container.style.display = 'none';
            const img = new Image();
            img.id = 'print-image';
            container.appendChild(img);
            document.body.appendChild(container);
            
            img.src = canvas.toDataURL({ format: 'png', multiplier: 1, quality: 1 });
            
            placeholders.forEach(p => p.visible = true);
            canvas.renderAll();

            img.onload = () => window.print();
        }

        // EVENT LISTENERS UNTUK UI
        canvas.on('selection:created', updateUI);
        canvas.on('selection:updated', updateUI);
        canvas.on('selection:cleared', updateUI);

        canvas.on('mouse:down', (e) => {
            if (e.target && e.target.isPlaceholder) {
                if (activeSlot && activeSlot !== e.target) {
                    activeSlot.item(0).set({ stroke: '#9ca3af', strokeWidth: 6, fill: '#e5e7eb' });
                }
                activeSlot = e.target;
                activeSlot.item(0).set({ 
                    stroke: '#355faa', 
                    strokeWidth: 10, 
                    fill: 'rgba(53, 95, 170, 0.2)' 
                });
                canvas.requestRenderAll();
            } else {
                if (activeSlot) {
                    activeSlot.item(0).set({ stroke: '#9ca3af', strokeWidth: 6, fill: '#e5e7eb' });
                    activeSlot = null;
                    canvas.requestRenderAll();
                }
            }
        });

        canvas.on('mouse:dblclick', function(e) {
            if (e.target && e.target.type === 'image' && !e.target.isFrame && !e.target.isGridImage) {
                if (e.target.clipPath) editCrop(e.target);
                else initCropMode('square'); 
            }
        });

        canvas.on('mouse:wheel', function(opt) {
            const activeObj = canvas.getActiveObject();
            if (activeObj && activeObj.isGridImage && activeObj.baseScale) {
                const delta = opt.e.deltaY;
                let zoom = activeObj.scaleX / activeObj.baseScale;
                if (delta < 0) zoom += 0.1;
                else zoom -= 0.1;

                if (zoom > 3) zoom = 3;
                if (zoom < 0.1) zoom = 0.1;

                const slider = document.getElementById('floating-zoom-slider');
                const sidebarSlider = document.getElementById('grid-zoom-slider');
                if(slider) slider.value = zoom;
                if(sidebarSlider) sidebarSlider.value = zoom;

                updateGridImageZoom(zoom);
                opt.e.preventDefault();
                opt.e.stopPropagation();
            }
        });

        function updateUI() {
            const activeObj = canvas.getActiveObject();
            const noSel = document.getElementById('no-selection-msg');
            const controls = document.getElementById('object-controls');
            const lockBtn = document.getElementById('lockBtn');
            const objLabel = document.getElementById('obj-type-label');
            const objIcon = document.getElementById('obj-icon');
            const cropSection = document.getElementById('crop-tools-section');
            const zoomPanel = document.getElementById('active-zoom-ui'); 
            const zoomSlider = document.getElementById('floating-zoom-slider'); 

            if (!activeObj) {
                noSel.classList.remove('hidden');
                controls.classList.add('hidden');
                return;
            }

            noSel.classList.add('hidden');
            controls.classList.remove('hidden');
            lockBtn.parentElement.classList.remove('hidden'); 

            if (activeObj.isPlaceholder) {
                canvas.discardActiveObject(); 
                updateUI(); 
                return; 
            }

            if (activeObj.isGridImage) {
                zoomPanel.classList.remove('hidden');
                if (activeObj.baseScale) {
                    zoomSlider.value = (activeObj.scaleX / activeObj.baseScale).toFixed(2);
                } else {
                    zoomSlider.value = 1; 
                }
                
                cropSection.classList.add('hidden');
                objLabel.innerText = "Grid Photo";
                objIcon.className = "fa-solid fa-table-cells";
                lockBtn.classList.add('hidden'); 
            } else {
                zoomPanel.classList.add('hidden');
                cropSection.classList.remove('hidden');
                lockBtn.classList.remove('hidden');
            }

            if (activeObj.isFrame) {
                objLabel.innerText = "Frame Overlay";
                objIcon.className = "fa-solid fa-border-all";
                lockBtn.innerHTML = '<i class="fa-solid fa-ban"></i> Terkunci di Atas';
                lockBtn.disabled = true;
                lockBtn.classList.add('opacity-50', 'cursor-not-allowed');
                cropSection.classList.add('opacity-30', 'pointer-events-none');
                zoomPanel.classList.add('hidden');
                lockBtn.classList.remove('hidden');
            } else if (!activeObj.isGridImage) {
                objLabel.innerText = "Free Photo";
                objIcon.className = "fa-solid fa-image";
                lockBtn.disabled = false;
                lockBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                cropSection.classList.remove('opacity-30', 'pointer-events-none');

                if (activeObj.lockMovementX) {
                    lockBtn.innerHTML = '<i class="fa-solid fa-lock"></i> Unlock Position (Q)';
                    lockBtn.style.color = "#355faa";
                    lockBtn.style.borderColor = "rgba(53, 95, 170, 0.2)";
                    lockBtn.style.backgroundColor = "rgba(53, 95, 170, 0.05)";
                } else {
                    lockBtn.innerHTML = '<i class="fa-solid fa-lock-open"></i> Lock Position (Q)';
                    lockBtn.style.color = ""; lockBtn.style.borderColor = ""; lockBtn.style.backgroundColor = "";
                }
            }
        }

        function enforceFrameLayering() {
            const objects = canvas.getObjects();
            const frames = objects.filter(o => o.isFrame);
            frames.forEach(f => f.bringToFront());
            const placeholders = objects.filter(o => o.isPlaceholder);
            placeholders.forEach(p => p.sendToBack());
            canvas.renderAll();
        }

        function bringForward() { const obj = canvas.getActiveObject(); if(obj && !obj.isFrame) { canvas.bringForward(obj); enforceFrameLayering(); }}
        function sendBackward() { const obj = canvas.getActiveObject(); if(obj && !obj.isFrame) { canvas.sendBackwards(obj); }}
        function bringToFront() { if(canvas.getActiveObject() && !canvas.getActiveObject().isFrame){ canvas.bringToFront(canvas.getActiveObject()); enforceFrameLayering(); }}
        function sendToBack() { if(canvas.getActiveObject() && !canvas.getActiveObject().isFrame){ canvas.sendToBack(canvas.getActiveObject()); }}

        function deleteActiveObject() {
            const activeObj = canvas.getActiveObject();
            if (!activeObj || activeObj.isFrame || activeObj.isPlaceholder) return;
            if (activeObj.isGridImage && activeObj.clipPath) {
                const clip = activeObj.clipPath;
                const placeholders = canvas.getObjects().filter(o => o.isPlaceholder);
                const associatedSlot = placeholders.find(p => 
                    Math.abs(p.left - clip.left) < 1 && Math.abs(p.top - clip.top) < 1
                );
                if (associatedSlot) associatedSlot.set('visible', true);
            }
            canvas.remove(activeObj); 
        }

        function toggleLock() {
            const activeObj = canvas.getActiveObject();
            if (!activeObj || activeObj.isFrame) return;
            const isLocked = !activeObj.lockMovementX;
            activeObj.set({
                lockMovementX: isLocked, lockMovementY: isLocked,
                lockRotation: isLocked, lockScalingX: isLocked, lockScalingY: isLocked,
                hasControls: !isLocked, selectable: true
            });
            canvas.requestRenderAll();
            updateUI();
        }

        // Shortcuts
        document.addEventListener('keydown', (e) => {
            const isCtrl = e.ctrlKey || e.metaKey;
            const key = e.key.toLowerCase();
            const activeObj = canvas.getActiveObject();

            if (!document.getElementById('export-modal-overlay').classList.contains('hidden')) {
                if (key === 'escape') closeExportModal();
                if (key === 'enter') processFinalExport();
                return;
            }

            if (['arrowup', 'arrowdown', 'arrowleft', 'arrowright'].includes(key)) {
                if (activeObj && !activeObj.isFrame && !activeObj.lockMovementX) {
                    e.preventDefault(); 
                    const step = e.shiftKey ? 30 : 5; 
                    if (key === 'arrowup') activeObj.top -= step;
                    else if (key === 'arrowdown') activeObj.top += step;
                    else if (key === 'arrowleft') activeObj.left -= step;
                    else if (key === 'arrowright') activeObj.left += step;
                    activeObj.setCoords();
                    canvas.requestRenderAll();
                }
                return;
            }

            if (key === 'd' && !isCtrl) { if (activeObj) deleteActiveObject(); }
            if (key === 'p' && !isCtrl) { e.preventDefault(); printCanvas(); }
            if (key === 'e' && !isCtrl) { e.preventDefault(); openExportModal(); }
            if (key === 'r' && !isCtrl) { if(confirm("Reload page? Unsaved changes will be lost.")) window.location.reload(); }
            if (key === 'q' && !isCtrl) { e.preventDefault(); toggleLock(); }
            if (e.key === 'Delete' || e.key === 'Backspace') { if (activeObj) deleteActiveObject(); }
            if (isCtrl && e.key === ']') { e.preventDefault(); bringForward(); }
            if (isCtrl && e.key === '[') { e.preventDefault(); sendBackward(); }
        });
        
        // --- CROP MODE LOGIC ---
        function initCropMode(shape) {
            const activeObj = canvas.getActiveObject();
            if (!activeObj || activeObj.isFrame || activeObj.type !== 'image') return;
            croppingImage = activeObj;
            currentCropShape = shape;
            if (cropOverlay) canvas.remove(cropOverlay);
            const bound = activeObj.getBoundingRect();
            const dim = Math.min(bound.width, bound.height) * 0.8; 
            createCropOverlay(shape, bound.left + bound.width/2, bound.top + bound.height/2, dim, dim);
        }
        function editCrop(activeObj) {
            croppingImage = activeObj;
            savedClipPath = activeObj.clipPath; 
            const clip = activeObj.clipPath;
            currentCropShape = (clip.type === 'rect') ? 'square' : (clip.type === 'circle' ? 'circle' : 'heart');
            const matrix = activeObj.calcTransformMatrix();
            const canvasCenter = fabric.util.transformPoint(new fabric.Point(clip.left, clip.top), matrix);
            activeObj.set({ clipPath: null, dirty: true });
            createCropOverlay(currentCropShape, canvasCenter.x, canvasCenter.y, clip.width * clip.scaleX * activeObj.scaleX, clip.height * clip.scaleY * activeObj.scaleY);
        }
        function createCropOverlay(shape, left, top, width, height) {
            const props = {
                left, top, originX: 'center', originY: 'center',
                fill: 'rgba(0,0,0,0.3)', stroke: '#fff', strokeWidth: 6, strokeDashArray: [15, 15],
                transparentCorners: false, cornerColor: 'white', cornerStrokeColor: '#000', cornerSize: 40,
                borderColor: 'white', lockRotation: true
            };
            if (shape === 'square') cropOverlay = new fabric.Rect({ width, height, ...props });
            else if (shape === 'circle') cropOverlay = new fabric.Circle({ radius: width / 2, scaleY: height/width, ...props });
            else {
                cropOverlay = new fabric.Path('M 10,30 A 20,20 0,0,1 50,30 A 20,20 0,0,1 90,30 Q 90,60 50,90 Q 10,60 10,30 z', { ...props, scaleX: width/100, scaleY: height/100 });
            }
            canvas.add(cropOverlay);
            canvas.setActiveObject(cropOverlay);
            document.getElementById('active-crop-ui').classList.remove('hidden');
            canvas.requestRenderAll();
        }
        function performCrop() {
            if (!cropOverlay || !croppingImage) return;
            const invertedMatrix = fabric.util.invertTransform(croppingImage.calcTransformMatrix());
            const localPoint = fabric.util.transformPoint(new fabric.Point(cropOverlay.left, cropOverlay.top), invertedMatrix);
            let clipPath;
            if (currentCropShape === 'square') {
                clipPath = new fabric.Rect({ width: cropOverlay.getScaledWidth() / croppingImage.scaleX, height: cropOverlay.getScaledHeight() / croppingImage.scaleY, left: localPoint.x, top: localPoint.y, originX: 'center', originY: 'center' });
            } else if (currentCropShape === 'circle') {
                clipPath = new fabric.Circle({ radius: (cropOverlay.radius * cropOverlay.scaleX) / croppingImage.scaleX, left: localPoint.x, top: localPoint.y, originX: 'center', originY: 'center' });
            } else {
                clipPath = new fabric.Path('M 10,30 A 20,20 0,0,1 50,30 A 20,20 0,0,1 90,30 Q 90,60 50,90 Q 10,60 10,30 z', { scaleX: cropOverlay.scaleX / croppingImage.scaleX, scaleY: cropOverlay.scaleY / croppingImage.scaleY, left: localPoint.x, top: localPoint.y, originX: 'center', originY: 'center' });
            }
            croppingImage.set({ clipPath, dirty: true });
            cancelCrop();
            canvas.setActiveObject(croppingImage);
        }
        function cancelCrop() {
            if (cropOverlay) { canvas.remove(cropOverlay); cropOverlay = null; }
            if (croppingImage && savedClipPath) croppingImage.set({ clipPath: savedClipPath, dirty: true });
            croppingImage = null; savedClipPath = null;
            document.getElementById('active-crop-ui').classList.add('hidden');
            canvas.requestRenderAll();
        }
        function removeClip() {
            const activeObj = canvas.getActiveObject();
            if (activeObj && !activeObj.isFrame) { activeObj.set({ clipPath: null, dirty: true }); canvas.renderAll(); }
        }

    </script>
</body>
</html>