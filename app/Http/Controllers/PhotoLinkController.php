<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http; // Tambahkan ini untuk akses G-Drive API

class PhotoLinkController extends Controller
{
    // 1. TAMPILAN ADMIN DASHBOARD
    public function index()
    {
        // Ambil semua data project dari database
        $db_all = DB::table('snap_links')->orderBy('created_at', 'desc')->get();
        
        return view('photo-link', [
            'mode' => 'admin_dashboard',
            'db_all' => $db_all,
            'active_links' => DB::table('snap_links')->where('expires_at', '>', now())->count(),
        ]);
    }

    // 2. TAMPILAN GALERI PELANGGAN
    public function customerView($id)
    {
        // Cari data album berdasarkan ID
        $album = DB::table('snap_links')->where('album_id', strtoupper($id))->first();

        // Jika album tidak ada, tampilkan halaman error
        if (!$album) {
            return view('photo-link', ['mode' => 'error']);
        }

        return view('photo-link', [
            'mode' => 'customer_view',
            'current_album' => [
                'id' => $album->album_id,
                'name' => $album->name,
                'paket' => $album->paket,
                'folder_id' => $album->folder_id,
                'expires_at' => strtotime($album->expires_at)
            ]
        ]);
    }

    // 3. FUNGSI UNTUK API BACKEND (AJAX DARI ADMIN)
    public function apiAction(Request $request)
    {
        $action = $request->input('action');
        $apiKey = env('GDRIVE_API_KEY'); // Pastikan udah diset di .env

        // -----------------------------------------------------------
        // Aksi 1: Buat Link Biasa (Single)
        // -----------------------------------------------------------
        if ($action === 'create_album') {
            preg_match('/(?:folders\/|id=)([\w-]+)/', $request->drive_link, $matches);
            $folder_id = $matches[1] ?? null;

            if (!$folder_id) {
                return response()->json(['success' => false, 'message' => 'Link G-Drive tidak valid']);
            }

            DB::table('snap_links')->insert([
                'album_id' => strtoupper(substr(md5(uniqid()), 0, 6)),
                'name' => $request->name,
                'paket' => $request->paket,
                'drive_link' => $request->drive_link,
                'folder_id' => $folder_id,
                'group_name' => $request->group_name,
                'expires_at' => now()->addHours((int)$request->hours),
                'created_at' => now(),
            ]);

            return response()->json(['success' => true]);
        }

        // -----------------------------------------------------------
        // Aksi 2: BATCH Create (Tarik Otomatis)
        // -----------------------------------------------------------
        if ($action === 'create_album_batch') {
            preg_match('/(?:folders\/|id=)([\w-]+)/', $request->drive_link, $matches);
            $main_folder_id = $matches[1] ?? null;

            if (!$main_folder_id) {
                return response()->json(['success' => false, 'message' => 'Link G-Drive (Folder Utama) tidak valid.']);
            }

            $group_name = trim($request->group_name);
            
            // Jika nama folder dashboard dikosongkan, tarik nama dari folder utama G-Drive
            if (empty($group_name)) {
                $parentResponse = Http::get("https://www.googleapis.com/drive/v3/files/{$main_folder_id}", [
                    'key' => $apiKey,
                    'fields' => 'name'
                ]);
                
                if ($parentResponse->successful()) {
                    $group_name = $parentResponse->json('name');
                } else {
                    $group_name = 'Proyek Batch';
                }
            }

            // Request ke Google Drive API untuk cari sub-folder
            $response = Http::get("https://www.googleapis.com/drive/v3/files", [
                'q' => "'" . $main_folder_id . "' in parents and mimeType='application/vnd.google-apps.folder' and trashed=false",
                'key' => $apiKey,
                'fields' => 'files(id,name)',
                'pageSize' => 1000
            ]);

            if ($response->failed()) {
                return response()->json(['success' => false, 'message' => 'Gagal akses API. Pastikan Folder Utama sudah di-set ke "Siapa saja yang memiliki link".']);
            }

            $files = $response->json('files');
            
            if (empty($files)) {
                return response()->json(['success' => false, 'message' => 'Tidak ada sub-folder klien ditemukan di dalam link tersebut.']);
            }

            $insertData = [];
            foreach ($files as $folder) {
                $insertData[] = [
                    'album_id' => strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 6)),
                    'name' => $folder['name'], // Nama ambil dari folder
                    'paket' => $request->paket,
                    'drive_link' => 'https://drive.google.com/drive/folders/' . $folder['id'],
                    'folder_id' => $folder['id'],
                    'group_name' => $group_name,
                    'expires_at' => now()->addHours((int)$request->hours),
                    'created_at' => now()
                ];
            }

            // Insert massal ke database
            DB::table('snap_links')->insert($insertData);

            return response()->json(['success' => true, 'count' => count($insertData)]);
        }

        // -----------------------------------------------------------
        // Aksi 3: Get Data untuk Modal Edit
        // -----------------------------------------------------------
        if ($action === 'get_album') {
            $album = DB::table('snap_links')->where('album_id', $request->id)->first();
            if ($album) {
                return response()->json(['success' => true, 'album' => $album]);
            }
            return response()->json(['success' => false]);
        }

        // -----------------------------------------------------------
        // Aksi 4: Update Data Edit
        // -----------------------------------------------------------
        if ($action === 'update_album') {
            preg_match('/(?:folders\/|id=)([\w-]+)/', $request->drive_link, $matches);
            $folder_id = $matches[1] ?? null;

            if (!$folder_id) {
                return response()->json(['success' => false, 'message' => 'Link Invalid']);
            }

            DB::table('snap_links')->where('album_id', $request->id)->update([
                'name' => $request->name,
                'paket' => $request->paket,
                'drive_link' => $request->drive_link,
                'folder_id' => $folder_id,
                'group_name' => $request->group_name,
            ]);

            return response()->json(['success' => true]);
        }

        // -----------------------------------------------------------
        // Aksi 5: Hapus Single Link
        // -----------------------------------------------------------
        if ($action === 'delete_album') {
            DB::table('snap_links')->where('album_id', $request->id)->delete();
            return response()->json(['success' => true]);
        }

        // -----------------------------------------------------------
        // Aksi 6: Hapus Folder Sekaligus
        // -----------------------------------------------------------
        if ($action === 'delete_group') {
            DB::table('snap_links')->where('group_name', $request->group_name)->delete();
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'message' => 'Aksi tidak ditemukan']);
    }
}