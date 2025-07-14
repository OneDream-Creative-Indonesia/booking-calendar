<?php
namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Background;
use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class BookingController extends Controller
{
    public function getBookedTimes($date)
    {
        $packageId = request()->query('package_id');

        $booked = Booking::whereDate('date', $date)
            ->when($packageId, fn ($q) => $q->where('package_id', $packageId))
            ->pluck('time')
            ->toArray();

        return response()->json([
            'booked_times' => $booked,
        ]);
    }

    public function showCalendar()
    {
        return view('background');
    }
       public function home()
    {
        return view('home');
    }

    public function submitBooking(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'whatsapp' => 'required|string|max:20',
            'people_count' => 'required|integer|min:1',
            'package_id' => 'required|exists:packages,id',
            'background_id' => 'required|exists:backgrounds,id',
            'date' => 'required|date',
            'time' => 'required|string',
            'price' => 'required|integer|min:0',
            'voucher_id' => 'nullable|exists:vouchers,id',
            'confirmation' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
              \Log::error('Validasi booking gagal', $validator->errors()->toArray());
            return response()->json([
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Validasi voucher kalau ada
            if ($request->voucher_id) {
               $voucher = \App\Models\Voucher::find($request->voucher_id);

                if (!$voucher) {
        return response()->json(['message' => 'Voucher tidak ditemukan.'], 400);
    }

    if (!$voucher->is_active) {
        return response()->json(['message' => 'Voucher tidak aktif.'], 400);
    }

    if ($voucher->start_date && $voucher->start_date > now()->toDateString()) {
        return response()->json(['message' => 'Voucher belum berlaku.'], 400);
    }

    if ($voucher->end_date && $voucher->end_date < now()->toDateString()) {
        return response()->json(['message' => 'Voucher sudah kedaluwarsa.'], 400);
    }

    if ($voucher->usage_limit && $voucher->used_count >= $voucher->usage_limit) {
        return response()->json(['message' => 'Voucher sudah mencapai batas pemakaian.'], 400);
    }

                $package = \App\Models\Package::find($request->package_id);
                $basePrice = $package->price;
                $extraPricePerPerson = match ($package->id) {
                    1 => 15000,
                    2 => 20000,
                    default => 0
                };

                if ($request->people_count > 1) {
                    $basePrice += ($request->people_count - 1) * $extraPricePerPerson;
                }
                $expectedDiscount = intval(round($basePrice * ($voucher->discount_percent / 100)));
                $expectedFinalPrice = max(0, $basePrice - $expectedDiscount);

                if ($request->price != $expectedFinalPrice) {
                    return response()->json([
                        'message' => 'Harga tidak valid. Cek kembali perhitungan.',
                    ], 400);
                }
            }

            // Simpan booking
            $booking = Booking::create([
                'name' => $request->name,
                'email' => $request->email,
                'whatsapp' => $request->whatsapp,
                'people_count' => $request->people_count,
                'package_id' => $request->package_id,
                'background_id' => $request->background_id,
                'date' => $request->date,
                'time' => $request->time,
                'price' => $request->price,
                'voucher_id' => $request->voucher_id,
                'confirmation' => $request->confirmation ?? 0,
            ]);

            // Tambah penggunaan voucher
            if ($request->voucher_id) {
                $voucher->increment('used_count');
            }

            return response()->json([
                'message' => 'Booking berhasil disimpan',
                'booking' => $booking
            ]);
        } catch (\Exception $e) {
             \Log::error('Gagal menyimpan booking', [
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                    'request' => $request->all(),
                ]);
            return response()->json([
                'message' => 'Terjadi kesalahan saat menyimpan booking',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function getBackgrounds(Request $request)
    {
        try {
            $packageId = $request->query('package_id');

            if (!$packageId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Parameter package_id wajib diisi.'
                ], 400);
            }

            $package = \App\Models\Package::with('backgrounds')->find($packageId);

            if (!$package) {
                return response()->json([
                    'success' => false,
                    'message' => 'Paket tidak ditemukan.'
                ], 404);
            }

            $backgrounds = $package->backgrounds->map(function ($bg) {
                return [
                    'id' => $bg->id,
                    'name' => $bg->name,
                    'image_url' => $bg->getFirstMediaUrl('image'),
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $backgrounds
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data background.',
                'error' => $e->getMessage()
            ], 500);
        }
    }


}
