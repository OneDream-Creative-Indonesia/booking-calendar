<?php

namespace App\Filament\Resources\TicketingResource\Pages;

use App\Filament\Resources\TicketingResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Colors\Color;

class ListTicketings extends ListRecords
{
    protected static string $resource = TicketingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('exportCsv')
            ->label('Export Ticketing')
            ->url(route('ticketings_reports.export'))
            ->icon('heroicon-o-arrow-down-tray'),
        ];
    }
}
