<?php

namespace App\Http\Controllers;

use App\Models\Frame;
use Illuminate\Http\Request;

class FrameController extends Controller
{
    public function index()
    {
        try {

            $frames = Frame::all();

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
