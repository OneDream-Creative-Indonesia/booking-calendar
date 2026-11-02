<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use App\Services\BookingReportService;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class BookingStatsOverview extends BaseWidget
{
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
            Stat::make("Pendapatan {$label}", BookingReportService::rupiah($month['revenue']))
                ->icon('heroicon-o-banknotes')->color('success'),
            Stat::make('Pendapatan Keseluruhan', BookingReportService::rupiah($all['revenue']))
                ->description($all['bookings'] . ' booking · ' . number_format($all['visitors'], 0, ',', '.') . ' pengunjung')
                ->icon('heroicon-o-currency-dollar')->color('warning'),
        ];
    }
}
