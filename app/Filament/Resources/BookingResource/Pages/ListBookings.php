<?php

namespace App\Filament\Resources\BookingResource\Pages;

use App\Filament\Resources\BookingResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Pages\Actions\Action;

class ListBookings extends ListRecords
{
    protected static string $resource = BookingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('Export CSV')
                ->label('Export CSV')
                ->icon('heroicon-o-arrow-down-tray')
                ->url(route('export.bookings'))
                ->openUrlInNewTab(),

        ];
    }
}
