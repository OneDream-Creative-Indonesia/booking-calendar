<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\PhotoOrder;
class PhotoOrderController extends Controller
{
    public function store(Request $request)
    {
         $validator = Validator::make($request->all(), [
        'name'         => 'required|string|max:255',
        'type'         => 'required|string|max:255',
        'layout_image' => 'required|string',
        'frame_id'     => 'nullable|exists:frames,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors'  => $validator->errors()
            ], 422);
        }

        // Simpan ke DB tanpa layout_image
        $photoOrder = PhotoOrder::create([
            'name'     => $request->name,
            'type'     => $request->type,
            'frame_id' => $request->frame_id,
        ]);

        // Proses base64 → simpan via Spatie
        $image        = str_replace('data:image/png;base64,', '', $request->layout_image);
        $image        = str_replace(' ', '+', $image);
        $imageContent = base64_decode($image);

        $photoOrder
            ->addMediaFromString($imageContent)
            ->usingFileName(uniqid() . '.png')
            ->toMediaCollection('layout');

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil disimpan',
            'data'    => $photoOrder
        ]);
    }
}
