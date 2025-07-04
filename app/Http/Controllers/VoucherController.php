<?php

namespace App\Http\Controllers;

use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class VoucherController extends Controller
{
   public function getVoucher(Request $request)
    {
    $vouchers = Voucher::all(); // atau bisa pakai paginate() kalau banyak banget
            return response()->json($vouchers);
    }
    /**
     * Check voucher validity
     */
    public function checkVoucher(Request $request): JsonResponse
    {
        $request->validate([
            'code_voucher' => 'required|string'
        ]);

        try {
            $voucher = Voucher::where('code_voucher', $request->code_voucher)->first();

            if (!$voucher) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kode voucher tidak ditemukan'
                ], 404);
            }

            if (!$voucher->isValid()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kode voucher tidak valid atau sudah expired'
                ], 400);
            }

            return response()->json([
                'success' => true,
                'message' => 'Kode voucher valid',
                'data' => [
                    'id' => $voucher->id,
                    'code_voucher' => $voucher->code_voucher,
                    'discount_percent' => $voucher->safe_discount_percent,
                    'discount_formatted' => 'Rp ' . number_format($voucher->discount_price, 0, ',', '.')
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memproses voucher'
            ], 500);
        }
    }

    /**
     * Apply voucher to booking
     */
    public function applyVoucher(Request $request): JsonResponse
    {
        $request->validate([
            'code_voucher' => 'required|string',
            'booking_id' => 'required|exists:bookings,id'
        ]);

        try {
            $voucher = Voucher::where('code_voucher', $request->code_voucher)->first();

            if (!$voucher || !$voucher->isValid()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kode voucher tidak valid'
                ], 400);
            }

            // Update booking dengan voucher
            $booking = \App\Models\Booking::find($request->booking_id);
            $booking->update([
                'voucher_id' => $voucher->id,
                'voucher_code' => 1, // sesuai permintaan Anda
                'discount_amount' => $voucher->discount_price
            ]);

            // Increment usage count
            $voucher->incrementUsage();

            return response()->json([
                'success' => true,
                'message' => 'Voucher berhasil diterapkan',
                'data' => [
                    'discount_amount' => $voucher->discount_price,
                    'discount_formatted' => 'Rp ' . number_format($voucher->discount_price, 0, ',', '.')
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menerapkan voucher'
            ], 500);
        }
    }

    /**
     * Remove voucher from booking
     */
    public function removeVoucher(Request $request): JsonResponse
    {
        $request->validate([
            'booking_id' => 'required|exists:bookings,id'
        ]);

        try {
            $booking = \App\Models\Booking::find($request->booking_id);

            if ($booking->voucher_id) {
                // Decrement usage count
                $voucher = Voucher::find($booking->voucher_id);
                if ($voucher) {
                    $voucher->decrement('used_count');
                }
            }

            // Remove voucher from booking
            $booking->update([
                'voucher_id' => null,
                'voucher_code' => 0,
                'discount_amount' => 0
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Voucher berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus voucher'
            ], 500);
        }
    }
}
