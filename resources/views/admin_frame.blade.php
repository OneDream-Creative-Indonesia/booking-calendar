<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin - Frame Manager</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Fabric.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fabric.js/5.3.1/fabric.min.js"></script>
    
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f3f4f6; }
        ::-webkit-scrollbar { width: 6px; height: 6px;}
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #9ca3af; }
        
        .checkerboard-bg {
            background-color: #f9fafb;
            background-image: 
                linear-gradient(45deg, #e5e7eb 25%, transparent 25%), 
                linear-gradient(-45deg, #e5e7eb 25%, transparent 25%), 
                linear-gradient(45deg, transparent 75%, #e5e7eb 75%), 
                linear-gradient(-45deg, transparent 75%, #e5e7eb 75%);
            background-size: 20px 20px;
            background-position: 0 0, 0 10px, 10px -10px, -10px 0px;
        }

        #canvas-wrapper {
            transition: transform 0.1s ease;
            background-color: rgba(250, 204, 21, 0.3); /* Kuning Transparan */
            transform-origin: top left;
        }
        
        #scale-container {
            transition: width 0.1s ease, height 0.1s ease;
        }

        .view-hidden { display: none !important; }
    </style>
</head>
<body class="h-screen flex flex-col overflow-hidden text-gray-800">

    <!-- Top Navbar -->
    <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-6 shrink-0 z-20 shadow-sm">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg flex items-center justify-center bg-gray-900 text-white shadow-md">
                <i class="fa-solid fa-screwdriver-wrench text-sm"></i>
            </div>
            <h1 class="font-bold text-lg tracking-tight">Admin Frame Manager</h1>
            <span class="bg-green-100 text-green-700 text-xs font-bold px-2 py-0.5 rounded ml-2"><i class="fa-solid fa-link"></i> Live Database</span>
        </div>
        <div class="flex items-center gap-3" id="header-actions">
            <a href="/admin" class="text-gray-500 hover:text-gray-900 text-sm font-semibold transition-colors flex items-center gap-2">
                <i class="fa-solid fa-arrow-right-from-bracket"></i> Kembali ke Dashboard
            </a>
        </div>
    </header>

    <!-- ========================================== -->
    <!-- VIEW 1: FOLDER MANAGER -->
    <!-- ========================================== -->
    <div id="view-folders" class="flex-1 overflow-auto p-8 bg-gray-50 flex flex-col">
        <div class="max-w-6xl w-full mx-auto space-y-6">
            
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-bold text-gray-700">Kategori Tipe Frame</h2>
                <button onclick="createNewFolder()" class="bg-[#355faa] hover:bg-[#2d5191] text-white px-5 py-2.5 rounded-xl text-sm font-bold transition-all shadow-md flex items-center gap-2">
                    <i class="fa-solid fa-folder-plus"></i> Buat Folder Baru
                </button>
            </div>

            <div id="folders-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Loader awal -->
                <div class="col-span-full text-center py-10 text-gray-400 text-sm">
                    <i class="fa-solid fa-circle-notch fa-spin text-2xl mb-2"></i><br>Memuat database...
                </div>
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- VIEW 2: FRAME EDITOR -->
    <!-- ========================================== -->
    <div id="view-editor" class="flex-1 flex overflow-hidden view-hidden">
        
        <!-- LEFT PANEL: Settings, Layers & Masks -->
        <aside class="w-[420px] bg-white border-r border-gray-200 flex flex-col z-20 shrink-0 shadow-lg">
            
            <div class="p-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                <button onclick="backToFolders()" class="text-gray-500 hover:text-gray-900 transition-colors text-sm font-semibold flex items-center gap-2">
                    <i class="fa-solid fa-arrow-left"></i> Kembali
                </button>
                <button onclick="saveFrameToFolder()" id="btn-save-frame" class="bg-[#355faa] hover:bg-[#2d5191] text-white px-4 py-1.5 rounded-lg text-sm font-bold shadow-md transition-colors flex items-center gap-2">
                    <i class="fa-solid fa-save"></i> Simpan Frame
                </button>
            </div>

            <div class="flex-1 overflow-y-auto p-6 space-y-6 custom-scrollbar">
                
                <!-- 1. Frame Metadata -->
                <div class="space-y-4">
                    <div class="flex items-center justify-between border-b pb-2">
                        <h2 class="text-xs font-bold text-gray-400 uppercase tracking-widest">1. Detail Frame</h2>
                        <span id="editor-folder-badge" class="text-[10px] bg-indigo-100 text-indigo-700 font-bold px-2 py-0.5 rounded">Folder Aktif</span>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-3">
                        <div class="col-span-2">
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Nama Frame</label>
                            <input type="text" id="frame-name" placeholder="Nama Template" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs focus:ring-2 focus:ring-[#355faa]/20 outline-none">
                        </div>
                        <!-- ID Frame Hidden -->
                        <input type="hidden" id="frame-id" value="">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Ukuran Kertas</label>
                            <select id="frame-paper" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs focus:ring-2 focus:ring-[#355faa]/20 outline-none" onchange="updateCanvasDimensions()">
                                <option value="2x6">2x6 inch (Photostrip)</option>
                                <option value="4R">4R (4x6 inch)</option>
                                <option value="A4">A4</option>
                                <option value="A3">A3</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Orientasi</label>
                            <select id="frame-orientation" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs focus:ring-2 focus:ring-[#355faa]/20 outline-none" onchange="updateCanvasDimensions()">
                                <option value="Portrait">Portrait</option>
                                <option value="Landscape">Landscape</option>
                            </select>
                        </div>
                    </div>

                    <!-- Input Width dan Height hanya digunakan untuk preview visual, tidak dikirim ke DB lagi -->
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Width Preview (px)</label>
                            <input type="number" id="frame-w" value="1200" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs outline-none bg-gray-50 text-gray-400" readonly>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Height Preview (px)</label>
                            <input type="number" id="frame-h" value="1800" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs outline-none bg-gray-50 text-gray-400" readonly>
                        </div>
                    </div>
                </div>

                <!-- 2. Background Image -->
                <div class="space-y-4 pt-4 border-t border-gray-100">
                    <h2 class="text-xs font-bold text-gray-400 uppercase tracking-widest border-b pb-2">2. Desain Overlay (Transparan)</h2>
                    <label class="flex items-center gap-3 p-3 border border-dashed border-gray-300 rounded-xl cursor-pointer hover:bg-gray-50 transition-colors group">
                        <div class="w-10 h-10 rounded-lg bg-pink-50 text-pink-500 flex items-center justify-center group-hover:scale-110 transition-transform">
                            <i class="fa-solid fa-upload"></i>
                        </div>
                        <div class="flex-1 overflow-hidden">
                            <span id="bg-label" class="text-xs font-medium text-gray-700 truncate block">Upload Frame PNG/SVG</span>
                        </div>
                        <input type="file" id="bg-upload" accept="image/png, image/svg+xml" class="hidden" onchange="loadBackgroundImage(this)">
                    </label>
                </div>

                <!-- 3. Layer Manager -->
                <div class="space-y-3 pt-4 border-t border-gray-100">
                    <h2 class="text-xs font-bold text-gray-400 uppercase tracking-widest border-b pb-2">3. Lapisan (Layers)</h2>
                    <div id="layers-container" class="space-y-2 bg-gray-50 p-2 rounded-xl border border-gray-200 min-h-[80px]">
                        <!-- Layers -->
                    </div>
                </div>

                <!-- 4. Masking Manager -->
                <div class="space-y-4 pt-4 border-t border-gray-100">
                    <div class="flex items-center justify-between border-b pb-2">
                        <h2 class="text-xs font-bold text-gray-400 uppercase tracking-widest">4. Koordinat Masking</h2>
                        <button onclick="addMask()" class="text-[10px] bg-indigo-50 text-indigo-600 px-2 py-1.5 rounded font-bold hover:bg-indigo-100 transition-colors shadow-sm">
                            <i class="fa-solid fa-plus"></i> Tambah Kotak
                        </button>
                    </div>

                    <p class="text-[10px] text-gray-500 leading-tight">Ubah ukuran kotak biru di kanvas. Klik ikon <i class="fa-regular fa-copy"></i> untuk menyalin masking.</p>
                    
                    <div id="masks-container" class="space-y-3">
                        <!-- Mask items -->
                    </div>
                </div>

            </div>
        </aside>

        <!-- RIGHT PANEL: Visualizer -->
        <main class="flex-1 flex flex-col relative bg-gray-100">
            
            <div class="h-12 bg-white border-b border-gray-200 flex items-center justify-between px-4 shrink-0 shadow-sm z-10">
                <span class="text-xs font-bold text-gray-500"><i class="fa-solid fa-eye"></i> Visualizer Transparan</span>
                <div class="flex items-center gap-3">
                    <button onclick="autoFitCanvas()" class="text-[10px] bg-gray-200 hover:bg-gray-300 text-gray-700 px-2 py-1 rounded transition-colors mr-2">Fit Screen</button>
                    <span class="text-[10px] font-semibold text-gray-400">Zoom:</span>
                    <input type="range" id="zoom-slider" min="0.05" max="1" step="0.01" value="0.2" class="w-24 h-1 bg-gray-200 rounded-lg appearance-none cursor-pointer" oninput="setZoom(this.value)">
                    <span id="zoom-val" class="text-[10px] font-bold text-gray-600 w-8">20%</span>
                </div>
            </div>

            <div class="flex-1 overflow-auto checkerboard-bg p-8 flex items-center justify-center" id="scroll-area">
                <div id="scale-container">
                    <div id="canvas-wrapper" class="shadow-2xl border border-gray-300 relative origin-top-left">
                        <canvas id="c"></canvas>
                    </div>
                </div>
            </div>

        </main>
    </div>

    <!-- CUSTOM MODALS -->
    <div id="custom-modal-overlay" class="fixed inset-0 bg-black/60 z-[200] flex items-center justify-center p-4 view-hidden backdrop-blur-sm">
        
        <div id="prompt-modal" class="bg-white rounded-2xl w-full max-w-sm p-6 shadow-2xl view-hidden transform transition-all">
            <h3 class="text-lg font-bold text-gray-900 mb-2" id="prompt-title">Buat Folder Baru</h3>
            <p class="text-sm text-gray-500 mb-4" id="prompt-msg">Masukkan nama Folder/Kategori baru:</p>
            <input type="text" id="prompt-input" placeholder="Misal: 4 Grid Classic" class="w-full px-4 py-3 border border-gray-300 rounded-xl mb-4 outline-none focus:border-[#355faa] focus:ring-2 focus:ring-[#355faa]/20">
            <div class="flex gap-3 justify-end">
                <button onclick="closeCustomModal()" class="px-4 py-2 rounded-lg text-sm font-semibold text-gray-600 hover:bg-gray-100 transition-colors">Batal</button>
                <button onclick="submitPrompt()" class="px-4 py-2 rounded-lg text-sm font-semibold text-white bg-[#355faa] hover:bg-[#2d5191] transition-colors">Simpan</button>
            </div>
        </div>

        <div id="confirm-modal" class="bg-white rounded-2xl w-full max-w-sm p-6 shadow-2xl view-hidden transform transition-all">
            <h3 class="text-lg font-bold text-gray-900 mb-2">Konfirmasi</h3>
            <p id="confirm-msg" class="text-sm text-gray-500 mb-6">Apakah Anda yakin ingin menghapusnya?</p>
            <div class="flex gap-3 justify-end">
                <button onclick="closeCustomModal()" class="px-4 py-2 rounded-lg text-sm font-semibold text-gray-600 hover:bg-gray-100 transition-colors">Batal</button>
                <button onclick="submitConfirm()" class="px-4 py-2 rounded-lg text-sm font-semibold text-white bg-red-500 hover:bg-red-600 transition-colors">Hapus</button>
            </div>
        </div>

        <div id="alert-modal" class="bg-white rounded-2xl w-full max-w-sm p-6 shadow-2xl view-hidden transform transition-all text-center">
            <div class="w-12 h-12 rounded-full bg-blue-50 text-[#355faa] flex items-center justify-center mx-auto mb-4" id="alert-icon-container">
                <i class="fa-solid fa-circle-check text-2xl" id="alert-icon"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-2" id="alert-title">Sukses!</h3>
            <p id="alert-msg" class="text-sm text-gray-500 mb-6 whitespace-pre-wrap overflow-hidden">Pesan informasi</p>
            <button onclick="closeCustomModal()" class="w-full px-4 py-2 rounded-lg text-sm font-semibold text-white bg-[#355faa] hover:bg-[#2d5191] transition-colors">Tutup</button>
        </div>
    </div>

    <script>
        // ==========================================
        // 0. GET CSRF TOKEN
        // ==========================================
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // ==========================================
        // 1. CUSTOM MODALS LOGIC
        // ==========================================
        let promptCallback = null;
        let confirmCallback = null;

        function showPromptModal(title, message, callback) {
            document.getElementById('custom-modal-overlay').classList.remove('view-hidden');
            document.getElementById('prompt-modal').classList.remove('view-hidden');
            document.getElementById('confirm-modal').classList.add('view-hidden');
            document.getElementById('alert-modal').classList.add('view-hidden');
            
            document.getElementById('prompt-title').innerText = title;
            document.getElementById('prompt-msg').innerText = message;
            const input = document.getElementById('prompt-input');
            input.value = '';
            
            promptCallback = callback;
            setTimeout(() => input.focus(), 100);
        }

        function submitPrompt() {
            const val = document.getElementById('prompt-input').value;
            if (promptCallback) promptCallback(val);
            closeCustomModal();
        }

        document.getElementById('prompt-input').addEventListener('keypress', function (e) {
            if (e.key === 'Enter') submitPrompt();
        });

        function showConfirmModal(message, callback) {
            document.getElementById('custom-modal-overlay').classList.remove('view-hidden');
            document.getElementById('confirm-modal').classList.remove('view-hidden');
            document.getElementById('prompt-modal').classList.add('view-hidden');
            document.getElementById('alert-modal').classList.add('view-hidden');
            
            document.getElementById('confirm-msg').innerText = message;
            confirmCallback = callback;
        }

        function submitConfirm() {
            if (confirmCallback) confirmCallback();
            closeCustomModal();
        }

        function showAlertModal(message, isError = false) {
            document.getElementById('custom-modal-overlay').classList.remove('view-hidden');
            document.getElementById('alert-modal').classList.remove('view-hidden');
            document.getElementById('prompt-modal').classList.add('view-hidden');
            document.getElementById('confirm-modal').classList.add('view-hidden');
            
            document.getElementById('alert-msg').innerText = message;
            
            const title = document.getElementById('alert-title');
            const icon = document.getElementById('alert-icon');
            const iconContainer = document.getElementById('alert-icon-container');

            if (isError) {
                title.innerText = "Terjadi Kesalahan";
                icon.className = "fa-solid fa-triangle-exclamation text-2xl";
                iconContainer.className = "w-12 h-12 rounded-full bg-red-50 text-red-500 flex items-center justify-center mx-auto mb-4";
            } else {
                title.innerText = "Sukses!";
                icon.className = "fa-solid fa-circle-check text-2xl";
                iconContainer.className = "w-12 h-12 rounded-full bg-blue-50 text-[#355faa] flex items-center justify-center mx-auto mb-4";
            }
        }

        function closeCustomModal() {
            document.getElementById('custom-modal-overlay').classList.add('view-hidden');
            document.getElementById('prompt-modal').classList.add('view-hidden');
            document.getElementById('confirm-modal').classList.add('view-hidden');
            document.getElementById('alert-modal').classList.add('view-hidden');
            promptCallback = null;
            confirmCallback = null;
        }

        // ==========================================
        // 2. DATABASE INTEGRATION & FOLDER MANAGEMENT
        // ==========================================
        
        let database = [];
        let activeFolderId = null;

        // Fetch dari Database Laravel API
        async function fetchDatabase() {
            try {
                // Catatan: Pastikan route /api/admin/get-frames ini terdaftar di routes web/api
                const response = await fetch('/api/admin/get-frames', {
                    headers: { 'Accept': 'application/json' }
                });
                
                if(!response.ok) {
                    const errorText = await response.text();
                    throw new Error(`HTTP ${response.status}: ${errorText.substring(0, 100)}...`);
                }
                
                const result = await response.json();
                if(result.success) {
                    database = result.data;
                    renderFolders();
                } else {
                    throw new Error(result.message || "Gagal mengambil data JSON yang valid");
                }
            } catch(e) {
                console.error("Fetch DB Error:", e);
                document.getElementById('folders-grid').innerHTML = `
                    <div class="col-span-full text-center py-10 text-red-500 text-sm border border-red-200 bg-red-50 rounded-xl">
                        <b>Gagal memuat database.</b><br>Detail Error: <code>${e.message}</code><br><br>
                        Pastikan server Laravel berjalan, tidak ada error di AdminFrameController, dan route API /api/admin/get-frames terdaftar.<br><br>
                        <button onclick="fetchDatabase()" class="underline font-bold text-blue-600">Coba Lagi</button>
                    </div>`;
            }
        }

        function renderFolders() {
            const grid = document.getElementById('folders-grid');
            grid.innerHTML = '';

            if (database.length === 0) {
                grid.innerHTML = `<div class="col-span-full text-center py-10 text-gray-400 text-sm">Belum ada folder. Klik "Buat Folder Baru".</div>`;
                return;
            }

            database.forEach(folder => {
                const frameCount = folder.frames ? folder.frames.length : 0;
                
                let framesPreviewHTML = '';
                if (frameCount === 0) {
                    framesPreviewHTML = `<div class="h-24 bg-gray-100 rounded-lg flex items-center justify-center text-gray-400 text-xs border border-dashed border-gray-200">Kosong</div>`;
                } else {
                    framesPreviewHTML = `<div class="h-24 bg-gray-50 rounded-lg border border-gray-200 p-2 overflow-hidden relative">
                        <div class="flex gap-2 overflow-x-auto h-full custom-scrollbar items-center">`;
                    folder.frames.forEach(f => {
                        const bgSrc = f.imageUrl ? f.imageUrl : 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIxMCIgaGVpZ2h0PSIxMCI+PHJlY3Qgd2lkdGg9IjEwIiBoZWlnaHQ9IjEwIiBmaWxsPSIjZTVlN2ViIi8+PC9zdmc+';
                        framesPreviewHTML += `<div class="h-full shrink-0 aspect-[2/3] bg-white border shadow-sm rounded flex items-center justify-center overflow-hidden relative cursor-pointer hover:border-blue-500" onclick="openFolderEditor('${folder.id}', '${f.id}')" title="Edit ${f.name}">
                            <img src="${bgSrc}" class="w-full h-full object-contain">
                            <div class="absolute bottom-0 w-full bg-black/50 text-[8px] text-white text-center truncate px-1">${f.name}</div>
                        </div>`;
                    });
                    framesPreviewHTML += `</div></div>`;
                }

                const cardHTML = `
                    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm hover:shadow-lg transition-shadow p-5 flex flex-col group relative">
                        <div class="flex justify-between items-start mb-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-blue-50 text-[#355faa] flex items-center justify-center">
                                    <i class="fa-solid fa-folder-open text-lg"></i>
                                </div>
                                <div>
                                    <h3 class="font-bold text-gray-800 text-lg leading-tight truncate w-40" title="${folder.name}">${folder.name}</h3>
                                    <p class="text-[10px] text-gray-500 font-semibold">${frameCount} Frame Tersimpan</p>
                                </div>
                            </div>
                            <button onclick="deleteFolder('${folder.id}')" class="text-gray-300 hover:text-red-500 transition-colors opacity-0 group-hover:opacity-100"><i class="fa-solid fa-trash"></i></button>
                        </div>
                        
                        ${framesPreviewHTML}

                        <button onclick="openFolderEditor('${folder.id}')" class="mt-4 w-full py-2.5 rounded-xl border-2 border-dashed border-[#355faa] text-[#355faa] font-bold text-xs hover:bg-blue-50 transition-colors flex items-center justify-center gap-2">
                            <i class="fa-solid fa-plus"></i> Buat Frame Baru Disini
                        </button>
                    </div>
                `;
                grid.innerHTML += cardHTML;
            });
        }

        async function createNewFolder() {
            showPromptModal("Buat Folder Baru", "Masukkan nama Folder/Kategori baru:", async function(name) {
                if (name && name.trim() !== '') {
                    try {
                        const response = await fetch('/api/admin/create-folder', {
                            method: 'POST',
                            headers: { 
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken 
                            },
                            body: JSON.stringify({ name: name.trim() })
                        });
                        const result = await response.json();
                        if(result.success) {
                            fetchDatabase();
                            showAlertModal("Folder berhasil dibuat!");
                        }
                    } catch (e) {
                        showAlertModal("Gagal menghubungi server untuk membuat folder.", true);
                    }
                }
            });
        }

        async function deleteFolder(id) {
            showConfirmModal("Hapus folder ini beserta SEMUA frame di dalamnya?", async function() {
                try {
                    const dbId = id.replace('folder_', '');
                    const response = await fetch('/api/admin/delete-folder/' + dbId, {
                        method: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': csrfToken }
                    });
                    const result = await response.json();
                    if(result.success) {
                        fetchDatabase();
                        showAlertModal("Folder berhasil dihapus.");
                    }
                } catch(e) {
                    showAlertModal("Gagal menghapus folder dari server.", true);
                }
            });
        }

        function backToFolders() {
            document.getElementById('view-editor').classList.add('view-hidden');
            document.getElementById('view-folders').classList.remove('view-hidden');
            document.getElementById('header-actions').classList.remove('view-hidden');
            fetchDatabase(); 
        }

        // ==========================================
        // 3. EDITOR INITIALIZATION
        // ==========================================

        const canvas = new fabric.Canvas('c', { preserveObjectStacking: true, backgroundColor: null });
        let maskRects = [];
        let maskCounter = 0;
        let bgImageObject = null;
        let bgBase64 = null; 
        
        fabric.Object.prototype.setControlsVisibility({ mtr: false }); // Matikan rotasi

        function openFolderEditor(folderId, frameId = null) {
            activeFolderId = folderId;
            const folder = database.find(f => f.id === folderId);
            
            document.getElementById('view-folders').classList.add('view-hidden');
            document.getElementById('header-actions').classList.add('view-hidden');
            document.getElementById('view-editor').classList.remove('view-hidden');
            document.getElementById('editor-folder-badge').innerText = `Folder: ${folder.name}`;
            
            canvas.clear();
            maskRects = [];
            maskCounter = 0;
            bgImageObject = null;
            bgBase64 = null;
            document.getElementById('bg-label').innerText = "Upload Frame PNG/SVG";
            document.getElementById('bg-upload').value = "";
            document.getElementById('masks-container').innerHTML = '';
            
            let frameToEdit = null;
            if (frameId && folder.frames) {
                frameToEdit = folder.frames.find(f => f.id == frameId);
            }

            // Delay agar CSS Transition tidak mengganggu canvas
            setTimeout(() => {
                if (frameToEdit) {
                    // EDIT MODE
                    document.getElementById('frame-id').value = frameToEdit.id;
                    document.getElementById('frame-name').value = frameToEdit.name;
                    document.getElementById('frame-paper').value = frameToEdit.paperSize || "4R";
                    document.getElementById('frame-orientation').value = frameToEdit.orientation || "Portrait";
                    document.getElementById('frame-w').value = frameToEdit.width || 1200;
                    document.getElementById('frame-h').value = frameToEdit.height || 1800;
                    
                    updateCanvasDimensions(false); 
                    
                    if(frameToEdit.imageUrl) {
                        document.getElementById('bg-label').innerText = "(Gambar tersimpan di database)";
                        fabric.Image.fromURL(frameToEdit.imageUrl, function(img) {
                            const scaleX = canvas.width / img.width;
                            const scaleY = canvas.height / img.height;
                            img.set({
                                left: 0, top: 0, scaleX: scaleX, scaleY: scaleY,
                                selectable: false, evented: false, opacity: 1, isBackgroundLayer: true, name: 'Frame Overlay'
                            });
                            bgImageObject = img;
                            canvas.add(img);
                            img.bringToFront();
                            canvas.renderAll();
                            updateLayersUI();
                        });
                    }

                    if(frameToEdit.masks && Array.isArray(frameToEdit.masks)) {
                        frameToEdit.masks.forEach(m => addMask(m.w, m.h, m.x, m.y));
                    }
                } else {
                    // CREATE MODE
                    const newFrameCount = folder.frames ? folder.frames.length + 1 : 1;
                    document.getElementById('frame-id').value = `${folderId}_new_${Date.now()}`;
                    document.getElementById('frame-name').value = `Template Baru ${newFrameCount}`;
                    
                    if (folder.frames && folder.frames.length > 0) {
                        const lastFrame = folder.frames[folder.frames.length - 1];
                        document.getElementById('frame-paper').value = lastFrame.paperSize || "4R";
                        document.getElementById('frame-orientation').value = lastFrame.orientation || "Portrait";
                        document.getElementById('frame-w').value = lastFrame.width || 1200;
                        document.getElementById('frame-h').value = lastFrame.height || 1800;
                        updateCanvasDimensions(false); 
                        
                        if(lastFrame.masks) {
                            lastFrame.masks.forEach(m => addMask(m.w, m.h, m.x, m.y));
                            setTimeout(() => showAlertModal(`Auto-Copy: Masking disalin dari setingan sebelumnya.`), 200);
                        }
                    } else {
                        document.getElementById('frame-paper').value = "4R";
                        document.getElementById('frame-orientation').value = "Portrait";
                        document.getElementById('frame-w').value = "1200";
                        document.getElementById('frame-h').value = "1800";
                        updateCanvasDimensions();
                    }
                }
                updateLayersUI();
            }, 50);
        }

        // AUTO-FIT CANVAS RESPONSIVE LOGIC
        function autoFitCanvas() {
            const scrollArea = document.getElementById('scroll-area');
            if (!scrollArea) return;
            
            const padding = 60;
            const availableW = scrollArea.clientWidth - padding;
            const availableH = scrollArea.clientHeight - padding;
            
            const canvasW = canvas.getWidth();
            const canvasH = canvas.getHeight();
            
            if (canvasW === 0 || canvasH === 0) return;

            const scaleX = availableW / canvasW;
            const scaleY = availableH / canvasH;
            let scale = Math.min(scaleX, scaleY);
            
            if (scale > 1) scale = 1; 
            
            scale = Math.floor(scale * 100) / 100;
            if(scale <= 0) scale = 0.05;

            setZoom(scale);
        }

        function updateCanvasDimensions(resetInputs = true) {
            const wInput = document.getElementById('frame-w');
            const hInput = document.getElementById('frame-h');
            const orientation = document.getElementById('frame-orientation').value;
            const paper = document.getElementById('frame-paper').value;

            if(resetInputs && event && (event.target.id === 'frame-orientation' || event.target.id === 'frame-paper')) {
                if(paper === '2x6') { wInput.value = 600; hInput.value = 1800; }
                if(paper === '4R') { wInput.value = 1200; hInput.value = 1800; }
                if(paper === 'A4') { wInput.value = 2480; hInput.value = 3508; }
                if(paper === 'A3') { wInput.value = 3508; hInput.value = 4961; }
                
                if(orientation === 'Landscape') {
                    let temp = wInput.value;
                    wInput.value = hInput.value;
                    hInput.value = temp;
                }
            }

            const w = parseInt(wInput.value) || 1200;
            const h = parseInt(hInput.value) || 1800;
            
            canvas.setWidth(w);
            canvas.setHeight(h);
            
            const wrapper = document.getElementById('canvas-wrapper');
            if (wrapper) {
                wrapper.style.width = w + 'px';
                wrapper.style.height = h + 'px';
            }

            canvas.renderAll();
            autoFitCanvas();
        }

        function setZoom(val) {
            const wrapper = document.getElementById('canvas-wrapper');
            const container = document.getElementById('scale-container');
            
            wrapper.style.transform = `scale(${val})`;
            
            const scaledW = canvas.getWidth() * val;
            const scaledH = canvas.getHeight() * val;
            
            container.style.width = `${scaledW}px`;
            container.style.height = `${scaledH}px`;

            document.getElementById('zoom-val').innerText = Math.round(val * 100) + '%';
            document.getElementById('zoom-slider').value = val;
            
            setTimeout(() => {
                canvas.calcOffset();
            }, 50);
        }

        // ==========================================
        // 4. BACKGROUND & LAYERS
        // ==========================================

        function loadBackgroundImage(input) {
            if (input.files && input.files[0]) {
                const file = input.files[0];
                const reader = new FileReader();
                
                document.getElementById('bg-label').innerText = file.name;

                reader.onload = function(e) {
                    bgBase64 = e.target.result; 
                    if (bgImageObject) { canvas.remove(bgImageObject); }

                    fabric.Image.fromURL(e.target.result, function(img) {
                        const scaleX = canvas.width / img.width;
                        const scaleY = canvas.height / img.height;
                        
                        img.set({
                            left: 0, top: 0,
                            scaleX: scaleX, scaleY: scaleY,
                            selectable: false, 
                            evented: false,    
                            lockMovementX: true, lockMovementY: true,
                            lockScalingX: true, lockScalingY: true,
                            hasControls: false,
                            opacity: 1,        
                            isBackgroundLayer: true,
                            name: 'Frame Overlay'
                        });
                        
                        bgImageObject = img;
                        canvas.add(img);
                        img.bringToFront(); 
                        canvas.renderAll();
                        updateLayersUI();
                    });
                };
                reader.readAsDataURL(file);
            }
        }

        function removeBackgroundImage() {
            if (bgImageObject) {
                canvas.remove(bgImageObject);
                bgImageObject = null;
                bgBase64 = null;
                document.getElementById('bg-label').innerText = "Upload Frame PNG/SVG";
                document.getElementById('bg-upload').value = "";
                canvas.renderAll();
                updateLayersUI();
            }
        }

        function updateLayersUI() {
            const container = document.getElementById('layers-container');
            container.innerHTML = '';
            
            const objects = canvas.getObjects().slice().reverse();

            if (objects.length === 0) {
                container.innerHTML = `<p class="text-[10px] text-gray-400 text-center py-2">Belum ada lapisan.</p>`;
                return;
            }

            objects.forEach((obj, reversedIndex) => {
                const actualIndex = objects.length - 1 - reversedIndex;
                const isBg = obj.isBackgroundLayer;
                const name = isBg ? 'Gambar Frame (BG)' : `Grid Box #${obj.maskNumber}`;
                const icon = isBg ? '<i class="fa-regular fa-image text-pink-500"></i>' : '<i class="fa-solid fa-square text-indigo-500"></i>';
                
                const div = document.createElement('div');
                div.className = `flex items-center justify-between bg-white p-2 rounded-lg border border-gray-100 shadow-sm ${isBg ? 'cursor-default bg-gray-50' : 'cursor-pointer hover:border-blue-300'} ${canvas.getActiveObject() === obj ? 'ring-1 ring-blue-500' : ''}`;
                
                div.onclick = (e) => {
                    if (e.target.closest('button')) return; 
                    if (isBg) return; 
                    canvas.setActiveObject(obj);
                    canvas.renderAll();
                    updateLayersUI(); 
                };

                div.innerHTML = `
                    <div class="flex items-center gap-2">
                        ${icon}
                        <span class="text-[10px] font-semibold text-gray-700">${name} ${isBg ? '<i class="fa-solid fa-lock text-gray-400 ml-1" title="Terkunci di Atas"></i>' : ''}</span>
                    </div>
                    <div class="flex gap-1">
                        <button onclick="toggleVisibility(${actualIndex})" class="w-6 h-6 flex items-center justify-center rounded text-gray-400 hover:bg-gray-100 transition-colors" title="Hide/Show">
                            <i class="fa-solid ${obj.visible !== false ? 'fa-eye' : 'fa-eye-slash'} text-xs"></i>
                        </button>
                        ${isBg ? `
                        <button onclick="removeBackgroundImage()" class="w-6 h-6 flex items-center justify-center rounded text-red-400 hover:bg-red-50 transition-colors" title="Hapus">
                            <i class="fa-solid fa-trash text-xs"></i>
                        </button>
                        ` : ''}
                    </div>
                `;
                container.appendChild(div);
            });
        }

        function toggleVisibility(index) {
            const obj = canvas.item(index);
            if (obj) {
                obj.visible = obj.visible === false ? true : false;
                canvas.renderAll();
                updateLayersUI();
            }
        }

        // ==========================================
        // 5. MASKING MANAGEMENT
        // ==========================================

        function addMask(optW = null, optH = null, optX = null, optY = null) {
            maskCounter++;
            const id = 'mask_' + Date.now() + Math.floor(Math.random() * 100);
            
            const defaultW = optW || 200;
            const defaultH = optH || 200;
            const startX = optX !== null ? optX : (canvas.width / 2 - defaultW / 2);
            const startY = optY !== null ? optY : (canvas.height / 2 - defaultH / 2);
            
            const rect = new fabric.Rect({
                left: startX, top: startY,
                width: defaultW, height: defaultH,
                fill: 'rgba(53, 95, 170, 0.4)', 
                stroke: '#355faa', strokeWidth: 2,
                cornerColor: '#ffffff', cornerStrokeColor: '#355faa',
                transparentCorners: false,
                maskId: id,
                maskNumber: maskCounter 
            });

            canvas.add(rect);
            maskRects.push(rect);
            
            if (bgImageObject) {
                bgImageObject.bringToFront();
            }

            canvas.setActiveObject(rect);
            renderMaskUI(id, rect);
            updateLayersUI();

            rect.on('modified', function() { syncMaskToUI(rect); });
            rect.on('moving', function() { syncMaskToUI(rect); });
            rect.on('scaling', function() { syncMaskToUI(rect); });
        }

        function duplicateMask(id) {
            const rect = maskRects.find(r => r.maskId === id);
            if (rect) {
                const w = rect.width * rect.scaleX;
                const h = rect.height * rect.scaleY;
                addMask(w, h, rect.left + 50, rect.top + 50); 
            }
        }

        function renderMaskUI(id, rect) {
            const container = document.getElementById('masks-container');
            const x = Math.round(rect.left);
            const y = Math.round(rect.top);
            const w = Math.round(rect.width * rect.scaleX);
            const h = Math.round(rect.height * rect.scaleY);

            const div = document.createElement('div');
            div.id = `ui-${id}`;
            div.className = "bg-white border border-gray-200 rounded-lg p-3 relative group shadow-sm";
            
            div.innerHTML = `
                <div class="flex justify-between items-center mb-2">
                    <span class="text-[10px] font-bold text-gray-700 px-2 py-0.5 bg-gray-100 rounded">Grid #${rect.maskNumber}</span>
                    <div class="flex gap-1">
                        <button onclick="duplicateMask('${id}')" class="text-blue-500 hover:text-blue-700 bg-blue-50 w-6 h-6 rounded flex items-center justify-center transition-colors" title="Copy (Duplicate)">
                            <i class="fa-regular fa-copy text-[10px]"></i>
                        </button>
                        <button onclick="deleteMask('${id}')" class="text-red-400 hover:text-red-600 bg-red-50 w-6 h-6 rounded flex items-center justify-center transition-colors" title="Hapus Kotak">
                            <i class="fa-solid fa-trash text-[10px]"></i>
                        </button>
                    </div>
                </div>
                <div class="grid grid-cols-4 gap-2 text-center">
                    <div>
                        <label class="block text-[9px] text-gray-500">X</label>
                        <input type="number" id="${id}-x" value="${x}" oninput="syncUIToMask('${id}')" class="w-full text-xs p-1 border border-gray-200 bg-gray-50 rounded text-center outline-none focus:border-[#355faa]">
                    </div>
                    <div>
                        <label class="block text-[9px] text-gray-500">Y</label>
                        <input type="number" id="${id}-y" value="${y}" oninput="syncUIToMask('${id}')" class="w-full text-xs p-1 border border-gray-200 bg-gray-50 rounded text-center outline-none focus:border-[#355faa]">
                    </div>
                    <div>
                        <label class="block text-[9px] text-gray-500">W</label>
                        <input type="number" id="${id}-w" value="${w}" oninput="syncUIToMask('${id}')" class="w-full text-xs p-1 border border-gray-200 bg-gray-50 rounded text-center outline-none focus:border-[#355faa]">
                    </div>
                    <div>
                        <label class="block text-[9px] text-gray-500">H</label>
                        <input type="number" id="${id}-h" value="${h}" oninput="syncUIToMask('${id}')" class="w-full text-xs p-1 border border-gray-200 bg-gray-50 rounded text-center outline-none focus:border-[#355faa]">
                    </div>
                </div>
            `;
            container.appendChild(div);
        }

        function syncMaskToUI(rect) {
            const id = rect.maskId;
            const elX = document.getElementById(`${id}-x`);
            if(!elX) return; 
            
            elX.value = Math.round(rect.left);
            document.getElementById(`${id}-y`).value = Math.round(rect.top);
            document.getElementById(`${id}-w`).value = Math.round(rect.width * rect.scaleX);
            document.getElementById(`${id}-h`).value = Math.round(rect.height * rect.scaleY);
        }

        function syncUIToMask(id) {
            const rect = maskRects.find(r => r.maskId === id);
            if (!rect) return;

            const x = parseInt(document.getElementById(`${id}-x`).value) || 0;
            const y = parseInt(document.getElementById(`${id}-y`).value) || 0;
            const w = parseInt(document.getElementById(`${id}-w`).value) || 10;
            const h = parseInt(document.getElementById(`${id}-h`).value) || 10;

            rect.set({
                left: x, top: y,
                width: w, height: h,
                scaleX: 1, scaleY: 1
            });
            
            rect.setCoords(); 
            canvas.renderAll();
        }

        function deleteMask(id) {
            const rect = maskRects.find(r => r.maskId === id);
            if (rect) {
                canvas.remove(rect);
                maskRects = maskRects.filter(r => r.maskId !== id);
                document.getElementById(`ui-${id}`).remove();
                canvas.renderAll();
                updateLayersUI();
            }
        }

        // ==========================================
        // 6. API SAVING & INTEGRATION
        // ==========================================

        async function saveFrameToFolder() {
            if (!activeFolderId) return;

            const btnSave = document.getElementById('btn-save-frame');
            btnSave.innerHTML = `<i class="fa-solid fa-circle-notch fa-spin"></i> Menyimpan...`;
            btnSave.disabled = true;

            const fId = document.getElementById('frame-id').value;
            const fName = document.getElementById('frame-name').value || 'Untitled Frame';
            const fOrient = document.getElementById('frame-orientation').value;

            const masksData = maskRects.map(rect => {
                return {
                    x: Math.round(rect.left),
                    y: Math.round(rect.top),
                    w: Math.round(rect.width * rect.scaleX),
                    h: Math.round(rect.height * rect.scaleY)
                };
            });

            // PERBAIKAN: Payload disesuaikan HANYA dengan struktur tabel di database Anda (tanpa width & height)
            const payload = {
                folder_id: activeFolderId,
                frame_id: fId, 
                name: fName,
                orientation: fOrient,
                masks: masksData,
                image_base64: bgBase64 
            };

            try {
                // Catatan: Pastikan route POST /api/admin/save-frame ini terdaftar
                const response = await fetch('/api/admin/save-frame', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify(payload)
                });

                if (!response.ok) {
                    // Tangkap pesan error dari Laravel jika bukan 200 OK!
                    let errMsg = `HTTP ${response.status}`;
                    const rawText = await response.text(); // BACA SEBAGAI TEXT DULU AGAR STREAM TIDAK TERKUNCI
                    
                    try {
                        const errData = JSON.parse(rawText); // Lalu coba ubah teks tadi menjadi JSON
                        errMsg = errData.message || errMsg;
                    } catch(err) {
                        // Jika gagal (berarti isinya HTML), ambil judul error-nya
                        const match = rawText.match(/<title>(.*?)<\/title>/);
                        if(match && match[1]) errMsg = match[1];
                        else errMsg = rawText.substring(0, 100);
                    }
                    throw new Error(errMsg);
                }

                const result = await response.json();
                
                if (result.success) {
                    showAlertModal("Frame berhasil disimpan ke Database!");
                    fetchDatabase(); 
                    setTimeout(backToFolders, 1500); 
                } else {
                    showAlertModal("Gagal menyimpan: " + (result.message || "Error tidak diketahui"), true);
                }
            } catch (e) {
                console.error("Save Error:", e);
                showAlertModal("Gagal menyimpan ke server.\n\nDetail: " + e.message, true);
            } finally {
                btnSave.innerHTML = `<i class="fa-solid fa-save"></i> Simpan Frame`;
                btnSave.disabled = false;
            }
        }

        // --- GLOBAL EVENT LISTENERS ---
        canvas.on('selection:created', updateLayersUI);
        canvas.on('selection:updated', updateLayersUI);
        canvas.on('selection:cleared', updateLayersUI);

        canvas.on('mouse:down', function(opt) {
            if (!opt.target) {
                canvas.discardActiveObject();
                canvas.renderAll();
                updateLayersUI();
            }
        });

        document.addEventListener('keydown', (e) => {
            const isInputFocus = document.activeElement.tagName === 'INPUT' || document.activeElement.tagName === 'TEXTAREA';
            
            if (!isInputFocus) {
                const activeObj = canvas.getActiveObject();
                if (e.key === 'Delete' || e.key === 'Backspace') {
                    if (activeObj && activeObj.maskId) {
                        deleteMask(activeObj.maskId);
                    }
                }
                if ((e.ctrlKey || e.metaKey) && e.key === 'c') {
                    if (activeObj && activeObj.maskId) {
                        duplicateMask(activeObj.maskId);
                    }
                }
            }
        });

        window.addEventListener('resize', () => {
            if (!document.getElementById('view-editor').classList.contains('view-hidden')) {
                autoFitCanvas();
            }
        });

        // Initialize on load
        document.addEventListener('DOMContentLoaded', fetchDatabase);

    </script>
</body>
</html>