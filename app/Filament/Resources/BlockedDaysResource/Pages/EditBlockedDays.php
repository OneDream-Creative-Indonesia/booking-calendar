<?php

namespace App\Filament\Resources\BlockedDaysResource\Pages;

use App\Filament\Resources\BlockedDaysResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBlockedDays extends EditRecord
{
    protected static string $resource = BlockedDaysResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
