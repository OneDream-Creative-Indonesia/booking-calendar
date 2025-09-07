<?php

namespace App\Filament\Resources\KeychainResource\Pages;

use App\Filament\Resources\KeychainResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditKeychain extends EditRecord
{
    protected static string $resource = KeychainResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
