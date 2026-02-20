<?php

namespace App\Filament\Resources\TicketingResource\Pages;

use App\Filament\Resources\TicketingResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewTicketing extends ViewRecord
{
    protected static string $resource = TicketingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
