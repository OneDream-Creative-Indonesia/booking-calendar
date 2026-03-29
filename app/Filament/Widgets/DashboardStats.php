<?php

namespace App\Filament\Widgets;

use App\Models\Project;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Storage;

class DashboardStats extends BaseWidget
{
    protected static bool $isDiscovered = false;
    
    protected function getStats(): array
    {
        $projects = Project::all();
        
        // 1. Hitung Total File (Menghitung isi array 'photos' dari setiap proyek)
        $totalFiles = 0;
        foreach ($projects as $project) {
            if (is_array($project->photos)) {
                $totalFiles += count($project->photos);
            }
        }

        // 2. Hitung Penyimpanan Langsung dari Folder Fisik (Paling Akurat)
        $totalBytes = 0;
        $disk = Storage::disk('public');
        
        // Cek apakah folder 'project-photos' ada, lalu hitung ukuran semua file di dalamnya
        if ($disk->exists('project-photos')) {
            $files = $disk->allFiles('project-photos');
            foreach ($files as $file) {
                $totalBytes += $disk->size($file);
            }
        }
        
        // Konversi Byte ke Gigabyte (GB)
        $totalGB = number_format($totalBytes / 1073741824, 2);

        return [
            // Sesuai permintaan: Total data yang ada di tabel project
            Stat::make('LINK AKTIF', Project::count())
                ->color('primary')
                ->extraAttributes([
                    'class' => 'text-2xl font-bold text-blue-600',
                ]),
                
            Stat::make('TOTAL FILE', $totalFiles)
                ->color('success')
                ->extraAttributes([
                    'class' => 'text-2xl font-bold',
                ]),
                
            Stat::make('PENYIMPANAN TERPAKAI', $totalGB . ' GB')
                ->icon('heroicon-o-server')
                ->color('warning')
                ->extraAttributes([
                    'class' => 'text-2xl font-bold text-yellow-600',
                ]),
        ];
    }
}