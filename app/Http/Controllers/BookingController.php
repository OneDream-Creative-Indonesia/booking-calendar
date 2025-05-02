<?php
namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Package;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function showCalendar()
    {
        return view('booking.calendar');
    }

}
