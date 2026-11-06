<?php

namespace App\Filament\Resources\SiteGalleryItemResource\Pages;

use App\Filament\Resources\SiteGalleryItemResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageSiteGalleryItems extends ManageRecords
{
    protected static string $resource = SiteGalleryItemResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()->label('Tambah Foto')];
    }
}
