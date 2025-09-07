<?php

namespace App\Filament\Resources\PhotoGridResource\Pages;

use App\Filament\Resources\PhotoGridResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPhotoGrid extends EditRecord
{
    protected static string $resource = PhotoGridResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
