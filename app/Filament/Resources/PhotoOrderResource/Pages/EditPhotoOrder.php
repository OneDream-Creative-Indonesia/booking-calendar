<?php

namespace App\Filament\Resources\PhotoOrderResource\Pages;

use App\Filament\Resources\PhotoOrderResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPhotoOrder extends EditRecord
{
    protected static string $resource = PhotoOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
