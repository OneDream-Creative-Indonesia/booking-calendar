<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Snap Link - Link Photo Snap Fun</title>
    <link rel="icon" href="{{ asset('snaplink.png') }}?v={{ time() }}">
    <!-- CSRF TOKEN UNTUK AJAX LARAVEL -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
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
        .dot-grid {
            background-image: radial-gradient(#d1d5db 1px, transparent 1px);
            background-size: 20px 20px;
        }
        .btn-touch { transition: transform 0.1s; }
        .btn-touch:active { transform: scale(0.96); }
        .shadow-glow { box-shadow: 0 10px 40px -10px rgba(53, 95, 170, 0.2); }
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        .gif-checkbox:checked + div {
            border-color: var(--primary);
            background-color: rgba(53, 95, 170, 0.1);
        }
        .gif-checkbox:checked + div .check-indicator {
            opacity: 1;
            transform: scale(1);
        }
        .anim-fallback { display: none; }
        img.anim-error { display: none !important; }
        img.anim-error + .anim-fallback { display: block; }
    </style>
</head>
<body class="min-h-screen flex flex-col">

    @if ($mode === 'admin_dashboard')
    <!-- 1. DASHBOARD ADMIN -->
    <div class="flex-1 bg-[#f3f4f6] dot-grid flex flex-col h-screen overflow-hidden relative">
        
        <!-- Header Utama (Menggantikan Sidebar) -->
        <header class="bg-white border-b border-gray-200 px-4 md:px-8 py-4 flex justify-between items-center z-20 shadow-sm shrink-0 w-full">
            <div class="flex items-center gap-3">
                <img src="{{ asset('img//logo/Logo Snapfun-01.svg') }}" alt="" style="height:60px;">
            </div>
            
            <!-- Tombol Kembali ke Dashboard Utama -->
            <a href="{{ url('/admin') }}" class="flex items-center gap-2 px-4 md:px-5 py-2.5 bg-white border border-gray-200 text-gray-600 rounded-xl hover:bg-gray-50 hover:text-gray-900 hover:border-gray-300 font-bold text-xs md:text-sm transition-all shadow-sm btn-touch">
                <i data-lucide="arrow-left" size="18"></i> 
                <span class="hidden sm:inline">Kembali ke Dashboard</span>
            </a>
        </header>

        <!-- Area Konten Utama -->
        <main class="flex-1 overflow-y-auto p-4 md:p-8 lg:p-10 custom-scrollbar relative w-full mx-auto max-w-[1400px]">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6 mb-8">
                <div class="bg-white p-5 md:p-6 rounded-2xl border border-gray-200 shadow-sm">
                    <p class="text-[10px] md:text-xs text-gray-400 font-bold uppercase mb-2 tracking-wider">Link Aktif</p>
                    <p class="text-3xl md:text-4xl font-black text-[#355faa]">{{ $active_links ?? 0 }}</p>
                </div>
                <div class="bg-white p-5 md:p-6 rounded-2xl border border-gray-200 shadow-sm">
                    <p class="text-[10px] md:text-xs text-gray-400 font-bold uppercase mb-2 tracking-wider">Total File</p>
                    <p class="text-3xl md:text-4xl font-black text-gray-800">{{ $total_files ?? 0 }}</p>
                </div>
                <div class="bg-white p-5 md:p-6 rounded-2xl border border-gray-200 shadow-sm md:col-span-2 flex items-center justify-between">
                    <div>
                        <p class="text-[10px] md:text-xs text-gray-400 font-bold uppercase mb-2 tracking-wider">Penyimpanan Terpakai</p>
                        <p class="text-xl md:text-2xl font-black text-amber-500 flex items-center gap-2"><i data-lucide="hard-drive" size="24"></i> {{ $total_storage ?? '0 B' }}</p>
                    </div>
                    <button onclick="toggleCreate()" class="hidden md:flex bg-[#355faa] text-white px-8 py-3.5 rounded-xl font-bold text-sm uppercase tracking-wider shadow-lg shadow-blue-900/20 hover:bg-[#2d5191] transition-colors items-center gap-2 btn-touch">
                        <i data-lucide="plus" size="18"></i> Buat Proyek Baru
                    </button>
                </div>
            </div>

            <!-- Form Buat Proyek Baru -->
            <div id="create-panel" class="hidden bg-white p-6 md:p-8 rounded-[2rem] shadow-xl border border-gray-200 mb-8 animate-in slide-in-from-top-4 max-w-4xl mx-auto relative z-30">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="font-bold text-xl md:text-2xl text-gray-900">Buat Proyek Baru</h3>
                    <button onclick="toggleCreate()" class="text-gray-400 hover:text-gray-900 bg-gray-50 hover:bg-gray-100 p-2 rounded-full transition-colors"><i data-lucide="x"></i></button>
                </div>
                <form id="createForm" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 md:gap-6">
                        <div class="md:col-span-1">
                            <label class="block text-[10px] font-bold text-gray-500 uppercase mb-2 tracking-wider">Paket</label>
                            <select id="formPaket" class="w-full bg-gray-50 p-4 rounded-xl border border-gray-200 outline-none cursor-pointer focus:border-[#355faa] focus:ring-2 focus:ring-blue-100 transition-all font-medium">
                                <option value="Self Photo">Self Photo</option>
                                <option value="Photobox">Photobox</option>
                                <option value="Pas Photo">Pas Photo</option>
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-[10px] font-bold text-gray-500 uppercase mb-2 tracking-wider">Nama Klien</label>
                            <input type="text" id="formName" required class="w-full bg-gray-50 p-4 rounded-xl border border-gray-200 outline-none focus:border-[#355faa] focus:ring-2 focus:ring-blue-100 transition-all font-medium" placeholder="Cth: Sesi Budi & Siska">
                        </div>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-gray-500 uppercase mb-2 tracking-wider">Durasi Akses</label>
                        <select id="formHours" class="w-full bg-gray-50 p-4 rounded-xl border border-gray-200 outline-none cursor-pointer focus:border-[#355faa] focus:ring-2 focus:ring-blue-100 transition-all font-medium">
                            <option value="168" selected>1 Minggu (168 Jam)</option>
                            <option value="336">2 Minggu (336 Jam)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-gray-500 uppercase mb-2 tracking-wider">Foto</label>
                        <div onclick="document.getElementById('fileInput').click()" class="border-2 border-dashed border-gray-200 p-10 md:p-12 rounded-2xl text-center cursor-pointer hover:border-[#355faa] hover:bg-blue-50 transition-colors group">
                            <div class="w-16 h-16 bg-gray-50 group-hover:bg-white rounded-full flex items-center justify-center mx-auto mb-4 shadow-sm transition-colors">
                                <i data-lucide="image-plus" class="text-gray-400 group-hover:text-[#355faa] transition-colors" size="28"></i>
                            </div>
                            <p class="text-sm font-bold text-gray-600">Klik area ini untuk memilih foto</p>
                            <p class="text-xs text-gray-400 mt-1 font-medium">Bisa memilih lebih dari satu foto sekaligus</p>
                            <input type="file" id="fileInput" multiple hidden accept="image/*">
                        </div>
                        <div id="fileCount" class="text-center text-xs font-bold text-[#355faa] mt-3"></div>
                    </div>
                    <button type="submit" class="w-full bg-[#355faa] text-white py-4 md:py-5 rounded-xl font-bold shadow-lg shadow-blue-900/20 btn-touch text-sm md:text-base uppercase tracking-widest mt-4 hover:bg-[#2d5191] transition-colors">Terbitkan Proyek Sekarang</button>
                </form>
            </div>

            <!-- Riwayat Proyek -->
            <div class="space-y-4 pb-20">
                <h3 class="font-bold text-gray-800 text-sm uppercase tracking-widest ml-1 mb-4 md:mb-6">Riwayat Proyek</h3>
                @if (empty($db_all))
                    <div class="text-center py-20 md:py-32 bg-white rounded-3xl border border-dashed border-gray-200">
                        <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i data-lucide="folder-open" class="text-gray-300" size="32"></i>
                        </div>
                        <p class="text-gray-400 text-sm font-bold">Belum ada proyek yang dibuat.</p>
                        <button onclick="toggleCreate()" class="mt-4 text-[#355faa] font-bold text-xs uppercase tracking-wider hover:underline">Buat Proyek Pertama Anda</button>
                    </div>
                @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 md:gap-5" id="projectsGrid">
                        @foreach (array_reverse($db_all, true) as $id => $album)
                        @php 
                            $is_exp = time() > $album['expires_at']; 
                            $paket_name = $album['paket'] ?? 'Reguler';
                        @endphp
                        <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm hover:shadow-md transition-shadow flex flex-col gap-3 group">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="text-[10px] font-black text-[#355faa] uppercase tracking-widest mb-1">{{ $paket_name }}</p>
                                    <h4 class="font-bold text-gray-900 truncate max-w-[180px] leading-tight text-lg">{{ $album['name'] }}</h4>
                                    <p class="text-xs text-gray-400 font-mono mt-1">ID: {{ $id }}</p>
                                </div>
                                @if($is_exp)
                                    <span class="bg-red-50 text-red-500 px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase shrink-0">Expired</span>
                                @else
                                    <span class="bg-emerald-50 text-emerald-600 px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase shrink-0 dash-countdown" data-expire="{{ $album['expires_at'] }}">Aktif</span>
                                @endif
                            </div>
                            
                            <div class="flex items-center gap-4 text-xs text-gray-500 bg-gray-50 p-3.5 rounded-xl mt-auto border border-gray-100">
                                <div class="flex items-center gap-1.5 font-medium"><i data-lucide="image" size="14" class="text-gray-400"></i> {{ count($album['photos']) }} Foto</div>
                                <div class="w-1 h-1 bg-gray-300 rounded-full"></div>
                                <div class="flex items-center gap-1.5 font-medium"><i data-lucide="clock" size="14" class="text-gray-400"></i> {{ date('d M Y', $album['created_at']) }}</div>
                            </div>

                            <div class="flex gap-2 pt-2">
                                <button onclick="copyLink('{{ $id }}')" class="flex-[2] bg-[#fbdc00] text-gray-900 py-3 rounded-xl text-xs font-bold btn-touch flex items-center justify-center gap-2 hover:bg-[#e5c900] transition-colors">
                                    <i data-lucide="link" size="14"></i> Salin Link
                                </button>
                                <button onclick="openEdit('{{ $id }}')" class="flex-1 bg-gray-100 text-gray-600 py-3 rounded-xl text-xs font-bold btn-touch flex items-center justify-center hover:bg-gray-200 transition-colors" title="Edit Proyek">
                                    <i data-lucide="edit-3" size="16"></i>
                                </button>
                                <button onclick="deleteAlbum('{{ $id }}')" class="px-4 bg-red-50 text-red-500 rounded-xl hover:bg-red-500 hover:text-white transition-colors btn-touch" title="Hapus Permanen">
                                    <i data-lucide="trash-2" size="16"></i>
                                </button>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </main>

        <div id="edit-panel" class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-3xl w-full max-w-4xl max-h-[90vh] flex flex-col shadow-2xl animate-in zoom-in duration-300">
                <div class="p-6 border-b border-gray-100 flex justify-between items-center shrink-0">
                    <div>
                        <h3 class="font-bold text-xl md:text-2xl">Edit Proyek</h3>
                        <p class="text-xs text-gray-500 font-mono mt-1" id="edit_project_title">ID: Memuat...</p>
                    </div>
                    <button onclick="closeEdit()" class="text-gray-400 hover:text-gray-900 bg-gray-50 hover:bg-gray-100 p-2.5 rounded-full transition-colors"><i data-lucide="x" size="20"></i></button>
                </div>
                
                <div class="p-6 md:p-8 overflow-y-auto custom-scrollbar flex-1 bg-gray-50">
                    <input type="hidden" id="edit_project_id">
                    
                    <div class="mb-6 flex flex-col md:flex-row justify-between items-start md:items-end gap-4">
                        <h4 class="font-bold text-sm uppercase tracking-widest text-gray-600">Daftar Foto Tersimpan</h4>
                        <form id="editAddForm" class="m-0 w-full md:w-auto">
                            <input type="file" id="editFileInput" multiple hidden accept="image/*">
                            <button type="button" onclick="document.getElementById('editFileInput').click()" class="w-full md:w-auto bg-[#355faa] text-white px-5 py-2.5 rounded-xl text-xs font-bold flex items-center justify-center gap-2 hover:bg-[#2d5191] transition-colors btn-touch shadow-lg shadow-blue-900/20">
                                <i data-lucide="plus" size="14"></i> Tambah Foto Baru
                            </button>
                        </form>
                    </div>
                    
                    <div id="editPhotoGrid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                        <p class="col-span-full text-center text-sm text-gray-400 py-10">Memuat foto...</p>
                    </div>
                </div>
            </div>
        </div>

        <div id="toastContainer" class="fixed bottom-6 right-6 z-[60] flex flex-col gap-3"></div>

        <!-- Tombol Tambah Mengambang (Hanya untuk Mobile) -->
        <button onclick="toggleCreate()" class="md:hidden fixed bottom-6 right-6 w-14 h-14 bg-[#fbdc00] text-gray-900 rounded-full shadow-glow flex items-center justify-center btn-touch z-40 border-2 border-white hover:scale-105 transition-transform">
            <i data-lucide="plus" size="28" class="stroke-[3px]"></i>
        </button>
    </div>

    @elseif ($mode === 'customer_view')
    <!-- 2. HALAMAN CUSTOMER -->
    @php $paket = $current_album['paket'] ?? 'Self Photo'; @endphp
    <div class="flex-1 bg-[#f9fafb] flex flex-col h-screen overflow-hidden relative">
        
        <div id="gifSelectionHeader" class="hidden fixed top-0 left-0 right-0 bg-[#fbdc00] text-gray-900 px-5 py-4 z-[60] shadow-lg flex items-center justify-between animate-in slide-in-from-top-2">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-[#355faa]/10 rounded-full flex items-center justify-center shrink-0">
                    <i data-lucide="mouse-pointer-click" size="16" class="text-[#355faa]"></i>
                </div>
                <span class="text-xs md:text-sm font-bold tracking-wide">Pilih foto untuk GIF</span>
            </div>
            <button onclick="toggleGifMode()" class="p-2 bg-black/5 rounded-full hover:bg-black/10 transition-all btn-touch shrink-0 text-gray-900">
                <i data-lucide="x" size="18"></i>
            </button>
        </div>

        <header class="bg-white/90 backdrop-blur-md px-5 py-4 flex justify-between items-center border-b border-gray-200 shrink-0 z-20 absolute top-0 w-full shadow-sm">
            <div class="overflow-hidden">
                <p class="text-[10px] font-bold text-[#355faa] uppercase tracking-widest mb-0.5">{{ $paket }}</p>
                <h1 class="text-gray-900 font-bold text-lg truncate max-w-[200px] leading-tight">{{ $current_album['name'] }}</h1>
            </div>
            <a href="{{ url('/admin') }}" class="flex items-center gap-2 px-3 md:px-4 py-2 bg-white border border-gray-200 text-gray-600 rounded-xl hover:bg-gray-50 hover:text-gray-900 font-bold text-xs transition-all shadow-sm btn-touch">
                <i data-lucide="arrow-left" size="16"></i> 
                <span class="hidden sm:inline">Kembali ke Dashboard</span>
            </a>
        </header>

        <main class="flex-1 overflow-y-auto pt-20 pb-32 px-4 md:px-8 mt-4 custom-scrollbar bg-[#f9fafb]">
            <div class="mb-8 mt-2 bg-[#355faa] text-white rounded-2xl p-5 shadow-lg shadow-blue-900/10 flex items-center gap-4">
                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center shrink-0">
                    <i data-lucide="clock" size="24" class="text-[#fbdc00]"></i>
                </div>
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-white/80 mb-1">Waktu mundur untuk link</p>
                    <p class="text-lg md:text-2xl font-black tracking-wide" id="timer" data-expire="{{ $current_album['expires_at'] }}">Menghitung...</p>
                </div>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-3" id="galleryGrid">
                @foreach ($current_album['photos'] as $idx => $photo)
                <div class="photo-item relative aspect-[4/5] bg-white rounded-xl overflow-hidden group shadow-sm border border-gray-100">
                    <img src="{{ $photo['url'] }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110" loading="lazy">
                    
                    @php 
                        $ext = pathinfo($photo['file'], PATHINFO_EXTENSION);
                        $custom_dl_name = "Snap Fun_" . $paket . "_" . $current_album['name'] . "_" . ($idx + 1) . "." . $ext;
                    @endphp
                    <div class="normal-overlay absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-end p-3">
                        <a href="{{ route('photo-link.download.file', ['file' => $photo['file']]) }}?dl_name={{ urlencode($custom_dl_name) }}" class="w-full bg-white text-[#355faa] py-2 rounded-lg text-[10px] font-bold text-center uppercase tracking-widest hover:bg-gray-50 shadow-lg btn-touch">
                            Unduh
                        </a>
                    </div>

                    <label class="selection-overlay absolute inset-0 bg-white/0 hidden cursor-pointer">
                        <input type="checkbox" class="gif-checkbox hidden" value="{{ $photo['url'] }}">
                        <div class="absolute inset-0 border-4 border-transparent transition-all flex items-start justify-end p-2">
                            <div class="check-indicator w-6 h-6 bg-[#355faa] rounded-full text-white flex items-center justify-center shadow-lg opacity-0 transform scale-50 transition-all">
                                <i data-lucide="check" size="14"></i>
                            </div>
                        </div>
                    </label>
                </div>
                @endforeach
            </div>
            
            <div class="mt-10 text-center px-6 pb-12">
                <p class="text-gray-400 text-xs font-bold uppercase tracking-[0.3em]">Snap Fun Studio</p>
            </div>
        </main>

        <div class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 pt-4 pb-6 px-6 z-30 shadow-[0_-5px_20px_rgba(0,0,0,0.05)]">
            <div id="normalActions" class="flex gap-3">
                <button onclick="toggleGifMode()" class="flex-1 bg-white border border-gray-200 text-gray-700 h-14 rounded-2xl font-bold text-[10px] md:text-xs uppercase tracking-widest shadow-sm hover:border-[#355faa] hover:text-[#355faa] btn-touch flex items-center justify-center gap-2 transition-all">
                    <i data-lucide="film" size="18"></i> Buat GIF
                </button>
                <button onclick="downloadAll()" class="flex-[2] bg-[#fbdc00] text-gray-900 h-14 rounded-2xl font-bold text-xs md:text-sm uppercase tracking-widest shadow-lg shadow-yellow-500/20 btn-touch flex items-center justify-center gap-2">
                    <i data-lucide="download-cloud" size="20"></i> Simpan Semua
                </button>
            </div>
            
            <div id="gifActions" class="hidden flex gap-3">
                <button onclick="toggleGifMode()" class="flex-1 bg-gray-100 text-gray-600 h-14 rounded-2xl font-bold text-xs uppercase tracking-widest btn-touch">Batal</button>
                <button onclick="generateGIF()" id="btnGenerateGif" class="flex-[2] bg-[#355faa] text-white h-14 rounded-2xl font-bold text-xs uppercase tracking-widest shadow-lg shadow-blue-900/20 btn-touch flex items-center justify-center gap-2">
                    Proses GIF (<span id="selectedCount">0</span>)
                </button>
            </div>
        </div>

        <div id="downloadModal" class="fixed inset-0 bg-black/80 backdrop-blur-sm z-50 hidden flex items-center justify-center p-6">
            <div class="bg-white p-8 rounded-[2rem] w-full max-w-sm shadow-2xl animate-in zoom-in duration-300 text-center relative">
                <div class="animate-spin rounded-full h-16 w-16 border-4 border-gray-200 border-t-[#355faa] mx-auto mb-6 mt-4"></div>
                <h3 class="font-bold text-xl text-gray-900 mb-2">Tunggu Sebentar...</h3>
                <p class="text-sm text-gray-500 font-medium leading-relaxed">Sabar ya pinpin lagi siapin file kamu buat di download</p>
            </div>
        </div>

        <div id="gifModal" class="fixed inset-0 bg-black/80 backdrop-blur-sm z-50 hidden flex items-center justify-center p-6">
            <div class="bg-white p-6 rounded-[2rem] w-full max-w-sm shadow-2xl animate-in zoom-in duration-300">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-bold text-lg text-gray-900">GIF Anda Siap!</h3>
                    <button onclick="closeGifModal()" class="text-gray-400 hover:text-gray-600"><i data-lucide="x"></i></button>
                </div>
                <div class="aspect-square bg-gray-100 rounded-xl overflow-hidden mb-4 border border-gray-200 flex items-center justify-center relative">
                    <img id="gifResultImage" class="w-full h-full object-contain">
                    <div id="gifLoading" class="hidden absolute inset-0 bg-white/80 flex flex-col items-center justify-center">
                        <div class="animate-spin rounded-full h-10 w-10 border-4 border-gray-200 border-t-[#355faa] mb-2"></div>
                        <p class="text-[10px] font-bold text-[#355faa] uppercase tracking-widest">Memproses...</p>
                    </div>
                </div>
                <a id="gifDownloadLink" href="#" download="SnapFun_GIF.gif" class="w-full bg-[#355faa] text-white py-3 rounded-xl font-bold flex items-center justify-center gap-2 mb-2 btn-touch">
                    <i data-lucide="download" size="18"></i> Unduh GIF
                </a>
            </div>
        </div>
    </div>

    @else
    <!-- 3. HALAMAN ERROR -->
    <div class="flex-1 flex flex-col items-center justify-center p-8 text-center bg-[#f9fafb]">
        <div class="w-20 h-20 bg-gray-200 text-gray-400 rounded-full flex items-center justify-center mb-6">
            <i data-lucide="alert-circle" size="40"></i>
        </div>
        <h2 class="text-2xl font-bold mb-2 text-gray-900">Galeri Tidak Tersedia</h2>
        <p class="text-gray-500 text-sm mb-8 leading-relaxed max-w-xs mx-auto">
            Proyek ini mungkin telah kedaluwarsa atau ID yang Anda masukkan salah.
        </p>
        <a href="{{ url('/') }}" class="px-8 py-3 bg-[#355faa] text-white rounded-xl font-bold text-sm shadow-lg shadow-blue-900/20">Kembali</a>
    </div>
    @endif

    <script>
        lucide.createIcons();

        // AMBIL CSRF TOKEN DARI META TAG
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const apiUrl = "{{ route('photo-link.api.action') }}";

        @if ($mode === 'admin_dashboard')
        
        let activeUploads = 0;
        window.addEventListener('beforeunload', function (e) {
            if (activeUploads > 0) {
                e.preventDefault();
                e.returnValue = 'Proses upload sedang berjalan. Jika Anda pindah halaman, upload akan gagal atau terputus.';
            }
        });

        function toggleCreate() {
            document.getElementById('create-panel').classList.toggle('hidden');
        }

        const fileInput = document.getElementById('fileInput');
        if(fileInput) {
            fileInput.onchange = function() {
                const count = this.files.length;
                document.getElementById('fileCount').innerText = count > 0 ? count + " Foto Dipilih" : "";
            };
        }

        const createForm = document.getElementById('createForm');
        if(createForm) {
            createForm.onsubmit = async function(e) {
                e.preventDefault();
                
                const filesArray = Array.from(document.getElementById('fileInput').files);
                if(filesArray.length === 0) { alert('Pilih foto terlebih dahulu!'); return; }
                
                const nameVal = document.getElementById('formName').value;
                const paketVal = document.getElementById('formPaket').value;
                const hoursVal = document.getElementById('formHours').value;
                
                toggleCreate();
                createForm.reset();
                document.getElementById('fileCount').innerText = "";
                
                activeUploads++;
                
                const toastId = 'toast_' + Date.now();
                const toastHtml = `
                    <div id="${toastId}" class="bg-white border border-gray-200 p-4 rounded-2xl shadow-2xl w-80 flex items-center gap-4 animate-in slide-in-from-bottom-5">
                        <div class="w-10 h-10 rounded-full border-4 border-gray-100 border-t-[#355faa] animate-spin flex-shrink-0 spinner"></div>
                        <div class="flex-1 min-w-0">
                            <p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1 truncate title">${nameVal}</p>
                            <div class="w-full bg-gray-100 rounded-full h-1.5 overflow-hidden mb-1">
                                <div class="h-full bg-[#355faa] transition-all duration-300 progress-bar" style="width: 0%"></div>
                            </div>
                            <p class="text-[10px] font-bold text-right text-[#355faa] progress-text">Memulai...</p>
                        </div>
                    </div>
                `;
                document.getElementById('toastContainer').insertAdjacentHTML('beforeend', toastHtml);
                
                const toastEl = document.getElementById(toastId);
                const pBar = toastEl.querySelector('.progress-bar');
                const pText = toastEl.querySelector('.progress-text');
                const spinner = toastEl.querySelector('.spinner');

                try {
                    const fdInit = new FormData();
                    fdInit.append('_token', csrfToken);
                    fdInit.append('action', 'init_album');
                    fdInit.append('name', nameVal);
                    fdInit.append('paket', paketVal);
                    fdInit.append('hours', hoursVal);
                    
                    const resInit = await fetch(apiUrl, { method: 'POST', body: fdInit });
                    const dataInit = await resInit.json();
                    
                    if(!dataInit.success) throw new Error("Gagal inisialisasi");
                    const albumId = dataInit.album_id;

                    for(let i = 0; i < filesArray.length; i++) {
                        pText.innerText = `${i+1} dari ${filesArray.length}`;
                        const percent = Math.round(((i) / filesArray.length) * 100);
                        pBar.style.width = percent + "%";

                        const fdUpload = new FormData();
                        fdUpload.append('_token', csrfToken);
                        fdUpload.append('action', 'upload_single');
                        fdUpload.append('album_id', albumId);
                        fdUpload.append('photo', filesArray[i]);

                        await fetch(apiUrl, { method: 'POST', body: fdUpload });
                    }

                    pBar.style.width = "100%";
                    pText.innerText = "Selesai!";
                    pText.classList.replace('text-[#355faa]', 'text-emerald-500');
                    pBar.classList.replace('bg-[#355faa]', 'bg-emerald-500');
                    spinner.classList.remove('animate-spin');
                    spinner.classList.add('bg-emerald-500', 'border-none');
                    
                    setTimeout(() => {
                        toastEl.remove();
                        activeUploads--;
                        if (activeUploads === 0) window.location.reload(); 
                    }, 2500);

                } catch (error) {
                    pText.innerText = "Gagal!";
                    pText.classList.replace('text-[#355faa]', 'text-red-500');
                    pBar.classList.replace('bg-[#355faa]', 'bg-red-500');
                    spinner.classList.remove('animate-spin');
                    spinner.classList.add('bg-red-500', 'border-none');
                    
                    setTimeout(() => {
                        toastEl.remove();
                        activeUploads--;
                    }, 5000);
                }
            };
        }

        async function deleteAlbum(id) {
            if(!confirm('Hapus proyek permanen?')) return;
            const fd = new FormData(); 
            fd.append('_token', csrfToken);
            fd.append('action', 'delete_album'); 
            fd.append('id', id);
            await fetch(apiUrl, { method:'POST', body:fd });
            window.location.reload();
        }

        function copyLink(id) {
            const link = "{{ url('/photo-link/album') }}/" + id;
            navigator.clipboard.writeText(link);
            alert('Link Tersalin!');
        }

        document.querySelectorAll('.dash-countdown').forEach(el => {
            const updateTimer = () => {
                const diff = parseInt(el.dataset.expire) * 1000 - new Date().getTime();
                if(diff < 0) { el.innerText = 'Expired'; return; }
                const d = Math.floor(diff / (1000 * 60 * 60 * 24));
                const h = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                el.innerText = `${d} Hari ${h} Jam`;
            };
            updateTimer();
            setInterval(updateTimer, 60000); 
        });

        async function openEdit(id) {
            document.getElementById('edit-panel').classList.remove('hidden');
            document.getElementById('edit_project_id').value = id;
            document.getElementById('edit_project_title').innerText = "ID: " + id;
            await loadEditPhotos(id);
        }
        
        function closeEdit() {
            if (activeUploads > 0) {
                alert("Harap tunggu hingga proses upload foto baru selesai sebelum menutup panel.");
                return;
            }
            document.getElementById('edit-panel').classList.add('hidden');
            window.location.reload(); 
        }

        async function loadEditPhotos(id) {
            const fd = new FormData(); 
            fd.append('_token', csrfToken);
            fd.append('action', 'get_album'); 
            fd.append('id', id);
            const res = await fetch(apiUrl, { method: 'POST', body: fd });
            const data = await res.json();
            
            const grid = document.getElementById('editPhotoGrid');
            grid.innerHTML = '';
            
            if(data.success && data.album.photos.length > 0) {
                data.album.photos.forEach((p) => {
                    grid.innerHTML += `
                        <div class="relative aspect-square bg-white rounded-xl overflow-hidden group shadow-sm border border-gray-200">
                            <img src="${p.url}" class="w-full h-full object-cover">
                            <button type="button" onclick="deleteSinglePhoto('${id}', '${p.file}')" class="absolute top-2 right-2 bg-red-500 text-white p-2 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity btn-touch" title="Hapus Foto">
                                <i data-lucide="trash-2" size="14"></i>
                            </button>
                        </div>
                    `;
                });
                lucide.createIcons();
            } else {
                grid.innerHTML = '<p class="col-span-full text-center text-sm text-gray-400 py-4">Proyek ini tidak memiliki foto.</p>';
            }
        }

        async function deleteSinglePhoto(id, fileName) {
            if(!confirm('Hapus foto ini?')) return;
            const fd = new FormData(); 
            fd.append('_token', csrfToken);
            fd.append('action', 'delete_photo'); 
            fd.append('id', id); 
            fd.append('file_name', fileName);
            await fetch(apiUrl, { method:'POST', body: fd });
            loadEditPhotos(id);
        }

        const editFileInput = document.getElementById('editFileInput');
        if(editFileInput) {
            editFileInput.onchange = async function() {
                const filesArray = Array.from(this.files);
                if(filesArray.length === 0) return;
                
                const id = document.getElementById('edit_project_id').value;
                const grid = document.getElementById('editPhotoGrid');
                
                activeUploads++; 
                
                for(let i=0; i<filesArray.length; i++) {
                    grid.innerHTML = `<p class="col-span-full text-center text-sm text-[#355faa] py-4 font-bold animate-pulse">Mengunggah ${i+1} dari ${filesArray.length} foto...</p>`;
                    const fd = new FormData();
                    fd.append('_token', csrfToken);
                    fd.append('action', 'upload_single');
                    fd.append('album_id', id);
                    fd.append('photo', filesArray[i]);
                    await fetch(apiUrl, { method: 'POST', body: fd });
                }
                
                this.value = ''; 
                activeUploads--;
                loadEditPhotos(id);
            };
        }
        @endif

        @if ($mode === 'customer_view')
        const albumData = @json(['id' => $current_album['id'], 'paket' => $paket, 'name' => $current_album['name']]);
        const photos = @json($current_album['photos']);
        
        const timerEl = document.getElementById('timer');
        if(timerEl) {
            const updateTimer = () => {
                const diff = parseInt(timerEl.dataset.expire) * 1000 - new Date().getTime();
                if(diff < 0) { window.location.reload(); return; }
                const d = Math.floor(diff / (1000 * 60 * 60 * 24));
                const h = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const m = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                timerEl.innerText = `${d} Hari ${h} Jam ${m} Menit`;
            };
            updateTimer();
            setInterval(updateTimer, 1000);
        }

        async function downloadAll() {
            const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent) || (navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1);
            const modal = document.getElementById('downloadModal');
            modal.classList.remove('hidden');

            if (isIOS) {
                setTimeout(() => {
                    window.location.href = `{{ url('/photo-link/download/zip') }}/${albumData.id}`;
                    setTimeout(() => { modal.classList.add('hidden'); }, 3000);
                }, 1000); 
            } else {
                for(let i=0; i<photos.length; i++) {
                    const a = document.createElement('a');
                    const ext = photos[i].file.split('.').pop();
                    const customName = `Snap Fun_${albumData.paket}_${albumData.name}_${i+1}.${ext}`;
                    
                    a.href = `{{ url('/photo-link/download/file') }}/${encodeURIComponent(photos[i].file)}?dl_name=${encodeURIComponent(customName)}`;
                    a.download = customName;
                    document.body.appendChild(a); 
                    a.click(); 
                    document.body.removeChild(a);
                    
                    await new Promise(r => setTimeout(r, 600)); 
                }
                modal.classList.add('hidden');
            }
        }

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
                normalOverlays.forEach(el => el.classList.add('hidden')); 
            } else {
                document.getElementById('normalActions').classList.remove('hidden');
                document.getElementById('gifActions').classList.add('hidden');
                if (gifHeader) gifHeader.classList.add('hidden');
                overlays.forEach(el => {
                    el.classList.add('hidden');
                    el.querySelector('input').checked = false; 
                });
                normalOverlays.forEach(el => el.classList.remove('hidden'));
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
        @endif
    </script>
</body>
</html>