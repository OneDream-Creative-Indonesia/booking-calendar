<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use App\Services\BookingReportService;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class BookingStatsOverview extends BaseWidget
{
    public static function canView(): bool
    {
        return auth()->user()?->can('view_any_booking') ?? false;
    }

    protected static ?int $sort = 1;
    protected static ?string $pollingInterval = null;

    protected function getStats(): array
    {
        $svc = app(BookingReportService::class);
        $month = $svc->summary($svc->bookings((int) now()->format('Y'), (int) now()->format('n')));
        $all = $svc->summary(Booking::with('bookingAddons')->get());
        $label = BookingReportService::monthName((int) now()->format('n'));

        return [
            Stat::make("Pengunjung {$label}", number_format($month['visitors'], 0, ',', '.'))
                ->description('Total jumlah orang bulan ini')->icon('heroicon-o-users')->color('info'),
            Stat::make("Booking {$label}", $month['bookings'])
                ->description('Booking aktif (tanpa batal)')->icon('heroicon-o-calendar-days')->color('primary'),
            Stat::make('Total Pengunjung', number_format($all['visitors'], 0, ',', '.'))
                ->description('Keseluruhan, dari List Booking')->icon('heroicon-o-user-group')->color('success'),
            Stat::make('Total Booking', $all['bookings'])
                ->description('Keseluruhan, dari List Booking')->icon('heroicon-o-clipboard-document-list')->color('warning'),
        ];
    }
}
