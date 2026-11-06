<?php

namespace App\Filament\Resources\SitePromoResource\Pages;

use App\Filament\Resources\SitePromoResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageSitePromos extends ManageRecords
{
    protected static string $resource = SitePromoResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()->label('Tambah Promo')];
    }
}
