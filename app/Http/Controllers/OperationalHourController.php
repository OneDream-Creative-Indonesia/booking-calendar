<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\BlockedDate;
use Illuminate\Http\Request;
use App\Models\OperationalHour;
use App\Http\Controllers\Controller;

class OperationalHourController extends Controller
{
    public function getOperationalHours()
    {
        $closedDays = OperationalHour::where('is_open', false)
            ->pluck('day')
            ->toArray();

        return response()->json([
            'closed_days' => $closedDays,
        ]);
    }
    public function blockedTimes(){
        return BlockedDate::all(['date', 'reason']);
    }
    public function closedDays()
    {
        $dayName = Carbon::now()->locale('id')->translatedFormat('l');
        $operationalHour = OperationalHour::where('day', $dayName)->first();

        return response()->json([
            'close_time' => optional($operationalHour)->close_time ?? '19:00'
        ]);
    }

    public function getTimeSlots(Request $request)
    {
        $date = $request->query('date');
        if (!$date) {
            return response()->json(['message' => 'Tanggal wajib diisi'], 400);
        }

        // Format nama hari (Senin, Selasa, dll)
        $dayName = ucfirst(Carbon::parse($date)->locale('id')->translatedFormat('l'));
        $operational = OperationalHour::where('day', $dayName)->where('is_open', true)->first();

        if (!$operational || !$operational->open_time || !$operational->close_time) {
            return response()->json([
                'slots' => [],
                'message' => 'Studio tutup pada hari ini'
            ]);
        }

        $start = Carbon::parse($operational->open_time);
        $end = Carbon::parse($operational->close_time);

        $slots = [];

        while ($start->lt($end)) {
            $slots[] = $start->format('H:i');
            $start->addMinutes(30);
        }

        return response()->json([
            'slots' => $slots
        ]);
    }

}
