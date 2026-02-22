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

        .tool-btn {
            @apply p-3 rounded-xl text-gray-500 hover:text-gray-900 hover:bg-gray-100 transition-all duration-200 flex flex-col items-center justify-center gap-1;
        }
        
        input[type=range] {
            @apply w-full h-1 bg-gray-200 rounded-lg appearance-none cursor-pointer;
        }
        input[type=range]::-webkit-slider-thumb {
            @apply appearance-none w-3 h-3 rounded-full shadow transition-colors;
            background-color: #355faa;
        }

        /* Sidebar Styles */
        .sidebar-item {
            transition: all 0.2s;
            cursor: grab;
        }
        .sidebar-item:active {
            cursor: grabbing;
        }
        .sidebar-item:hover {
            border-color: #355faa;
        }

        #active-crop-ui, #active-zoom-ui {
            animation: fadeIn 0.2s ease-out;
        }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

        /* Setup Page Animations */
        #setup-page {
            animation: slideIn 0.3s ease-out;
        }
        @keyframes slideIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }

        @media print {
            @page { margin: 0; size: auto; }
            header, .left-sidebar, .right-sidebar, .floating-bar, #active-crop-ui, #active-zoom-ui, #setup-page { display: none !important; }
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
                        <p class="text-white/80 text-sm leading-relaxed">Persiapkan area kerja Anda. Pilih layout grid yang diinginkan, upload referensi Grid, Frame, dan foto-foto.</p>
                    </div>
                    <div class="mt-8 text-xs text-white/50">
                        &copy; 2024 Snap Edit Frame Editor
                    </div>
                </div>

                <!-- Setup Form -->
                <div class="w-full md:w-2/3 p-8 space-y-6">
                    
                    <!-- 1. Canvas Size -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">1. Ukuran Canvas</label>
                        <div class="grid grid-cols-2 gap-3">
                            <select id="setup-size" class="w-full p-3 border border-gray-200 rounded-xl bg-gray-50 text-sm focus:outline-none focus:border-[#355faa]">
                                <option value="4r">4R (4x6 inch)</option>
                                <option value="a4">A4 (International Paper)</option>
                            </select>
                            <select id="setup-orientation" class="w-full p-3 border border-gray-200 rounded-xl bg-gray-50 text-sm focus:outline-none focus:border-[#355faa]">
                                <option value="portrait">Portrait (Tegak)</option>
                                <option value="landscape">Landscape (Mendatar)</option>
                            </select>
                        </div>
                    </div>

                    <!-- 2. Grid Layout Selection -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            2. Pilih Layout Grid <span class="text-red-500">*</span>
                        </label>
                        <div class="grid grid-cols-6 gap-2">
                             <!-- Option 0: Bebas -->
                             <label class="cursor-pointer">
                                <input type="radio" name="grid-layout" value="0" class="peer hidden">
                                <div class="p-3 border rounded-xl hover:bg-gray-50 peer-checked:border-[#355faa] peer-checked:bg-blue-50 peer-checked:text-[#355faa] transition-all flex flex-col items-center gap-1">
                                    <i class="fa-solid fa-slash text-xl rotate-90"></i>
                                    <span class="text-[10px] font-medium">Bebas</span>
                                </div>
                            </label>
                            <!-- Grid Options -->
                            <label class="cursor-pointer">
                                <input type="radio" name="grid-layout" value="1" class="peer hidden" checked>
                                <div class="p-3 border rounded-xl hover:bg-gray-50 peer-checked:border-[#355faa] peer-checked:bg-blue-50 peer-checked:text-[#355faa] transition-all flex flex-col items-center gap-1">
                                    <i class="fa-regular fa-square text-xl"></i>
                                    <span class="text-[10px] font-medium">1 Grid</span>
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="grid-layout" value="2" class="peer hidden">
                                <div class="p-3 border rounded-xl hover:bg-gray-50 peer-checked:border-[#355faa] peer-checked:bg-blue-50 peer-checked:text-[#355faa] transition-all flex flex-col items-center gap-1">
                                    <i class="fa-solid fa-pause text-xl"></i>
                                    <span class="text-[10px] font-medium">2 Grid</span>
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="grid-layout" value="3" class="peer hidden">
                                <div class="p-3 border rounded-xl hover:bg-gray-50 peer-checked:border-[#355faa] peer-checked:bg-blue-50 peer-checked:text-[#355faa] transition-all flex flex-col items-center gap-1">
                                    <i class="fa-solid fa-bars text-xl"></i>
                                    <span class="text-[10px] font-medium">3 Grid</span>
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="grid-layout" value="4" class="peer hidden">
                                <div class="p-3 border rounded-xl hover:bg-gray-50 peer-checked:border-[#355faa] peer-checked:bg-blue-50 peer-checked:text-[#355faa] transition-all flex flex-col items-center gap-1">
                                    <i class="fa-solid fa-table-cells-large text-xl"></i>
                                    <span class="text-[10px] font-medium">4 Grid</span>
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="grid-layout" value="6" class="peer hidden">
                                <div class="p-3 border rounded-xl hover:bg-gray-50 peer-checked:border-[#355faa] peer-checked:bg-blue-50 peer-checked:text-[#355faa] transition-all flex flex-col items-center gap-1">
                                    <i class="fa-solid fa-table-cells text-xl"></i>
                                    <span class="text-[10px] font-medium">6 Grid</span>
                                </div>
                            </label>
                        </div>
                    </div>

                     <!-- 3. Grid Preview -->
                     <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            3. Referensi Nomor Grid (Preview Kiri)
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

                    <!-- 4. Frame -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            4. Frame Overlay <span class="text-red-500">*</span>
                        </label>
                        <label class="flex items-center gap-3 p-3 border border-dashed border-gray-300 rounded-xl cursor-pointer hover:bg-gray-50 transition-colors group">
                            <div class="w-10 h-10 rounded-lg bg-pink-50 text-pink-500 flex items-center justify-center group-hover:scale-110 transition-transform">
                                <i class="fa-solid fa-border-all"></i>
                            </div>
                            <div class="flex-1">
                                <span id="label-frame-file" class="text-sm text-gray-500">Upload Frame PNG (Wajib)</span>
                            </div>
                            <input type="file" id="input-frame-file" accept="image/png" class="hidden" onchange="updateFileLabel(this, 'label-frame-file')">
                        </label>
                    </div>

                    <!-- 5. Photos -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">5. Foto-foto</label>
                        <label class="flex flex-col items-center gap-2 p-6 border-2 border-dashed border-gray-200 rounded-xl cursor-pointer hover:bg-gray-50 hover:border-[#355faa] transition-all text-center">
                            <i class="fa-solid fa-images text-2xl text-gray-400"></i>
                            <span id="label-photos-file" class="text-sm text-gray-500">Klik untuk upload banyak foto sekaligus</span>
                            <input type="file" id="input-photos-file" accept="image/*" multiple class="hidden" onchange="updateFileLabel(this, 'label-photos-file', true)">
                        </label>
                    </div>

                    <div class="pt-4">
                        <button onclick="startEditor()" class="w-full py-4 rounded-xl bg-[#355faa] hover:bg-[#2d5191] text-white font-bold shadow-lg shadow-indigo-200 transition-all active:scale-[0.99] flex items-center justify-center gap-2">
                            Mulai Editing <i class="fa-solid fa-arrow-right"></i>
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
                <button onclick="backToSetup()" class="text-gray-400 hover:text-gray-800 transition-colors mr-2" title="Back to Setup">
                    <i class="fa-solid fa-arrow-left"></i>
                </button>
                <div class="w-8 h-8 rounded-lg flex items-center justify-center shadow-md shadow-[#355faa]/20" style="background-color: #355faa;">
                    <i class="fa-solid fa-layer-group text-white text-sm"></i>
                </div>
                <h1 class="font-semibold text-lg tracking-tight text-gray-900">Snap Edit</h1>
            </div>

            <div class="flex items-center gap-4">
                <div class="text-xs font-medium px-3 py-1.5 bg-gray-100 rounded-lg text-gray-600 border border-gray-200">
                    <span id="current-canvas-size">4R Portrait</span>
                </div>

                <div class="h-6 w-px bg-gray-200 mx-2"></div>

                <button onclick="printCanvas()" class="bg-white px-4 py-1.5 rounded-lg text-sm font-semibold transition-colors border flex items-center gap-2 shadow-sm" style="color: #355faa; border-color: rgba(53, 95, 170, 0.2);" title="Print (P)">
                    <i class="fa-solid fa-print"></i> Print
                </button>

                <button onclick="downloadImage()" class="text-black hover:opacity-90 px-4 py-1.5 rounded-lg text-sm font-semibold transition-colors shadow-lg shadow-yellow-200" style="background-color: #fbdc00;" title="Export (E)">
                    Export
                </button>
            </div>
        </header>

        <div class="flex-1 flex overflow-hidden">
            
            <!-- LEFT SIDEBAR: GRID & ASSETS -->
            <aside class="w-64 bg-white border-r border-gray-200 flex flex-col z-20 left-sidebar">
                
                <!-- Section 1: Grid Preview (RESTORED) -->
                <div class="h-1/2 border-b border-gray-200 flex flex-col">
                     <div class="p-3 border-b border-gray-100 bg-gray-50">
                        <h2 class="text-xs font-bold text-gray-500 uppercase tracking-widest flex items-center gap-2">
                            <i class="fa-solid fa-eye"></i> Grid Reference
                        </h2>
                    </div>
                    <div class="flex-1 p-4 bg-gray-100 flex items-center justify-center overflow-hidden relative group">
                        <img id="grid-preview-img" src="" class="max-w-full max-h-full object-contain shadow-sm hidden">
                        <div id="no-grid-msg" class="text-center text-gray-400 text-xs">
                            <i class="fa-solid fa-table-cells text-2xl mb-1"></i><br>
                            No Grid Preview
                        </div>
                    </div>
                </div>

                <!-- Section 2: Uploaded Photos -->
                <div class="h-1/2 flex flex-col">
                    <div class="p-3 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                        <h2 class="text-xs font-bold text-gray-500 uppercase tracking-widest flex items-center gap-2">
                            <i class="fa-regular fa-images"></i> Photos
                        </h2>
                        <!-- Added Upload Button Here -->
                        <button onclick="document.getElementById('add-more-photos').click()" class="w-6 h-6 rounded-md bg-[#355faa] text-white flex items-center justify-center hover:bg-[#2d5191] transition-colors shadow-sm" title="Tambah Foto">
                            <i class="fa-solid fa-plus text-xs"></i>
                        </button>
                        <input type="file" id="add-more-photos" accept="image/*" multiple onchange="appendPhotos(this)" class="hidden">
                    </div>
                    <div id="photo-gallery" class="flex-1 overflow-y-auto p-3 grid grid-cols-2 gap-2 content-start">
                        <!-- Photos will be injected here via JS -->
                        <div class="col-span-2 text-center text-gray-400 text-sm mt-10">
                            No photos uploaded.
                        </div>
                    </div>
                </div>
            </aside>

            <!-- CENTER: CANVAS WORKSPACE -->
            <main class="flex-1 relative workspace-bg flex flex-col items-center overflow-auto py-10" id="workspace-container">
                
                <!-- The Canvas -->
                <div class="relative shadow-xl shadow-gray-400/20 transition-transform duration-200 ease-out mb-24" id="canvas-wrapper">
                    <canvas id="c"></canvas>
                </div>

                <!-- NEW: FLOATING ZOOM SLIDER (MOVED FROM SIDEBAR) -->
                <div id="active-zoom-ui" class="fixed bottom-24 left-[calc(50%+10rem)] -translate-x-1/2 bg-white/90 backdrop-blur-md border border-gray-200 px-6 py-3 rounded-2xl shadow-xl flex flex-col items-center gap-2 z-40 hidden w-64">
                    <div class="flex justify-between w-full text-xs font-bold text-gray-600">
                        <span><i class="fa-solid fa-minus text-[10px]"></i> Zoom Out</span>
                        <span>Zoom In <i class="fa-solid fa-plus text-[10px]"></i></span>
                    </div>
                    <input type="range" id="floating-zoom-slider" min="0.1" max="3" step="0.05" value="1" oninput="updateGridImageZoom(this.value)" class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer">
                    <span class="text-[10px] text-gray-400">Geser untuk zoom foto dalam grid</span>
                </div>

                <!-- CROP CONFIRMATION OVERLAY (Still used for 'Bebas' mode or additional cropping) -->
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
                        <p class="text-sm">Select an object to edit</p>
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
                            <!-- UPDATED: Relative Zoom Slider (0.1x to 3x) -->
                            <input type="range" id="grid-zoom-slider" min="0.1" max="3" step="0.05" value="1" oninput="updateGridImageZoom(this.value)">
                            <div class="flex justify-between mt-1 text-[9px] text-gray-400">
                                <span>0.1x</span>
                                <span>1x</span>
                                <span>3x</span>
                            </div>
                            <p class="text-[10px] text-indigo-400 mt-2">Drag image to pan inside grid</p>
                        </div>

                        <!-- GRID SETTINGS CONTROL (Global for Grid Mode) -->
                        <div id="grid-settings-panel" class="hidden border border-gray-200 bg-white p-4 rounded-xl shadow-sm space-y-4">
                            <h3 class="text-xs font-bold text-gray-700 mb-2 uppercase tracking-wider">Grid Layout Settings</h3>
                            
                            <!-- Gap Slider -->
                            <div>
                                <label class="text-[10px] font-semibold text-gray-500 mb-1 flex justify-between">
                                    <span>Jarak Antar Masking</span>
                                    <span id="val-gap">10</span>
                                </label>
                                <input type="range" id="input-gap" min="0" max="50" value="10" oninput="updateGridSettings('gap', this.value)">
                            </div>

                            <!-- Top Margin -->
                            <div>
                                <label class="text-[10px] font-semibold text-gray-500 mb-1 flex justify-between">
                                    <span>Margin Atas</span>
                                    <span id="val-mt">20</span>
                                </label>
                                <input type="range" id="input-mt" min="0" max="100" value="20" oninput="updateGridSettings('top', this.value)">
                            </div>

                            <!-- Bottom Margin -->
                            <div>
                                <label class="text-[10px] font-semibold text-gray-500 mb-1 flex justify-between">
                                    <span>Margin Bawah</span>
                                    <span id="val-mb">20</span>
                                </label>
                                <input type="range" id="input-mb" min="0" max="100" value="20" oninput="updateGridSettings('bottom', this.value)">
                            </div>

                            <!-- Left Margin -->
                            <div>
                                <label class="text-[10px] font-semibold text-gray-500 mb-1 flex justify-between">
                                    <span>Margin Kiri</span>
                                    <span id="val-ml">20</span>
                                </label>
                                <input type="range" id="input-ml" min="0" max="100" value="20" oninput="updateGridSettings('left', this.value)">
                            </div>

                            <!-- Right Margin -->
                            <div>
                                <label class="text-[10px] font-semibold text-gray-500 mb-1 flex justify-between">
                                    <span>Margin Kanan</span>
                                    <span id="val-mr">20</span>
                                </label>
                                <input type="range" id="input-mr" min="0" max="100" value="20" oninput="updateGridSettings('right', this.value)">
                            </div>
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
        // --- Setup & Config ---
        fabric.Object.NUM_FRACTION_DIGITS = 8; 

        const canvas = new fabric.Canvas('c', {
            preserveObjectStacking: true,
            backgroundColor: '#ffffff',
            selection: true
        });

        // Ensure clipPath and other custom props are saved
        const SERIALIZATION_PROPS = [
            'id', 'isFrame', 'isPlaceholder', 'isGridImage', 'lockMovementX', 'lockMovementY', 
            'lockScalingX', 'lockScalingY', 'lockRotation', 
            'hasControls', 'hasBorders', 'perPixelTargetFind', 'selectable', 
            'clipPath', 'baseScale', 'absolutePositioned'
        ];

        const sizes = {
            a4: { w: 794, h: 1123 },
            '4r': { w: 600, h: 900 }
        };

        let cropOverlay = null;
        let croppingImage = null;
        let currentCropShape = 'square';
        let savedClipPath = null;
        let activeSlot = null; // Stores target slot for clicking
        
        // Data holding for setup
        let setupData = {
            gridType: 1,
            orientation: 'portrait',
            frameFile: null,
            photos: []
        };

        // Grid Settings State
        let gridConfig = {
            gap: 10,
            marginTop: 20,
            marginBottom: 20,
            marginLeft: 20,
            marginRight: 20
        };

        // --- Event Listeners ---
        
        canvas.on('selection:created', updateUI);
        canvas.on('selection:updated', updateUI);
        canvas.on('selection:cleared', updateUI);

        // --- Mouse Wheel Zoom Shortcut ---
        canvas.on('mouse:wheel', function(opt) {
            const activeObj = canvas.getActiveObject();
            if (activeObj && activeObj.isGridImage && activeObj.baseScale) {
                const delta = opt.e.deltaY;
                let zoom = activeObj.scaleX / activeObj.baseScale;
                
                // Adjust zoom sensitivity
                if (delta < 0) {
                    zoom += 0.1; // Zoom In
                } else {
                    zoom -= 0.1; // Zoom Out
                }

                // Clamp values based on slider limits
                if (zoom > 3) zoom = 3;
                if (zoom < 0.1) zoom = 0.1;

                // Update Slider UI
                const slider = document.getElementById('floating-zoom-slider');
                const sidebarSlider = document.getElementById('grid-zoom-slider');
                if(slider) slider.value = zoom;
                if(sidebarSlider) sidebarSlider.value = zoom;

                // Apply Zoom
                updateGridImageZoom(zoom);

                opt.e.preventDefault();
                opt.e.stopPropagation();
            }
        });

        canvas.on('mouse:down', (e) => {
            // Handle slot selection logic with Animation (Blue Highlight)
            if (e.target && e.target.isPlaceholder) {
                // Reset previous selection
                if (activeSlot && activeSlot !== e.target) {
                    activeSlot.item(0).set({ stroke: '#9ca3af', strokeWidth: 2, fill: '#e5e7eb' });
                }
                activeSlot = e.target;
                // Animate/Highlight Active Slot
                activeSlot.item(0).set({ 
                    stroke: '#355faa', 
                    strokeWidth: 4, 
                    fill: 'rgba(53, 95, 170, 0.2)' // Blue tint
                });
                canvas.requestRenderAll();
                // We keep updateUI mostly for updating toolbar state, but grid settings are now global
            } else {
                // Deselect active slot
                if (activeSlot) {
                    activeSlot.item(0).set({ stroke: '#9ca3af', strokeWidth: 2, fill: '#e5e7eb' });
                    activeSlot = null;
                    canvas.requestRenderAll();
                }
            }
        });

        canvas.on('mouse:dblclick', function(e) {
            if (e.target && e.target.type === 'image' && !e.target.isFrame && !e.target.isGridImage) {
                if (e.target.clipPath) {
                    editCrop(e.target);
                } else {
                    initCropMode('square'); 
                }
            }
        });

        // --- SETUP PAGE LOGIC ---

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
            // 1. Get Settings
            const sizeKey = document.getElementById('setup-size').value;
            const orientation = document.getElementById('setup-orientation').value;
            const gridLayout = document.querySelector('input[name="grid-layout"]:checked').value;
            
            // Store global setup
            setupData.gridType = parseInt(gridLayout);
            setupData.orientation = orientation;

            // 2. Validation Checks (Frame Mandatory)
            const frameInput = document.getElementById('input-frame-file');
            const gridInput = document.getElementById('input-grid-file');

            if (!frameInput.files || !frameInput.files[0]) {
                alert("Mohon upload gambar Frame (Wajib) sebelum melanjutkan.");
                return;
            }

            // 3. Set Canvas
            setCanvasSize(sizeKey, orientation);
            
            // 4. GENERATE GRID (Adaptive Logic)
            if (setupData.gridType !== 0) {
                generateGridSlots(setupData.gridType, orientation);
            }

            // 5. Grid Preview (Restored Logic)
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

            // 6. Handle Frame
            if (frameInput.files && frameInput.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    addImageToCanvas(e.target.result, 'frame');
                };
                reader.readAsDataURL(frameInput.files[0]);
            }

            // 7. Handle Photos -> Populate Sidebar
            const photosInput = document.getElementById('input-photos-file');
            const gallery = document.getElementById('photo-gallery');
            gallery.innerHTML = ''; // Clear prev

            if (photosInput.files && photosInput.files.length > 0) {
                Array.from(photosInput.files).forEach(file => {
                    addPhotoToSidebar(file);
                });
            } else {
                gallery.innerHTML = '<div class="col-span-2 text-center text-gray-400 text-sm mt-10">No photos uploaded.</div>';
            }

            // Switch View
            document.getElementById('setup-page').classList.add('hidden');
            document.getElementById('app-container').classList.remove('hidden');
            document.getElementById('app-container').classList.add('flex');
            
            updateUI(); // Trigger UI check for grid mode
        }

        function backToSetup() {
            if(confirm("Kembali ke setup akan mereset canvas Anda. Lanjutkan?")) {
                window.location.reload();
            }
        }

        function addPhotoToSidebar(file) {
            const gallery = document.getElementById('photo-gallery');
            if(gallery.innerText.includes('No photos uploaded')) gallery.innerHTML = '';

            const reader = new FileReader();
            reader.onload = function(e) {
                const div = document.createElement('div');
                div.className = "sidebar-item relative group aspect-square rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden cursor-move hover:border-[#355faa] hover:shadow-md";
                div.draggable = true;
                div.title = file.name;
                
                div.addEventListener('dragstart', (ev) => {
                    ev.dataTransfer.setData("imageSrc", e.target.result);
                });

                // Changed: Removed transition-opacity, added opacity-0 group-hover:opacity-100 directly without transition
                div.innerHTML = `
                    <img src="${e.target.result}" class="w-full h-full object-cover cursor-pointer" onclick="handleSidebarPhotoClick('${e.target.result}')">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 flex flex-col justify-end p-2 pointer-events-none">
                         <p class="text-[10px] text-white font-medium truncate drop-shadow-md">${file.name}</p>
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
            // RESTRICT IMPORT: Only allow if 'Bebas' mode or a slot is active
            if (setupData.gridType !== 0 && !activeSlot) {
                alert("Silakan pilih kotak grid terlebih dahulu untuk memasukkan foto.");
                return;
            }

            if (activeSlot) {
                fillSlotWithImage(url, activeSlot);
                // Reset active slot visual
                activeSlot.item(0).set({ stroke: '#9ca3af', strokeWidth: 2, fill: '#e5e7eb' });
                activeSlot = null;
                canvas.requestRenderAll();
            } else {
                // Free mode import
                addImageToCanvas(url, 'image');
            }
        }

        function appendPhotos(input) {
            if (input.files && input.files.length > 0) {
                Array.from(input.files).forEach((file, index) => {
                    addPhotoToSidebar(file);
                });
            }
            input.value = '';
        }

        // --- ADAPTIVE GRID GENERATION ---
        function generateGridSlots(type, orientation) {
            // Clear existing placeholders
            const existing = canvas.getObjects().filter(o => o.isPlaceholder);
            existing.forEach(o => canvas.remove(o));

            const w = canvas.getWidth();
            const h = canvas.getHeight();
            
            // Calculate Usable Area based on Margins
            const usableW = w - (gridConfig.marginLeft + gridConfig.marginRight);
            const usableH = h - (gridConfig.marginTop + gridConfig.marginBottom);

            let rows = 1, cols = 1;

            if (type === 1) {
                rows = 1; cols = 1;
            } 
            else if (type === 2) { 
                if (orientation === 'landscape') { rows = 1; cols = 2; }
                else { rows = 2; cols = 1; }
            }
            else if (type === 3) { 
                if (orientation === 'landscape') { rows = 1; cols = 3; }
                else { rows = 3; cols = 1; }
            }
            else if (type === 4) { 
                rows = 2; cols = 2; 
            }
            else if (type === 6) { 
                if (orientation === 'landscape') { rows = 2; cols = 3; }
                else { rows = 3; cols = 2; }
            }

            const cellW = (usableW - (gridConfig.gap * (cols - 1))) / cols;
            const cellH = (usableH - (gridConfig.gap * (rows - 1))) / rows;

            for (let r = 0; r < rows; r++) {
                for (let c = 0; c < cols; c++) {
                    const left = gridConfig.marginLeft + (c * (cellW + gridConfig.gap));
                    const top = gridConfig.marginTop + (r * (cellH + gridConfig.gap));

                    createSlot(left, top, cellW, cellH);
                }
            }
            canvas.requestRenderAll();
        }

        function createSlot(x, y, w, h) {
            const rect = new fabric.Rect({
                width: w, height: h,
                fill: '#e5e7eb', // gray-200
                stroke: '#9ca3af', // gray-400
                strokeWidth: 2,
                strokeDashArray: [5, 5],
                originX: 'center', originY: 'center'
            });

            const icon = new fabric.Text('+', {
                fontSize: 40, fill: '#6b7280',
                originX: 'center', originY: 'center',
                fontFamily: 'Arial', fontWeight: 'bold'
            });
            
            const text = new fabric.Text('Click to Select', {
                fontSize: 14, fill: '#6b7280',
                originX: 'center', originY: 'center',
                top: 25, fontFamily: 'Inter'
            });

            const group = new fabric.Group([rect, icon, text], {
                left: x + w/2, top: y + h/2,
                originX: 'center', originY: 'center',
                selectable: false, // Important: Selection logic handled manually to persist settings panel
                hoverCursor: 'pointer',
                isPlaceholder: true,
                hasControls: false,
                lockMovementX: true, lockMovementY: true,
                name: 'slotGroup'
            });

            canvas.add(group);
            canvas.sendToBack(group); 
        }

        // --- GRID SETTINGS FUNCTIONS ---
        function updateGridSettings(type, value) {
            const val = parseInt(value);
            
            if (type === 'gap') { gridConfig.gap = val; document.getElementById('val-gap').innerText = val; }
            if (type === 'top') { gridConfig.marginTop = val; document.getElementById('val-mt').innerText = val; }
            if (type === 'bottom') { gridConfig.marginBottom = val; document.getElementById('val-mb').innerText = val; }
            if (type === 'left') { gridConfig.marginLeft = val; document.getElementById('val-ml').innerText = val; }
            if (type === 'right') { gridConfig.marginRight = val; document.getElementById('val-mr').innerText = val; }
            
            // Re-generate grid based on new margins
            if (setupData.gridType !== 0) {
                generateGridSlots(setupData.gridType, setupData.orientation);
            }
        }

        // --- FILL SLOT LOGIC (With Relative Zoom & Pan Support) ---
        function fillSlotWithImage(url, slotGroup) {
            fabric.Image.fromURL(url, function(img) {
                // Determine slot dimensions
                const slotW = slotGroup.width * slotGroup.scaleX;
                const slotH = slotGroup.height * slotGroup.scaleY;
                const slotX = slotGroup.left;
                const slotY = slotGroup.top;

                // Scale image to COVER the slot (Initial scale)
                const scaleX = slotW / img.width;
                const scaleY = slotH / img.height;
                const scale = Math.max(scaleX, scaleY);

                // Create Absolute Clip Path (Rect matching slot EXACTLY)
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
                    baseScale: scale, // STORE BASE SCALE FOR RELATIVE ZOOM
                    clipPath: clipRect,
                    cornerColor: '#355faa', borderColor: '#355faa',
                    cornerStyle: 'circle', transparentCorners: false, cornerSize: 12,
                    isFrame: false,
                    isGridImage: true, 
                    hasControls: true, 
                    lockRotation: true,
                    lockScalingX: true, lockScalingY: true 
                });

                canvas.add(img);
                
                // Layering: Place properly
                const topFrame = canvas.getObjects().find(o => o.isFrame);
                if (topFrame) {
                    img.moveTo(canvas.getObjects().indexOf(topFrame));
                } else {
                    img.sendToBack(); 
                }

                // Hide Placeholder instead of removing to keep grid logic cleaner
                slotGroup.visible = false;
                
                canvas.setActiveObject(img);
                canvas.renderAll();
            });
        }

        // --- RELATIVE ZOOM SLIDER LOGIC ---
        function updateGridImageZoom(value) {
            const obj = canvas.getActiveObject();
            if (obj && obj.isGridImage && obj.baseScale) {
                // Calculate new absolute scale based on relative value
                const newScale = obj.baseScale * parseFloat(value);
                obj.scale(newScale);
                canvas.requestRenderAll();
            }
        }

        // --- Core Functions ---

        function setCanvasSize(sizeKey, orientation) {
            let w = sizes[sizeKey].w;
            let h = sizes[sizeKey].h;
            if (orientation === 'landscape') [w, h] = [h, w];

            canvas.setWidth(w);
            canvas.setHeight(h);
            canvas.renderAll();
            
            const labelMap = { a4: 'A4', '4r': '4R' };
            const orientLabel = orientation.charAt(0).toUpperCase() + orientation.slice(1);
            document.getElementById('current-canvas-size').innerText = `${labelMap[sizeKey]} ${orientLabel}`;
        }

        function addImageToCanvas(url, type, dropEvent = null) {
            // Check for drop onto slot FIRST
            if (dropEvent) {
                 const pointer = canvas.getPointer(dropEvent);
                 const objects = canvas.getObjects();
                 const targetSlot = objects.find(obj => obj.isPlaceholder && obj.containsPoint(pointer));

                 if (targetSlot) {
                     fillSlotWithImage(url, targetSlot);
                     return;
                 }
            }

            // RESTRICT IMPORT ON DROP if not Free mode and no target
            if (type !== 'frame' && setupData.gridType !== 0 && !dropEvent) {
                 // Block direct API calls or non-drop additions in grid mode
                 return; 
            }

            fabric.Image.fromURL(url, function(img) {
                const cvW = canvas.getWidth();
                const cvH = canvas.getHeight();

                if (type === 'frame') {
                    img.set({
                        left: 0, top: 0, originX: 'left', originY: 'top',
                        scaleX: cvW / img.width,
                        scaleY: cvH / img.height,
                        lockMovementX: true, lockMovementY: true,
                        lockScalingX: true, lockScalingY: true, lockRotation: true,
                        hasControls: false, hasBorders: true,
                        hoverCursor: 'default',
                        selectable: true,
                        perPixelTargetFind: true,
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
                        cornerStyle: 'circle', transparentCorners: false, cornerSize: 12,
                        isFrame: false,
                        isGridImage: false // Free floating images
                    });
                    canvas.add(img);
                    
                    let topFrame = canvas.getObjects().find(o => o.isFrame);
                    if (topFrame) {
                        img.moveTo(canvas.getObjects().indexOf(topFrame));
                    } else {
                        img.sendToBack();
                    }
                    canvas.setActiveObject(img);
                }
                canvas.renderAll();
            });
        }

        // --- Drag & Drop Implementation ---
        const workspace = document.getElementById('workspace-container');
        workspace.addEventListener('dragover', (e) => { e.preventDefault(); workspace.classList.add('drag-active'); });
        workspace.addEventListener('dragleave', () => { workspace.classList.remove('drag-active'); });
        workspace.addEventListener('drop', (e) => {
            e.preventDefault();
            workspace.classList.remove('drag-active');
            
            const imageSrc = e.dataTransfer.getData("imageSrc");
            if (imageSrc) {
                // If in Grid Mode, we only allow if dropping on a slot
                if (setupData.gridType !== 0) {
                    const pointer = canvas.getPointer(e);
                    const targetSlot = canvas.getObjects().find(obj => obj.isPlaceholder && obj.containsPoint(pointer));
                    if (!targetSlot) {
                        alert("Dalam Mode Grid, geser foto langsung ke dalam kotak area (masking).");
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

        // --- Print & UI Utils ---
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
            
            img.src = canvas.toDataURL({ format: 'png', multiplier: 3 });
            
            placeholders.forEach(p => p.visible = true);
            canvas.renderAll();

            img.onload = () => window.print();
        }

        function updateUI() {
            const activeObj = canvas.getActiveObject();
            const noSel = document.getElementById('no-selection-msg');
            const controls = document.getElementById('object-controls');
            const lockBtn = document.getElementById('lockBtn');
            const objLabel = document.getElementById('obj-type-label');
            const objIcon = document.getElementById('obj-icon');
            const cropSection = document.getElementById('crop-tools-section');
            const zoomPanel = document.getElementById('active-zoom-ui'); // CHANGED ID
            const zoomSlider = document.getElementById('floating-zoom-slider'); // CHANGED ID
            const gridSettings = document.getElementById('grid-settings-panel');
            
            // --- LOGIC: SHOW GRID SETTINGS GLOBALLY IN GRID MODE ---
            const isGridMode = setupData.gridType !== 0;

            if (!activeObj) {
                if (isGridMode) {
                    // Show Grid Settings Panel instead of "No Selection"
                    noSel.classList.add('hidden');
                    controls.classList.remove('hidden');
                    
                    objLabel.innerText = "Grid Layout Settings";
                    objIcon.className = "fa-solid fa-border-none";
                    
                    gridSettings.classList.remove('hidden');
                    cropSection.classList.add('hidden');
                    zoomPanel.classList.add('hidden');
                    lockBtn.parentElement.classList.add('hidden'); 
                } else {
                    // Normal "No Selection" state
                    noSel.classList.remove('hidden');
                    controls.classList.add('hidden');
                }
                return;
            }

            // If we have an active object, show controls
            noSel.classList.add('hidden');
            controls.classList.remove('hidden');
            lockBtn.parentElement.classList.remove('hidden'); 

            // PLACEHOLDER SELECTED (Though selectable is false usually, just in case)
            if (activeObj.isPlaceholder) {
                // If clicked manually
                canvas.discardActiveObject(); // Force deselect to show global settings
                updateUI(); // Recurse
                return; 
            } else {
                // Hide Grid Settings if object is selected
                gridSettings.classList.add('hidden');
            }

            // --- GRID ZOOM UI LOGIC ---
            if (activeObj.isGridImage) {
                zoomPanel.classList.remove('hidden');
                // Calculate relative slider value based on baseScale
                if (activeObj.baseScale) {
                    zoomSlider.value = (activeObj.scaleX / activeObj.baseScale).toFixed(2);
                } else {
                    zoomSlider.value = 1; // Fallback
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
                lockBtn.innerHTML = '<i class="fa-solid fa-ban"></i> Fixed Position (Q)';
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

        // --- CROP ENGINE ---
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
                fill: 'rgba(0,0,0,0.3)', stroke: '#fff', strokeWidth: 2, strokeDashArray: [6, 6],
                transparentCorners: false, cornerColor: 'white', cornerStrokeColor: '#000', cornerSize: 10,
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
                clipPath = new fabric.Rect({
                    width: cropOverlay.getScaledWidth() / croppingImage.scaleX,
                    height: cropOverlay.getScaledHeight() / croppingImage.scaleY,
                    left: localPoint.x, top: localPoint.y, originX: 'center', originY: 'center'
                });
            } else if (currentCropShape === 'circle') {
                clipPath = new fabric.Circle({
                    radius: (cropOverlay.radius * cropOverlay.scaleX) / croppingImage.scaleX,
                    left: localPoint.x, top: localPoint.y, originX: 'center', originY: 'center'
                });
            } else {
                clipPath = new fabric.Path('M 10,30 A 20,20 0,0,1 50,30 A 20,20 0,0,1 90,30 Q 90,60 50,90 Q 10,60 10,30 z', {
                    scaleX: cropOverlay.scaleX / croppingImage.scaleX,
                    scaleY: cropOverlay.scaleY / croppingImage.scaleY,
                    left: localPoint.x, top: localPoint.y, originX: 'center', originY: 'center'
                });
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
            if (activeObj && !activeObj.isFrame) {
                activeObj.set({ clipPath: null, dirty: true });
                canvas.renderAll();
            }
        }

        // --- Arrangement Fix ---
        function enforceFrameLayering() {
            const objects = canvas.getObjects();
            const frames = objects.filter(o => o.isFrame);
            frames.forEach(f => f.bringToFront());
            
            // Also ensure placeholders are at the back
            const placeholders = objects.filter(o => o.isPlaceholder);
            placeholders.forEach(p => p.sendToBack());
            
            canvas.renderAll();
        }

        function bringForward() { 
            const obj = canvas.getActiveObject();
            if(obj && !obj.isFrame) { 
                canvas.bringForward(obj); 
                enforceFrameLayering();
            }
        }
        function sendBackward() { 
            const obj = canvas.getActiveObject();
            if(obj && !obj.isFrame) { 
                canvas.sendBackwards(obj); 
            }
        }
        function bringToFront() { if(canvas.getActiveObject()){ canvas.bringToFront(canvas.getActiveObject()); enforceFrameLayering(); }}
        function sendToBack() { if(canvas.getActiveObject()){ canvas.sendToBack(canvas.getActiveObject()); }}

        // --- History / Utils ---
        function deleteActiveObject() {
            const activeObj = canvas.getActiveObject();
            
            // 1. Safety Check: Nothing selected
            if (!activeObj) return;

            // 2. Protection: Prevent deleting Frames or Placeholders (Masking)
            if (activeObj.isFrame || activeObj.isPlaceholder) {
                return;
            }

            // 3. Smart Delete: If deleting a photo inside a grid, show the placeholder again
            if (activeObj.isGridImage && activeObj.clipPath) {
                const clip = activeObj.clipPath;
                const placeholders = canvas.getObjects().filter(o => o.isPlaceholder);
                
                // Find the placeholder that matches the clip position
                const associatedSlot = placeholders.find(p => 
                    Math.abs(p.left - clip.left) < 1 && Math.abs(p.top - clip.top) < 1
                );

                if (associatedSlot) {
                    associatedSlot.set('visible', true);
                }
            }

            // 4. Remove the object
            canvas.remove(activeObj); 
        }

        function downloadImage() {
            if(cropOverlay) cancelCrop();
            // Hide Placeholders for export
            const placeholders = canvas.getObjects().filter(o => o.isPlaceholder);
            placeholders.forEach(p => p.visible = false);

            canvas.discardActiveObject();
            canvas.renderAll();
            
            const link = document.createElement('a');
            link.download = `snapedit-export.png`; // CHANGED: PNG extension
            // CHANGED: PNG format, No quality (lossless), Multiplier 2
            link.href = canvas.toDataURL({ format: 'png', multiplier: 2 });
            
            placeholders.forEach(p => p.visible = true);
            canvas.renderAll();

            link.click();
        }

        // --- Keyboard Shortcuts ---
        document.addEventListener('keydown', (e) => {
            const isCtrl = e.ctrlKey || e.metaKey;
            const key = e.key.toLowerCase();
            const activeObj = canvas.getActiveObject();

            // Arrow Keys for Movement (UPDATED LOGIC)
            // Works for any object (Grid Photo or Free Photo) unless locked
            if (['arrowup', 'arrowdown', 'arrowleft', 'arrowright'].includes(key)) {
                if (activeObj && !activeObj.isFrame && !activeObj.lockMovementX) {
                    e.preventDefault(); // Prevent page scroll
                    const step = e.shiftKey ? 10 : 1;
                    
                    if (key === 'arrowup') activeObj.top -= step;
                    else if (key === 'arrowdown') activeObj.top += step;
                    else if (key === 'arrowleft') activeObj.left -= step;
                    else if (key === 'arrowright') activeObj.left += step;

                    activeObj.setCoords();
                    canvas.requestRenderAll();
                }
                return;
            }

            // C = Crop (Only if image selected)
            if (key === 'c' && !isCtrl) {
                if(activeObj && activeObj.type === 'image' && !activeObj.isGridImage) {
                    initCropMode('square');
                }
            }

            // D = Delete
            if (key === 'd' && !isCtrl) { if (activeObj) deleteActiveObject(); }
            
            // P = Print
            if (key === 'p' && !isCtrl) { e.preventDefault(); printCanvas(); }

            // E = Export
            if (key === 'e' && !isCtrl) { e.preventDefault(); downloadImage(); }

            // R = Restart/Reload
            if (key === 'r' && !isCtrl) { 
                if(confirm("Reload page? Unsaved changes will be lost.")) window.location.reload(); 
            }

            // Q = Lock
            if (key === 'q' && !isCtrl) { 
                e.preventDefault(); 
                toggleLock(); 
            }

            // Standard Keys
            if (e.key === 'Enter' && cropOverlay) { e.preventDefault(); performCrop(); }
            if (e.key === 'Delete' || e.key === 'Backspace') { if (activeObj) deleteActiveObject(); }
            if (isCtrl && e.key === ']') { e.preventDefault(); bringForward(); }
            if (isCtrl && e.key === '[') { e.preventDefault(); sendBackward(); }
        });

    </script>
</body>
</html>