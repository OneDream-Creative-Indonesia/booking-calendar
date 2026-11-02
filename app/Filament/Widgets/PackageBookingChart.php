<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use App\Services\BookingReportService;
use Filament\Widgets\ChartWidget;

class PackageBookingChart extends ChartWidget
{
    protected static ?string $heading = 'Jumlah Booking per Paket';
    protected static ?int $sort = 2;
    protected int|string|array $columnSpan = 1;

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getData(): array
    {
        $packages = app(BookingReportService::class)->summary(Booking::with(['package', 'bookingAddons'])->get())['packages'];

        return [
            'datasets' => [[
                'label' => 'Booking',
                'data' => array_column($packages, 'bookings'),
                'backgroundColor' => ['#f59e0b', '#3b82f6', '#10b981', '#ef4444', '#8b5cf6', '#ec4899', '#14b8a6', '#64748b'],
            ]],
            'labels' => array_column($packages, 'name'),
        ];
    }
}
