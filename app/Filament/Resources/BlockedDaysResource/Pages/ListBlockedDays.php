<?php

namespace App\Filament\Resources\BlockedDaysResource\Pages;

use App\Filament\Resources\BlockedDaysResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBlockedDays extends ListRecords
{
    protected static string $resource = BlockedDaysResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->label('Add Studio Tidak Tersedia'),
        ];
    }
}
