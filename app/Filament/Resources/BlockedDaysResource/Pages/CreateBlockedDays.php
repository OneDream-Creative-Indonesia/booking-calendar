<?php

namespace App\Filament\Resources\BlockedDaysResource\Pages;

use App\Filament\Resources\BlockedDaysResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateBlockedDays extends CreateRecord
{
    protected static string $resource = BlockedDaysResource::class;
    protected ?string $heading = 'Tambahkan Waktu Studio Tidak Tersedia';
}
