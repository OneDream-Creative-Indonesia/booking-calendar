<?php

namespace App\Filament\Resources\PhotoGridResource\Pages;

use App\Filament\Resources\PhotoGridResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPhotoGrids extends ListRecords
{
    protected static string $resource = PhotoGridResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
