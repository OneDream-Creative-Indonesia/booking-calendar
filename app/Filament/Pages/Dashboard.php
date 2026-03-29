<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    // Tulis nama grup yang Anda inginkan di sini
    protected static ?string $navigationGroup = 'Studio'; 
    
    // Opsional: Jika ingin mengganti urutannya di dalam grup
    protected static ?int $navigationSort = -1; 
}