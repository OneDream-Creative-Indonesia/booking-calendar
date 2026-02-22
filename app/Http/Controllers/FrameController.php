<?php

namespace App\Http\Controllers;

use App\Models\Frame;
use Illuminate\Http\Request;
use App\Models\PhotoGrid;

class FrameController extends Controller
{
    public function index(Request $request)
    {
        try {

            $photoGridId = $request->photo_grid_id;

            if ($photoGridId) {

                $photoGrid = PhotoGrid::findOrFail($photoGridId);

                $frames = $photoGrid->frames; // ambil dari pivot

            } else {

                $frames = Frame::all(); // fallback kalau ga kirim id
            }

            $data = $frames->map(function ($frame) {
                return [
                    'id' => $frame->id,
                    'name' => $frame->name,
                    'image_url' => $frame->getFirstMediaUrl('frames'),
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $data
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data frame',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
