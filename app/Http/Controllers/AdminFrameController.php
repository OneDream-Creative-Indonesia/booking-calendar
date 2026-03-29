<?php

namespace App\Http\Controllers;

use App\Models\FrameType;
use App\Models\FrameSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminFrameController extends Controller
{
    // Mengambil semua data untuk ditampilkan di Dashboard Admin
    public function getFrames()
    {
        $frameTypes = FrameType::with('frames')->get();

        $formattedData = $frameTypes->map(function ($type) {
            return [
                'id' => 'folder_' . $type->id, // Format ID folder untuk UI
                'name' => $type->name,
                'frames' => $type->frames->map(function ($frame) use ($type) {
                    
                    $masksData = [];
                    if (!empty($frame->masks)) {
                        $masksData = is_string($frame->masks) ? json_decode($frame->masks, true) : $frame->masks;
                    }

                    // Ambil ukuran kertas dari tabel FrameType (Folder)
                    $paperSize = strtolower($type->type ?? '4r');

                    return [
                        'id' => $frame->id,
                        'name' => $frame->name,
                        'paperSize' => $type->type, 
                        'orientation' => $frame->orientation,
                        'width' => $paperSize == '2x6' ? 600 : ($paperSize == 'a4' ? 2480 : ($paperSize == 'a3' ? 3508 : 1200)),
                        'height' => $paperSize == 'a4' ? 3508 : ($paperSize == 'a3' ? 4961 : 1800),
                        'imageUrl' => $frame->getFirstMediaUrl('framesSettings'),
                        'masks' => $masksData
                    ];
                })
            ];
        });

        return response()->json(['success' => true, 'data' => $formattedData]);
    }

    // Membuat Folder (FrameType) Baru
    public function createFolder(Request $request)
    {
        $request->validate(['name' => 'required|string']);
        
        FrameType::create([
            'name' => $request->name,
            'type' => '4R', // Beri nilai default 4R saat folder baru dibuat
        ]);

        return response()->json(['success' => true]);
    }

    // Menghapus Folder dan isinya
    public function deleteFolder($id)
    {
        $folderId = str_replace('folder_', '', $id);
        $folder = FrameType::find($folderId);
        
        if ($folder) {
            foreach ($folder->frames as $frame) {
                $frame->clearMediaCollection('framesSettings');
                $frame->delete();
            }
            $folder->delete();
        }
        return response()->json(['success' => true]);
    }

    // Menyimpan Frame & Masking
    public function saveFrame(Request $request)
    {
        $typeId = str_replace('folder_', '', $request->folder_id);
        
        if (Str::contains($request->frame_id, 'folder_') || Str::contains($request->frame_id, 'new_')) {
            $frame = new FrameSetting();
        } else {
            $frame = FrameSetting::findOrFail($request->frame_id);
        }

        // 1. SIMPAN DATA KE FRAME SETTING
        $frame->type_id = $typeId;
        $frame->name = $request->name;
        $frame->orientation = strtolower($request->orientation);
        $frame->masks = $request->masks; // Simpan array koordinat
        $frame->save();

        if ($request->has('paper_size')) {
            \App\Models\FrameType::where('id', $typeId)->update([
                'type' => $request->paper_size // Simpan ukuran kertas (misal: A3, 4R) ke sini
            ]);
        }

        // 3. SIMPAN GAMBAR FOTO
        if ($request->image_base64 && str_starts_with($request->image_base64, 'data:image')) {
            $frame->clearMediaCollection('framesSettings');
            $frame->addMediaFromBase64($request->image_base64)
                  ->usingFileName('frame_' . time() . '.png')
                  ->toMediaCollection('framesSettings');
        }

        return response()->json(['success' => true, 'message' => 'Frame tersimpan']);
    }
}