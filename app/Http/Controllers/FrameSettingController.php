<?php

namespace App\Http\Controllers;

use App\Models\FrameType;
use Illuminate\Http\Request;

class FrameSettingController extends Controller
{
    public function index()
    {
        // PERBAIKAN: Panggil FrameType (sebagai Folder), lalu load relasi 'frames' (FrameSettings)
        $frameTypes = FrameType::with('frames')->get();

        $formattedData = $frameTypes->map(function ($type) {
            return [
                'id' => 'folder_' . $type->id,
                'name' => $type->name, // Nama Folder (contoh: "Photostrip (2x6)")
                
                'frames' => $type->frames->map(function ($frame) use ($type) {
                    
                    $mediaUrl = $frame->getFirstMediaUrl('framesSettings');

                    $w = 1200; $h = 1800; 
                    if (strtolower($type->type) == '2x6') { $w = 600; $h = 1800; }
                    elseif (strtolower($type->type) == 'a4') { $w = 2480; $h = 3508; }
                    elseif (strtolower($type->type) == 'a3') { $w = 3508; $h = 4961; }

                    if (strtolower($frame->orientation) == 'landscape') {
                        $temp = $w; $w = $h; $h = $temp;
                    }

                   return [
                        'id' => 'frame_' . $frame->id,
                        'name' => $frame->name, 
                        'paperSize' => $type->type,
                        'orientation' => ucfirst($frame->orientation), 
                        'width' => $frame->width ?? $w, 
                        'height' => $frame->height ?? $h, 
                        'imageUrl' => $mediaUrl ?: null, 
                        
                        // KARENA SUDAH DI-CAST SEBAGAI ARRAY DI MODEL, KITA BISA LANGSUNG PANGGIL:
                        'masks' => $frame->masks ?? [] 
                    ];
                })
            ];
        });

        return response()->json(['success' => true, 'data' => $formattedData]);
    }
}