<?php

namespace App\Filament\Resources\PhotoOrderResource\Pages;

use App\Filament\Resources\PhotoOrderResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPhotoOrders extends ListRecords
{
    protected static string $resource = PhotoOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
