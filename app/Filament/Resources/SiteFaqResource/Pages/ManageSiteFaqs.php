<?php

namespace App\Filament\Resources\SiteFaqResource\Pages;

use App\Filament\Resources\SiteFaqResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageSiteFaqs extends ManageRecords
{
    protected static string $resource = SiteFaqResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()->label('Tambah FAQ')];
    }
}
