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
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'layout_image' => 'required|string',  // base64 image
            'warna' => 'required|string',
        ]);

        // Cek jika validasi gagal
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        // Simpan ke database
        $photoOrder = PhotoOrder::create([
            'name' => $request->name,
            'type' => $request->type,
            'layout_image' => $request->layout_image,
            'warna' => $request->warna,
            // status otomatis 'pending'
        ]);
        $image = $request->input('layout_image');
        $image = str_replace('data:image/png;base64,', '', $image);
        $image = str_replace(' ', '+', $image);
        $imageContent = base64_decode($image);

        // simpan via spatie
        $photoOrder
            ->addMediaFromString($imageContent)
            ->usingFileName(uniqid().'.png')
            ->toMediaCollection('layout');
        return response()->json([
            'success' => true,
            'message' => 'Data berhasil disimpan',
            'data' => $photoOrder
        ]);
    }
}
