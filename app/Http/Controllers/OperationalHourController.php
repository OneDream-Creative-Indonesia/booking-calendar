<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
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
    public function closedDays()
    {
        $dayName = Carbon::now()->locale('id')->translatedFormat('l');
        $operationalHour = OperationalHour::where('day', $dayName)->first();

        return response()->json([
            'close_time' => optional($operationalHour)->close_time ?? '19:00'
        ]);
    }
}
