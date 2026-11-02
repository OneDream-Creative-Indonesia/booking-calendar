<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use App\Services\BookingReportService;
use Filament\Widgets\ChartWidget;

class TopBackgroundChart extends ChartWidget
{
    public static function canView(): bool
    {
        return auth()->user()?->can('view_any_booking') ?? false;
    }

    protected static ?string $heading = 'Top Favorit Background';
    protected static ?int $sort = 3;
    protected int|string|array $columnSpan = 1;

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return ['indexAxis' => 'y', 'plugins' => ['legend' => ['display' => false]]];
    }

    protected function getData(): array
    {
        $top = array_slice(app(BookingReportService::class)->summary(Booking::with(['background', 'bookingAddons'])->get())['backgrounds'], 0, 5);

        return [
            'datasets' => [['label' => 'Booking', 'data' => array_column($top, 'bookings'), 'backgroundColor' => '#f59e0b']],
            'labels' => array_column($top, 'name'),
        ];
    }
}
