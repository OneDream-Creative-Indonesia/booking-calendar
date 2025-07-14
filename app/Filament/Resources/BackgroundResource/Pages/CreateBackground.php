<?php

namespace App\Filament\Resources\BackgroundResource\Pages;

use App\Filament\Resources\BackgroundResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateBackground extends CreateRecord
{
    protected static string $resource = BackgroundResource::class;
    protected function mutateFormDataBeforeCreate(array $data): array
{
    $data['image_url'] = $data->getMedia('image')->first();
    dd($data);
    $data['user_id'] = auth()->id();

    return $data;
}
}
