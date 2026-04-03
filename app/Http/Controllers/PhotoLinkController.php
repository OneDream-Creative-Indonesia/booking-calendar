<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;
use ZipArchive;

class PhotoLinkController extends Controller
{
    private $db_file;
    private $storage_dir;

    public function __construct()
    {
        // Simpan database.json secara aman di folder storage/app
        $this->db_file = storage_path('app/database.json');
        // Folder upload di public/uploads agar bisa diakses langsung via web
        $this->storage_dir = public_path('uploads');

        if (!File::exists($this->storage_dir)) {
            File::makeDirectory($this->storage_dir, 0777, true);
        }
        if (!File::exists($this->db_file)) {
            File::put($this->db_file, json_encode([]));
        }

        // Jalankan auto cleanup setiap kali controller dipanggil
        $this->autoCleanup();
    }

    private function getDb()
    {
        $data = json_decode(File::get($this->db_file), true);
        return is_array($data) ? $data : [];
    }

    private function saveDb($data)
    {
        File::put($this->db_file, json_encode($data, JSON_PRETTY_PRINT));
    }

    private function autoCleanup()
    {
        $db = $this->getDb();
        $changed = false;
        $now = time();

        foreach ($db as $id => $album) {
            if ($now >= $album['expires_at']) {
                foreach ($album['photos'] as $p) {
                    $filePath = $this->storage_dir . '/' . $p['file'];
                    if (File::exists($filePath)) {
                        File::delete($filePath);
                    }
                }
                // Bersihkan file zip temporary jika ada
                $zip_pattern = $this->storage_dir . '/temp_' . $id . '_*.zip';
                foreach (glob($zip_pattern) as $zf) {
                    @unlink($zf);
                }
                unset($db[$id]);
                $changed = true;
            }
        }

        if ($changed) {
            $this->saveDb($db);
        }
    }

    // Hitung ukuran folder (Helper)
    private function folderSize($dir)
    {
        $size = 0;
        foreach (glob(rtrim($dir, '/') . '/*', GLOB_NOSORT) as $each) {
            $size += is_file($each) ? filesize($each) : $this->folderSize($each);
        }
        return $size;
    }

    private function formatBytes($bytes)
    {
        if ($bytes == 0) return '0 B';
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $pow = floor(log($bytes) / log(1024));
        return round($bytes / pow(1024, $pow), 2) . ' ' . $units[$pow];
    }

    // --- VIEW METHODS ---

    public function index()
    {
        // 100% Cek menggunakan Session Login Bawaan Laravel
        if (!Auth::check()) {
            return redirect('/booking'); 
        }

        $db_all = $this->getDb();
        $total_files = 0;
        $active_links = 0;
        foreach ($db_all as $a) {
            $total_files += count($a['photos']);
            if (time() < $a['expires_at']) $active_links++;
        }
        $total_storage = $this->formatBytes($this->folderSize($this->storage_dir));

        return view('photo-link', [
            'mode' => 'admin_dashboard',
            'db_all' => $db_all,
            'active_links' => $active_links,
            'total_files' => $total_files,
            'total_storage' => $total_storage
        ]);
    }

    public function customerView($id)
    {
        $db = $this->getDb();
        $id = strtoupper($id);

        if (isset($db[$id])) {
            if (time() < $db[$id]['expires_at']) {
                return view('photo-link', [
                    'mode' => 'customer_view',
                    'current_album' => $db[$id]
                ]);
            } else {
                return view('photo-link', ['mode' => 'customer_expired']);
            }
        }

        return view('photo-link', ['mode' => 'customer_not_found']);
    }

    // --- ACTION METHODS ---

    public function downloadFile(Request $request, $file)
    {
        $target_file = $this->storage_dir . '/' . basename($file);
        $custom_name = $request->query('dl_name', basename($file));

        if (File::exists($target_file)) {
            // Memaksa browser (khususnya Android) untuk mengunduh, bukan membuka file di tab baru
            return response()->download($target_file, $custom_name, [
                'Content-Type' => 'application/octet-stream',
                'Content-Disposition' => 'attachment; filename="' . $custom_name . '"',
            ]);
        }
        abort(404, 'File tidak ditemukan.');
    }

    public function downloadZip($album_id)
    {
        $album_id = strtoupper($album_id);
        $db = $this->getDb();

        if (isset($db[$album_id]) && extension_loaded('zip')) {
            $album = $db[$album_id];
            $paket = $album['paket'] ?? 'SelfPhoto';
            $clean_name = preg_replace('/[^a-zA-Z0-9]/', '_', $album['name']);
            $zip_name = "SnapFun_" . $paket . "_" . $clean_name . ".zip";
            
            $zip_file = $this->storage_dir . '/temp_' . $album_id . '_' . time() . '.zip';
            $zip = new ZipArchive();
            
            if ($zip->open($zip_file, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
                foreach ($album['photos'] as $idx => $photo) {
                    $target_file = $this->storage_dir . '/' . $photo['file'];
                    if (File::exists($target_file)) {
                        // MENGGUNAKAN NAMA ASLI DI DALAM ZIP
                        $zip->addFile($target_file, $photo['name']);
                    }
                }
                $zip->close();

                if (File::exists($zip_file)) {
                    return response()->download($zip_file, $zip_name)->deleteFileAfterSend(true);
                }
            }
        }
        abort(500, 'Gagal membuat file ZIP.');
    }

    // --- API POST METHODS (AJAX) ---
    public function apiAction(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized. Harap login.'], 401);
        }

        $db = $this->getDb();
        $action = $request->input('action');

        if ($action === 'init_album') {
            $album_id = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 6));
            $hours = (int)($request->input('hours', 168));
            
            $db[$album_id] = [
                'id' => $album_id,
                'name' => $request->input('name', 'Proyek Tanpa Judul'),
                'paket' => $request->input('paket', 'Self Photo'),
                'expires_at' => time() + ($hours * 3600),
                'created_at' => time(),
                'photos' => []
            ];
            $this->saveDb($db);
            return response()->json(['success' => true, 'album_id' => $album_id]);
        }

        if ($action === 'upload_single') {
            $album_id = $request->input('album_id');
            if (isset($db[$album_id]) && $request->hasFile('photo')) {
                $file = $request->file('photo');
                
                // Mengambil nama asli file
                $originalName = $file->getClientOriginalName();
                
                // Menambahkan random ID ke nama sistem agar tidak tertimpa jika ada nama file sama, 
                // tapi nama aslinya tetap kita simpan di database.
                $safeName = preg_replace('/[^A-Za-z0-9.\-_]/', '_', $originalName);
                $fname = $album_id . '_' . time() . '_' . mt_rand(100, 999) . '_' . $safeName;
                
                // Move tidak mengompres file sama sekali (raw data transfer)
                $file->move($this->storage_dir, $fname);

                $db[$album_id]['photos'][] = [
                    'name' => $originalName, // NAMA ASLI DISIMPAN DI SINI
                    'file' => $fname,
                    'url' => asset('uploads/' . $fname) 
                ];
                $this->saveDb($db);
                return response()->json(['success' => true]);
            }
            return response()->json(['success' => false, 'message' => 'Gagal menyimpan file']);
        }

        if ($action === 'get_album') {
            $id = $request->input('id');
            if (isset($db[$id])) {
                return response()->json(['success' => true, 'album' => $db[$id]]);
            }
            return response()->json(['success' => false]);
        }

        if ($action === 'delete_album') {
            $id = $request->input('id');
            if (isset($db[$id])) {
                foreach ($db[$id]['photos'] as $p) {
                    $filePath = $this->storage_dir . '/' . $p['file'];
                    if (File::exists($filePath)) File::delete($filePath);
                }
                unset($db[$id]);
                $this->saveDb($db);
                return response()->json(['success' => true]);
            }
            return response()->json(['success' => false]);
        }

        if ($action === 'delete_photo') {
            $id = $request->input('id');
            $file_name = $request->input('file_name'); 
            if (isset($db[$id])) {
                foreach ($db[$id]['photos'] as $idx => $p) {
                    if ($p['file'] === $file_name) {
                        $filePath = $this->storage_dir . '/' . $p['file'];
                        if (File::exists($filePath)) File::delete($filePath);
                        unset($db[$id]['photos'][$idx]);
                    }
                }
                $db[$id]['photos'] = array_values($db[$id]['photos']);
                $this->saveDb($db);
                return response()->json(['success' => true]);
            }
            return response()->json(['success' => false]);
        }
    }
}