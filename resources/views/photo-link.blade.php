<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Snap Link - Drive Gallery</title>
    <link rel="icon" href="{{ asset('snaplink.png') }}?v={{ time() }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gifshot/0.3.2/gifshot.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; -webkit-tap-highlight-color: transparent; background-color: #f9fafb; color: #111827; }
        :root { --primary: #355faa; --action: #fbdc00; }
        .dot-grid { background-image: radial-gradient(#d1d5db 1px, transparent 1px); background-size: 20px 20px; }
        .btn-touch { transition: transform 0.1s; }
        .btn-touch:active { transform: scale(0.96); }
        .shadow-glow { box-shadow: 0 10px 40px -10px rgba(53, 95, 170, 0.2); }
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        .gif-checkbox:checked + div { border-color: var(--primary); background-color: rgba(53, 95, 170, 0.1); }
        .gif-checkbox:checked + div .check-indicator { opacity: 1; transform: scale(1); }
    </style>
</head>
<body class="min-h-screen flex flex-col">

    @if ($mode === 'admin_dashboard')
    <!-- ========================================================== -->
    <!-- 1. DASHBOARD ADMIN (UI ORIGINAL)                           -->
    <!-- ========================================================== -->
    @php 
        $active_links = 0;
        $grouped_projects = [];
        $loose_projects = [];
        
        // PENGELOMPOKAN DATA (FOLDERING) DARI DATABASE LARAVEL
        if(isset($db_all)) {
            foreach($db_all as $album) { 
                if(strtotime($album->expires_at) > time()) $active_links++; 
                if(!empty($album->group_name)) {
                    $grouped_projects[$album->group_name][] = $album;
                } else {
                    $loose_projects[] = $album;
                }
            }
        }
    @endphp

    <div class="flex-1 bg-[#f3f4f6] dot-grid flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <aside class="hidden md:flex w-72 bg-white border-r border-gray-200 flex-col z-20 shadow-sm">
            <div class="p-6 border-b border-gray-100">
                <div class="flex items-center gap-3 mb-1">
                    <div class="w-8 h-8 bg-[#355faa] rounded-lg flex items-center justify-center text-white"><i data-lucide="folder-cloud" size="18"></i></div>
                    <span class="font-bold text-lg">Snap Link API</span>
                </div>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider pl-11">Admin Panel</p>
            </div>
            <nav class="flex-1 p-4 space-y-2">
                <div class="bg-blue-50 text-[#355faa] p-3 rounded-xl flex items-center gap-3 font-bold text-sm cursor-pointer">
                    <i data-lucide="folder-open" size="18"></i> Proyek Klien
                </div>
            </nav>
            <div class="p-4 border-t border-gray-100">
                <a href="{{ url('/admin') }}" class="flex items-center gap-3 p-3 rounded-xl text-gray-500 hover:bg-gray-50 font-bold text-sm"><i data-lucide="arrow-left" size="18"></i> Menu Utama</a>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col h-full overflow-hidden relative">
            <header class="md:hidden bg-white border-b border-gray-200 px-6 py-4 flex justify-between items-center z-20">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-[#355faa] rounded-xl flex items-center justify-center text-white"><i data-lucide="folder-cloud"></i></div>
                    <h2 class="font-bold text-lg leading-none">Snap Link</h2>
                </div>
                <a href="{{ url('/admin') }}" class="p-2 bg-gray-50 text-gray-500 rounded-lg"><i data-lucide="arrow-left" size="20"></i></a>
            </header>

            <main class="flex-1 overflow-y-auto p-4 md:p-10 custom-scrollbar">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6 mb-8">
                    <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm">
                        <p class="text-[10px] md:text-xs text-gray-400 font-bold uppercase mb-2">Total Proyek</p>
                        <p class="text-2xl md:text-3xl font-bold text-gray-800">{{ count($db_all ?? []) }}</p>
                    </div>
                    <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm">
                        <p class="text-[10px] md:text-xs text-gray-400 font-bold uppercase mb-2">Link Aktif</p>
                        <p class="text-2xl md:text-3xl font-bold text-[#355faa]">{{ $active_links }}</p>
                    </div>
                    <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm md:col-span-2 flex items-center justify-between">
                        <div>
                            <p class="text-[10px] md:text-xs text-gray-400 font-bold uppercase mb-1">Status Storage Server</p>
                            <p class="text-lg md:text-xl font-bold text-emerald-500 flex items-center gap-2">
                                <i data-lucide="cloud-check" size="18"></i> 0 MB (Google Drive)
                            </p>
                        </div>
                        <button onclick="toggleCreate()" class="hidden md:flex bg-[#355faa] text-white px-6 py-3 rounded-xl font-bold text-sm shadow-lg hover:bg-[#2d5191] items-center gap-2">
                            <i data-lucide="plus" size="18"></i> Hubungkan Drive Baru
                        </button>
                    </div>
                </div>

                <!-- Form Buat Baru (UI ORIGINAL) -->
                <div id="create-panel" class="hidden bg-white p-6 md:p-8 rounded-[2rem] shadow-xl border border-gray-200 mb-8 max-w-3xl mx-auto">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="font-bold text-xl">Hubungkan Folder G-Drive Baru</h3>
                        <button onclick="toggleCreate()" class="text-gray-400"><i data-lucide="x"></i></button>
                    </div>
                    
                    <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-xl">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" id="formIsBatch" class="w-5 h-5 text-[#355faa] bg-white border-gray-300 rounded focus:ring-[#355faa]">
                            <span class="text-sm font-bold text-[#355faa]">Tarik Otomatis dari Folder Utama (Batch Processing)</span>
                        </label>
                        <p class="text-xs text-blue-600 mt-2 ml-8">Centang jika G-Drive berisi banyak sub-folder. Sistem akan otomatis mendeteksi dan membuatkan folder & link klien secara instan.</p>
                    </div>

                    <form id="createForm" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="md:col-span-1">
                                <label class="block text-[10px] font-bold text-gray-500 uppercase mb-2">Paket</label>
                                <select id="formPaket" class="w-full bg-gray-50 p-4 rounded-xl border border-gray-200 outline-none">
                                    <option value="Self Photo">Self Photo</option>
                                    <option value="Photobox">Photobox</option>
                                    <option value="Pas Photo">Pas Photo</option>
                                </select>
                            </div>
                            <div class="md:col-span-2" id="nameContainer">
                                <label class="block text-[10px] font-bold text-gray-500 uppercase mb-2">Nama Klien</label>
                                <input type="text" id="formName" required class="w-full bg-gray-50 p-4 rounded-xl border border-gray-200 outline-none" placeholder="Cth: Sesi Budi & Siska">
                            </div>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-gray-500 uppercase mb-2">Link Google Drive (Folder Utama / Folder Klien)</label>
                            <input type="url" id="formDriveLink" required class="w-full bg-gray-50 p-4 rounded-xl border border-gray-200 outline-none" placeholder="Paste URL Folder G-Drive disini...">
                            <p class="text-[10px] text-red-500 mt-1 font-bold">*Pastikan akses folder Google Drive sudah diatur ke "Siapa saja yang memiliki link" (Public).</p>
                        </div>
                        
                        <div>
                            <label class="block text-[10px] font-bold text-gray-500 uppercase mb-2">Nama Folder di Dashboard (Opsional)</label>
                            <input type="text" id="formGroupName" class="w-full bg-gray-50 p-4 rounded-xl border border-gray-200 outline-none" placeholder="Kosongkan jika tak ingin dikelompokkan...">
                            <p class="text-[10px] text-gray-400 mt-1" id="groupHelperText">Jika mode Tarik Otomatis aktif dan dikosongkan, nama folder Dashboard akan mengikuti nama G-Drive.</p>
                        </div>

                        <div>
                            <label class="block text-[10px] font-bold text-gray-500 uppercase mb-2">Durasi Akses Galeri</label>
                            <select id="formHours" class="w-full bg-gray-50 p-4 rounded-xl border border-gray-200 outline-none">
                                <option value="168" selected>1 Minggu (168 Jam)</option>
                                <option value="336">2 Minggu (336 Jam)</option>
                                <option value="720">1 Bulan (720 Jam)</option>
                            </select>
                        </div>
                        <button id="btnSubmitCreate" type="submit" class="w-full bg-[#355faa] text-white py-4 rounded-xl font-bold uppercase tracking-widest flex justify-center items-center gap-2">
                            Terbitkan Galeri
                        </button>
                    </form>
                </div>

                <!-- RENDER FOLDER (GROUPED PROJECTS) -->
                @if(!empty($grouped_projects))
                <div class="mb-8">
                    <h3 class="font-bold text-gray-800 text-sm uppercase tracking-wider ml-1 mb-4">Folder Proyek</h3>
                    <div class="space-y-4">
                        @foreach ($grouped_projects as $group_name => $albums)
                        @php $g_id = md5($group_name); @endphp
                        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                            <div class="p-5 flex justify-between items-center cursor-pointer hover:bg-gray-50 transition-colors btn-toggle-folder" data-target="folder-{{ $g_id }}">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center text-[#355faa]"><i data-lucide="folder" size="24"></i></div>
                                    <div>
                                        <h4 class="font-bold text-gray-900 text-lg">{{ $group_name }}</h4>
                                        <p class="text-xs text-gray-500 font-medium">{{ count($albums) }} Link Klien Tersimpan</p>
                                    </div>
                                </div>
                                <i data-lucide="chevron-down" class="text-gray-400 transition-transform transform duration-300"></i>
                            </div>
                            <div id="folder-{{ $g_id }}" class="hidden border-t border-gray-100 p-5 bg-gray-50/50">
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                    @foreach ($albums as $album)
                                    @php $is_exp = strtotime($album->expires_at) < time(); @endphp
                                    <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm flex flex-col gap-3">
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <p class="text-[10px] font-black text-[#355faa] uppercase mb-1">{{ $album->paket }}</p>
                                                <h4 class="font-bold text-gray-900 truncate max-w-[150px]">{{ $album->name }}</h4>
                                                <p class="text-xs text-gray-400 font-mono mt-1">ID: {{ $album->album_id }}</p>
                                            </div>
                                            @if($is_exp) 
                                                <span class="bg-red-50 text-red-500 px-2 py-1 rounded-lg text-[10px] font-bold uppercase">Expired</span>
                                            @else 
                                                <span class="bg-emerald-50 text-emerald-600 px-2 py-1 rounded-lg text-[10px] font-bold uppercase dash-countdown" data-expire="{{ strtotime($album->expires_at) }}">Aktif</span> 
                                            @endif
                                        </div>
                                        <div class="flex gap-2 pt-1 mt-auto">
                                            <button onclick="copyLink('{{ $album->album_id }}')" class="flex-[2] bg-[#fbdc00] text-gray-900 py-2.5 rounded-xl text-xs font-bold btn-touch flex justify-center gap-2"><i data-lucide="copy" size="14"></i> Link Web</button>
                                            <button onclick="openEdit('{{ $album->album_id }}')" class="flex-1 bg-gray-100 text-gray-600 py-2.5 rounded-xl text-xs font-bold btn-touch hover:bg-gray-200"><i data-lucide="edit-3" size="14" class="mx-auto"></i></button>
                                            <button onclick="deleteAlbum('{{ $album->album_id }}')" class="px-3 bg-red-50 text-red-500 rounded-xl hover:bg-red-500 hover:text-white transition-colors btn-touch"><i data-lucide="trash-2" size="16"></i></button>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- RENDER LOOSE PROJECTS (TANPA FOLDER) -->
                @if(!empty($loose_projects) || empty($db_all))
                <div>
                    <h3 class="font-bold text-gray-800 text-sm uppercase tracking-wider ml-1 mb-4">Proyek Lainnya</h3>
                    @if (empty($db_all))
                        <div class="text-center py-20 bg-white rounded-3xl border border-dashed border-gray-200">
                            <i data-lucide="link-2-off" class="mx-auto text-gray-300 mb-3" size="48"></i>
                            <p class="text-gray-400 text-sm font-bold">Belum ada link proyek dibuat.</p>
                        </div>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4" id="projectsGrid">
                            @foreach ($loose_projects as $album)
                            @php $is_exp = strtotime($album->expires_at) < time(); @endphp
                            <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm flex flex-col gap-3">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <p class="text-[10px] font-black text-[#355faa] uppercase mb-1">{{ $album->paket }}</p>
                                        <h4 class="font-bold text-gray-900 truncate max-w-[150px]">{{ $album->name }}</h4>
                                        <p class="text-xs text-gray-400 font-mono mt-1">ID: {{ $album->album_id }}</p>
                                    </div>
                                    @if($is_exp) 
                                        <span class="bg-red-50 text-red-500 px-2 py-1 rounded-lg text-[10px] font-bold uppercase">Expired</span>
                                    @else 
                                        <span class="bg-emerald-50 text-emerald-600 px-2 py-1 rounded-lg text-[10px] font-bold uppercase dash-countdown" data-expire="{{ strtotime($album->expires_at) }}">Aktif</span> 
                                    @endif
                                </div>
                                <div class="flex gap-2 pt-1 mt-auto">
                                    <button onclick="copyLink('{{ $album->album_id }}')" class="flex-[2] bg-[#fbdc00] text-gray-900 py-2.5 rounded-xl text-xs font-bold btn-touch flex justify-center gap-2"><i data-lucide="copy" size="14"></i> Link Web</button>
                                    <button onclick="openEdit('{{ $album->album_id }}')" class="flex-1 bg-gray-100 text-gray-600 py-2.5 rounded-xl text-xs font-bold btn-touch hover:bg-gray-200"><i data-lucide="edit-3" size="14" class="mx-auto"></i></button>
                                    <button onclick="deleteAlbum('{{ $album->album_id }}')" class="px-3 bg-red-50 text-red-500 rounded-xl hover:bg-red-500 hover:text-white transition-colors btn-touch"><i data-lucide="trash-2" size="16"></i></button>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @endif
                </div>
                @endif
            </main>

            <!-- Modal Edit -->
            <div id="edit-panel" class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
                <div class="bg-white rounded-3xl w-full max-w-md shadow-2xl p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="font-bold text-xl">Edit Data</h3>
                        <button onclick="closeEdit()" class="text-gray-400"><i data-lucide="x"></i></button>
                    </div>
                    <form id="editForm" class="space-y-4">
                        <input type="hidden" id="edit_id">
                        <div><label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Paket</label><select id="edit_paket" class="w-full bg-gray-50 p-3 rounded-xl border outline-none"><option value="Self Photo">Self Photo</option><option value="Photobox">Photobox</option><option value="Pas Photo">Pas Photo</option></select></div>
                        <div><label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Nama Klien</label><input type="text" id="edit_name" required class="w-full bg-gray-50 p-3 rounded-xl border outline-none"></div>
                        <div><label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Link Google Drive Baru</label><input type="url" id="edit_drive_link" required class="w-full bg-gray-50 p-3 rounded-xl border outline-none"></div>
                        <div><label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Pindah ke Folder Dashboard</label><input type="text" id="edit_group_name" class="w-full bg-gray-50 p-3 rounded-xl border outline-none" placeholder="Kosongkan jika tak ingin dikelompokkan..."></div>
                        <button type="submit" class="w-full bg-[#355faa] text-white py-3 rounded-xl font-bold btn-touch text-sm mt-4">Simpan</button>
                    </form>
                </div>
            </div>

            <button onclick="toggleCreate()" class="md:hidden fixed bottom-6 right-6 w-14 h-14 bg-[#fbdc00] text-gray-900 rounded-full shadow-glow flex items-center justify-center z-40"><i data-lucide="plus" size="28"></i></button>
        </div>
    </div>

    @elseif ($mode === 'customer_view')
    <!-- ========================================================== -->
    <!-- 2. HALAMAN CUSTOMER (G-DRIVE API CLIENT SIDE GALLERY)      -->
    <!-- ========================================================== -->
    @php $paket = $current_album['paket'] ?? 'Self Photo'; @endphp
    <div class="flex-1 bg-[#f9fafb] flex flex-col h-screen overflow-hidden relative">
        
        <div id="gifSelectionHeader" class="hidden fixed top-0 left-0 right-0 bg-[#fbdc00] text-gray-900 px-5 py-4 z-[60] shadow-lg flex items-center justify-between">
            <div class="flex items-center gap-3">
                <i data-lucide="mouse-pointer-click" size="16" class="text-[#355faa]"></i>
                <span class="text-xs md:text-sm font-bold">Pilih foto untuk GIF</span>
            </div>
            <button onclick="toggleGifMode()" class="p-2 bg-black/5 rounded-full"><i data-lucide="x" size="18"></i></button>
        </div>

        <header class="bg-white/90 backdrop-blur-md px-5 py-4 flex justify-between items-center border-b border-gray-200 shrink-0 z-20 absolute top-0 w-full">
            <div class="overflow-hidden">
                <p class="text-[10px] font-bold text-[#355faa] uppercase tracking-widest mb-0.5">{{ $paket }}</p>
                <h1 class="text-gray-900 font-bold text-lg truncate max-w-[200px]">{{ $current_album['name'] ?? '' }}</h1>
            </div>
            <div class="w-10 h-10 bg-blue-50 rounded-full flex items-center justify-center text-[#355faa]">
                <i data-lucide="image" size="18"></i>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto pt-20 pb-32 px-4 md:px-8 mt-4 custom-scrollbar bg-[#f9fafb]">
            <div class="mb-8 mt-2 bg-[#355faa] text-white rounded-2xl p-5 flex items-center gap-4">
                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center"><i data-lucide="clock" size="24" class="text-[#fbdc00]"></i></div>
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-white/80 mb-1">Akses Galeri Berakhir Dalam</p>
                    <p class="text-lg md:text-2xl font-black" id="timer" data-expire="{{ $current_album['expires_at'] ?? 0 }}">Menghitung...</p>
                </div>
            </div>

            <div id="loadingGallery" class="flex flex-col items-center justify-center py-20 text-gray-400">
                <i data-lucide="loader-2" class="animate-spin mb-4" size="40"></i>
                <p class="text-sm font-bold animate-pulse">Menarik foto dari Google Drive...</p>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-3 hidden" id="galleryGrid"></div>
            
            <div class="mt-10 text-center px-6 pb-12"><p class="text-gray-400 text-xs font-bold uppercase tracking-[0.3em]">Snap Fun Studio</p></div>
        </main>

        <div class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 pt-4 pb-6 px-6 z-30 shadow-[0_-5px_20px_rgba(0,0,0,0.05)]">
            <div id="normalActions" class="flex gap-3 hidden">
                <button onclick="toggleGifMode()" class="flex-1 bg-white border border-gray-200 text-gray-700 h-14 rounded-2xl font-bold text-[10px] md:text-xs uppercase shadow-sm flex items-center justify-center gap-2 btn-touch">
                    <i data-lucide="film" size="18"></i> Buat GIF
                </button>
                <button onclick="downloadAll()" class="flex-[2] bg-[#fbdc00] text-gray-900 h-14 rounded-2xl font-bold text-xs md:text-sm uppercase shadow-lg btn-touch flex items-center justify-center gap-2">
                    <i data-lucide="download-cloud" size="20"></i> Simpan Semua
                </button>
            </div>
            
            <div id="gifActions" class="hidden flex gap-3">
                <button onclick="toggleGifMode()" class="flex-1 bg-gray-100 text-gray-600 h-14 rounded-2xl font-bold text-xs uppercase btn-touch">Batal</button>
                <button onclick="generateGIF()" class="flex-[2] bg-[#355faa] text-white h-14 rounded-2xl font-bold text-xs uppercase shadow-lg btn-touch">
                    Proses GIF (<span id="selectedCount">0</span>)
                </button>
            </div>
        </div>

        <div id="downloadModal" class="fixed inset-0 bg-black/80 backdrop-blur-sm z-50 hidden flex items-center justify-center p-6">
            <div class="bg-white p-8 rounded-[2rem] w-full max-w-sm text-center">
                <div id="fbSpinner" class="animate-spin rounded-full h-16 w-16 border-4 border-gray-200 border-t-[#355faa] mx-auto mb-6 mt-4"></div>
                <h3 class="font-bold text-xl text-gray-900 mb-2">Tunggu Sebentar...</h3>
                <p id="dlProgressText" class="text-sm text-gray-500 font-medium">Sabar ya pinpin lagi nyiapin foto kamu...</p>
            </div>
        </div>

        <div id="gifModal" class="fixed inset-0 bg-black/80 backdrop-blur-sm z-50 hidden flex items-center justify-center p-6">
            <div class="bg-white p-6 rounded-[2rem] w-full max-w-sm">
                <div class="flex justify-between items-center mb-4"><h3 class="font-bold text-lg">GIF Anda Siap!</h3><button onclick="closeGifModal()"><i data-lucide="x"></i></button></div>
                <div class="aspect-square bg-gray-100 rounded-xl overflow-hidden mb-4 border flex items-center justify-center relative">
                    <img id="gifResultImage" class="w-full h-full object-contain">
                    <div id="gifLoading" class="absolute inset-0 bg-white/80 flex flex-col items-center justify-center"><i data-lucide="loader-2" class="animate-spin text-[#355faa] mb-2" size="40"></i><p class="text-[10px] font-bold text-[#355faa]">Memproses...</p></div>
                </div>
                <a id="gifDownloadLink" href="#" download="SnapFun_GIF.gif" class="w-full bg-[#355faa] text-white py-3 rounded-xl font-bold flex items-center justify-center gap-2 hidden">
                    <i data-lucide="download" size="18"></i> Unduh GIF
                </a>
            </div>
        </div>
    </div>

    @else
    <!-- ========================================================== -->
    <!-- 3. HALAMAN ERROR / EXPIRED                                 -->
    <!-- ========================================================== -->
    <div class="flex-1 flex flex-col items-center justify-center p-8 text-center bg-[#f9fafb] h-screen">
        <div class="w-24 h-24 bg-red-50 text-red-500 rounded-3xl flex items-center justify-center mb-6"><i data-lucide="clock-4" size="48"></i></div>
        <h2 class="text-2xl font-bold mb-3 text-gray-900">Link Telah Kedaluwarsa</h2>
        <p class="text-gray-500 text-sm">Batas waktu akses galeri proyek ini telah habis.</p>
    </div>
    @endif

    <!-- ========================================================== -->
    <!-- SCRIPTS CORE                                               -->
    <!-- ========================================================== -->
    <script>
        lucide.createIcons();
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const apiUrl = "{{ route('photo-link.api.action') }}";

        @if ($mode === 'admin_dashboard')
        // JS ADMIN DASHBOARD
        function toggleCreate() { document.getElementById('create-panel').classList.toggle('hidden'); }

        document.querySelectorAll('.btn-toggle-folder').forEach(btn => {
            btn.addEventListener('click', () => {
                const targetId = btn.getAttribute('data-target');
                const target = document.getElementById(targetId);
                const icon = btn.querySelector('i[data-lucide="chevron-down"]');
                if (target.classList.contains('hidden')) {
                    target.classList.remove('hidden');
                    icon.style.transform = 'rotate(180deg)';
                } else {
                    target.classList.add('hidden');
                    icon.style.transform = 'rotate(0deg)';
                }
            });
        });

        const formIsBatch = document.getElementById('formIsBatch');
        const nameContainer = document.getElementById('nameContainer');
        const formName = document.getElementById('formName');
        if(formIsBatch) {
            formIsBatch.addEventListener('change', function() {
                if (this.checked) {
                    nameContainer.classList.add('hidden');
                    formName.required = false; 
                } else {
                    nameContainer.classList.remove('hidden');
                    formName.required = true;
                }
            });
        }

        const createForm = document.getElementById('createForm');
        if(createForm) {
            createForm.onsubmit = async function(e) {
                e.preventDefault();
                const btn = document.getElementById('btnSubmitCreate');
                const isBatchMode = document.getElementById('formIsBatch').checked;
                btn.innerHTML = 'Memproses...'; btn.disabled = true;
                
                const fd = new FormData();
                fd.append('_token', csrfToken);
                fd.append('action', isBatchMode ? 'create_album_batch' : 'create_album'); 
                if(!isBatchMode) fd.append('name', document.getElementById('formName').value); 
                fd.append('paket', document.getElementById('formPaket').value); 
                fd.append('drive_link', document.getElementById('formDriveLink').value); 
                fd.append('group_name', document.getElementById('formGroupName').value);
                fd.append('hours', document.getElementById('formHours').value);
                
                try {
                    const res = await fetch(apiUrl, { method: 'POST', body: fd });
                    const data = await res.json();
                    if(data.success) { 
                        if (isBatchMode) alert(`Sukses! ${data.count} Link berhasil dibuat.`);
                        window.location.reload(); 
                    } else { 
                        alert(data.message); btn.innerHTML = 'Coba Lagi'; btn.disabled = false; 
                    }
                } catch(err) {
                    alert('Terjadi kesalahan jaringan.');
                    btn.innerHTML = 'Coba Lagi'; btn.disabled = false; 
                }
            };
        }

        async function deleteAlbum(id) {
            if(!confirm('Hapus link proyek ini?')) return;
            const fd = new FormData(); fd.append('_token', csrfToken); fd.append('action', 'delete_album'); fd.append('id', id);
            await fetch(apiUrl, { method:'POST', body:fd }); window.location.reload();
        }

        function copyLink(id) {
            navigator.clipboard.writeText(window.location.origin + "/photo-link/album/" + id);
            alert('Link Tersalin! Berikan ke pelanggan.');
        }

        document.querySelectorAll('.dash-countdown').forEach(el => {
            const upd = () => {
                const diff = parseInt(el.dataset.expire) * 1000 - new Date().getTime();
                if(diff < 0) { el.innerText = 'Expired'; return; }
                el.innerText = `${Math.floor(diff / 86400000)} Hr ${Math.floor((diff % 86400000) / 3600000)} Jm`;
            }; upd(); setInterval(upd, 60000); 
        });

        async function openEdit(id) {
            document.getElementById('edit-panel').classList.remove('hidden');
            document.getElementById('edit_id').value = id;
            const fd = new FormData(); fd.append('_token', csrfToken); fd.append('action', 'get_album'); fd.append('id', id);
            const data = await (await fetch(apiUrl, { method: 'POST', body: fd })).json();
            if(data.success) { 
                document.getElementById('edit_name').value = data.album.name; 
                document.getElementById('edit_paket').value = data.album.paket; 
                document.getElementById('edit_drive_link').value = data.album.drive_link; 
                document.getElementById('edit_group_name').value = data.album.group_name || ''; 
            }
        }
        function closeEdit() { document.getElementById('edit-panel').classList.add('hidden'); }
        document.getElementById('editForm')?.addEventListener('submit', async(e) => {
            e.preventDefault();
            const fd = new FormData(); 
            fd.append('_token', csrfToken);
            fd.append('action', 'update_album'); 
            fd.append('id', document.getElementById('edit_id').value); 
            fd.append('name', document.getElementById('edit_name').value); 
            fd.append('paket', document.getElementById('edit_paket').value); 
            fd.append('drive_link', document.getElementById('edit_drive_link').value);
            fd.append('group_name', document.getElementById('edit_group_name').value);
            const res = await (await fetch(apiUrl, { method: 'POST', body: fd })).json();
            if(res.success) window.location.reload(); else alert(res.message);
        });
        @endif

        @if ($mode === 'customer_view')
        // JS CUSTOMER VIEW
        const albumData = @json($current_album ?? []);
        const API_KEY = "{{ env('GDRIVE_API_KEY') }}";
        const FOLDER_ID = albumData.folder_id;
        let drivePhotos = [];

        const timerEl = document.getElementById('timer');
        if(timerEl) {
            const upd = () => {
                const diff = parseInt(timerEl.dataset.expire) * 1000 - new Date().getTime();
                if(diff < 0) { window.location.reload(); return; }
                const d = Math.floor(diff / 86400000), h = Math.floor((diff % 86400000) / 3600000), m = Math.floor((diff % 3600000) / 60000);
                timerEl.innerText = `${d>0?d+'H ':''}${String(h).padStart(2,'0')}j ${String(m).padStart(2,'0')}m`;
            }; upd(); setInterval(upd, 60000);
        }

        async function fetchDriveGallery() {
            try {
                const query = `https://www.googleapis.com/drive/v3/files?q='${FOLDER_ID}'+in+parents+and+mimeType+contains+'image/'&key=${API_KEY}&fields=files(id,name,thumbnailLink)&pageSize=100`;
                const response = await fetch(query);
                const data = await response.json();

                if (data.error) throw new Error(data.error.message);
                if (data.files && data.files.length > 0) {
                    drivePhotos = data.files.map((f, i) => {
                        const rawThumb = f.thumbnailLink || '';
                        const ext = f.name.split('.').pop() || 'jpg';
                        return {
                            id: f.id,
                            name: f.name,
                            dlName: `SnapFun_${albumData.paket}_${albumData.name}_${i+1}.${ext}`,
                            displayUrl: rawThumb.replace(/=s\d+/, '=s800'),
                            downloadUrl: `https://www.googleapis.com/drive/v3/files/${f.id}?alt=media&key=${API_KEY}`
                        };
                    });
                    renderGallery();
                } else {
                    throw new Error("Folder kosong atau aksesnya tertutup.");
                }
            } catch (error) {
                document.getElementById('loadingGallery').innerHTML = `<i data-lucide="alert-circle" class="text-red-500 mb-2" size="40"></i><p class="text-sm text-red-500 text-center px-4">${error.message}</p>`;
                lucide.createIcons();
            }
        }

        function renderGallery() {
            const grid = document.getElementById('galleryGrid');
            grid.innerHTML = '';
            drivePhotos.forEach((photo) => {
                grid.innerHTML += `
                <div class="photo-item relative aspect-[4/5] bg-white rounded-xl overflow-hidden group shadow-sm border border-gray-100">
                    <img src="${photo.displayUrl}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110" loading="lazy" crossorigin="anonymous">
                    <div class="normal-overlay absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-end p-3">
                        <button onclick="downloadSingle('${photo.downloadUrl}', '${photo.dlName}')" class="w-full bg-white text-[#355faa] py-2 rounded-lg text-[10px] font-bold uppercase tracking-widest hover:bg-gray-50 shadow-lg btn-touch">Unduh</button>
                    </div>
                    <label class="selection-overlay absolute inset-0 bg-white/0 hidden cursor-pointer">
                        <input type="checkbox" class="gif-checkbox hidden" value="${photo.downloadUrl}">
                        <div class="absolute inset-0 border-4 border-transparent transition-all flex items-start justify-end p-2">
                            <div class="check-indicator w-6 h-6 bg-[#355faa] rounded-full text-white flex items-center justify-center opacity-0 transform scale-50 transition-all"><i data-lucide="check" size="14"></i></div>
                        </div>
                    </label>
                </div>`;
            });
            document.getElementById('loadingGallery').classList.add('hidden');
            grid.classList.remove('hidden');
            document.getElementById('normalActions').classList.remove('hidden');
            lucide.createIcons();
            document.querySelectorAll('.gif-checkbox').forEach(cb => { cb.addEventListener('change', () => { document.getElementById('selectedCount').innerText = document.querySelectorAll('.gif-checkbox:checked').length; }); });
        }

        fetchDriveGallery();

        async function downloadAll() {
            const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent) || (navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1);
            const modal = document.getElementById('downloadModal');
            const dlText = document.getElementById('dlProgressText');
            modal.classList.remove('hidden');
            try {
                if (isIOS) {
                    dlText.innerText = "Menyiapkan file ZIP khusus iPhone/iPad...";
                    const zip = new JSZip();
                    for (let i = 0; i < drivePhotos.length; i++) {
                        dlText.innerText = `Menarik foto ${i+1} dari ${drivePhotos.length}...`;
                        const res = await fetch(drivePhotos[i].downloadUrl);
                        const blob = await res.blob();
                        zip.file(drivePhotos[i].dlName, blob);
                    }
                    dlText.innerText = `Membuat file ZIP (Bisa memakan waktu)...`;
                    const zipContent = await zip.generateAsync({ type: "blob" });
                    saveAs(zipContent, `SnapFun_${albumData.name}.zip`);
                } else {
                    for (let i = 0; i < drivePhotos.length; i++) {
                        dlText.innerText = `Mengunduh foto ${i+1} dari ${drivePhotos.length}...`;
                        const res = await fetch(drivePhotos[i].downloadUrl);
                        const blob = await res.blob();
                        saveAs(blob, drivePhotos[i].dlName); 
                        await new Promise(r => setTimeout(r, 600)); 
                    }
                }
                dlText.innerText = `Selesai!`;
                setTimeout(() => modal.classList.add('hidden'), 1500);
            } catch(e) {
                dlText.innerText = "Gagal mengunduh foto. Cek koneksi Anda.";
                setTimeout(()=> modal.classList.add('hidden'), 3000);
            }
        }

        async function downloadSingle(url, filename) {
            try {
                const res = await fetch(url);
                const blob = await res.blob();
                saveAs(blob, filename); 
            } catch(e) { alert("Gagal mengunduh foto."); }
        }

        let gifMode = false;
        function toggleGifMode() {
            gifMode = !gifMode;
            document.getElementById('normalActions').classList.toggle('hidden');
            document.getElementById('gifActions').classList.toggle('hidden');
            document.getElementById('gifSelectionHeader').classList.toggle('hidden');
            document.querySelectorAll('.selection-overlay').forEach(el => el.classList.toggle('hidden'));
            document.querySelectorAll('.normal-overlay').forEach(el => el.classList.toggle('hidden'));
            if(!gifMode) document.querySelectorAll('.gif-checkbox').forEach(cb => cb.checked = false);
            document.getElementById('selectedCount').innerText = '0';
        }

        function generateGIF() {
            const selected = Array.from(document.querySelectorAll('.gif-checkbox:checked')).map(el => el.value);
            if(selected.length < 2) return alert("Pilih minimal 2 foto!");
            const modal = document.getElementById('gifModal');
            const img = document.getElementById('gifResultImage');
            const loading = document.getElementById('gifLoading');
            const btn = document.getElementById('gifDownloadLink');
            modal.classList.remove('hidden'); img.src=''; btn.classList.add('hidden'); loading.classList.remove('hidden');

            const tempImg = new Image();
            tempImg.crossOrigin = "Anonymous"; 
            tempImg.src = selected[0];
            tempImg.onload = function() {
                const ratio = tempImg.naturalWidth / tempImg.naturalHeight;
                gifshot.createGIF({
                    images: selected,
                    gifWidth: 600,
                    gifHeight: Math.round(600 / ratio),
                    interval: 0.5,
                    crossOrigin: 'Anonymous'
                }, function(obj) {
                    if(!obj.error) {
                        img.src = obj.image;
                        loading.classList.add('hidden');
                        btn.href = obj.image;
                        btn.classList.remove('hidden');
                    } else {
                        alert("Gagal membuat GIF."); closeGifModal();
                    }
                });
            }
        }
        function closeGifModal() { document.getElementById('gifModal').classList.add('hidden'); }
        @endif
    </script>
</body>
</html>