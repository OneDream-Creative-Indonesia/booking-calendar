<?php

namespace App\Filament\Resources\FrameSettingResource\Pages;

use App\Filament\Resources\FrameSettingResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFrameSettings extends ListRecords
{
    protected static string $resource = FrameSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
                 Actions\Action::make('buka_frame_manager')
                ->label('Buka Frame Manager') // Anda bisa mengubah teks tombolnya di sini
                ->icon('heroicon-o-window') // Menambahkan ikon agar lebih menarik
                ->url(url('/admin/frame-manager')) // Mengarahkan ke rute custom Anda
                ->color('primary')
                ->button(),
        ];
    }
}
