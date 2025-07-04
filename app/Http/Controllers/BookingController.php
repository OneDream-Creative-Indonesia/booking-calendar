<?php
namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BookingController extends Controller
{
    public function getBookedTimes($date)
    {
        $bookedTimes = Booking::where('date', $date)->pluck('time');

        return response()->json([
            'booked_times' => $bookedTimes
        ]);
    }
    // public function showCalendar()
    // {
    //     return view('booking.calendar');
    // }
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

                if (!$voucher || !$voucher->isValid()) {
                    return response()->json(['message' => 'Voucher tidak tersedia atau tidak valid'], 400);
                }

                $package = \App\Models\Package::find($request->package_id);
                $basePrice = $package->price;

                if ($request->people_count > 1) {
                    $basePrice += ($request->people_count - 1) * 15000;
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
            return response()->json([
                'message' => 'Terjadi kesalahan saat menyimpan booking',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getBackgrounds()
    {
        $backgrounds = \App\Models\Background::all();
        return response()->json($backgrounds);
    }

}
