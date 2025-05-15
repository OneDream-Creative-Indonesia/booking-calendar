<?php

namespace App\Filament\Resources\OperationalSettingsResource\Pages;

use App\Filament\Resources\OperationalSettingsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOperationalSettings extends ListRecords
{
    protected static string $resource = OperationalSettingsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
