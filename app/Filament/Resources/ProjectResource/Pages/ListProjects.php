<?php
namespace App\Filament\Resources\ProjectResource\Pages;

use App\Filament\Resources\ProjectResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Widgets\DashboardStats;

class ListProjects extends ListRecords
{
    protected static string $resource = ProjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Buat Proyek Baru')
                ->icon('heroicon-o-plus'),
        ];
    }

    // Memasukkan widget ke halaman List
    protected function getHeaderWidgets(): array
    {
        return [
            DashboardStats::class,
        ];
    }
}