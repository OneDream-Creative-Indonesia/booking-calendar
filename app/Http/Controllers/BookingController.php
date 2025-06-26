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
    public function showCalendar()
    {
        return view('booking.calendar');
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
        'date' => 'required|date',
        'time' => 'required|string',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'message' => 'Validasi gagal',
            'errors' => $validator->errors()
        ], 422);
    }

    try {
        $booking = Booking::create([
            'name' => $request->name,
            'email' => $request->email,
            'whatsapp' => $request->whatsapp,
            'people_count' => $request->people_count,
            'package_id' => $request->package_id,
            'date' => $request->date,
            'time' => $request->time,
        ]);

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

}
