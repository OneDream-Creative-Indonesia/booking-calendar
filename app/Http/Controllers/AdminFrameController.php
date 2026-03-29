<?php

namespace App\Http\Controllers;

use App\Models\FrameType;
use App\Models\FrameSetting;
use Illuminate\Http\Request;

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
                    
                    // Cek apakah bentuknya String atau sudah Array (tergantung model)
                    $masksData = [];
                    if (!empty($frame->masks)) {
                        $masksData = is_string($frame->masks) ? json_decode($frame->masks, true) : $frame->masks;
                    }

                    return [
                        'id' => $frame->id,
                        'name' => $frame->name,
                        'paperSize' => $type->type,
                        'orientation' => $frame->orientation,
                        // Secara otomatis dihitung oleh backend karena tidak ada di DB
                        'width' => strtolower($type->type) == '2x6' ? 600 : (strtolower($type->type) == 'a4' ? 2480 : (strtolower($type->type) == 'a3' ? 3508 : 1200)),
                        'height' => strtolower($type->type) == 'a4' ? 3508 : (strtolower($type->type) == 'a3' ? 4961 : 1800),
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
            'type' => '4R', // Default tipe, bisa diubah jika perlu
        ]);

        return response()->json(['success' => true]);
    }

    // Menghapus Folder dan isinya
    public function deleteFolder($id)
    {
        $folder = FrameType::find($id);
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
        $isNewFrame = str_contains($request->frame_id, 'folder_');

        if ($isNewFrame) {
            $frame = new FrameSetting();
        } else {
            $frame = FrameSetting::findOrFail($request->frame_id);
        }

        // HANYA MASUKKAN FIELD YANG ADA DI DATABASE SAJA
        $frame->type_id = $typeId;
        $frame->name = $request->name;
        $frame->orientation = strtolower($request->orientation);
        $frame->masks = $request->masks; // Simpan array koordinat
        
        // TIDAK ADA LAGI PENYIMPANAN $frame->width dan $frame->height!
        $frame->save();

        if ($request->image_base64 && str_starts_with($request->image_base64, 'data:image')) {
            $frame->clearMediaCollection('framesSettings');
            $frame->addMediaFromBase64($request->image_base64)
                  ->usingFileName('frame_' . time() . '.png')
                  ->toMediaCollection('framesSettings');
        }

        return response()->json(['success' => true, 'message' => 'Frame tersimpan']);
    }
}
