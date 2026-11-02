<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationGroup = 'Studio'; 
    protected static ?int $navigationSort = -1; 

    public static function shouldRegisterNavigation(): bool
    {
        // Sembunyikan menu Dashboard untuk role event dan event_viewer
        if (auth()->user()?->hasAnyRole(['event', 'event_viewer'])) {
            return false;
        }

        return parent::shouldRegisterNavigation();
    }

    public function mount(): void
    {
        // Jika akun event atau event_viewer masuk ke dashboard, otomatis alihkan ke Register Photobooth
        if (auth()->user()?->hasAnyRole(['event', 'event_viewer'])) {
            redirect()->to(\App\Filament\Resources\TicketingResource::getUrl('index'));
        }
    }
}
