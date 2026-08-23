<?php

namespace App\Http\Controllers;

use App\Services\GoogleDriveService;
use Illuminate\Http\Request;

class GoogleDriveAuthController extends Controller
{
    public function connect(GoogleDriveService $drive)
    {
        return redirect($drive->getAuthUrl());
    }

    public function callback(Request $request, GoogleDriveService $drive)
    {
        if ($request->has('error')) {
            return redirect('/gdrive/select-folder')->with('error', 'Login Google dibatalkan.');
        }

        $success = $drive->handleCallback($request->get('code'));

        if (!$success) {
            return redirect('/gdrive/select-folder')->with('error', 'Gagal menghubungkan akun Google Drive.');
        }

        return redirect()->route('gdrive.select-folder')->with('success', 'Berhasil terhubung ke Google Drive!');
    }

    public function selectFolder(GoogleDriveService $drive)
    {
        $connected = $drive->isConnected();
        $folders = $connected ? $drive->listFolders() : [];

        return view('gdrive.select-folder', compact('connected', 'folders'));
    }

    public function saveFolder(Request $request, GoogleDriveService $drive)
    {
        $request->validate([
            'folder_id' => 'required|string',
            'folder_name' => 'required|string',
        ]);

        $drive->saveMainFolder($request->folder_id, $request->folder_name);

        return redirect()->route('gdrive.select-folder')->with('success', 'Folder utama berhasil disimpan: ' . $request->folder_name);
    }
}