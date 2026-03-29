<?php

namespace App\Filament\Resources\FrameSettingResource\Pages;

use App\Filament\Resources\FrameSettingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFrameSetting extends EditRecord
{
    protected static string $resource = FrameSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
