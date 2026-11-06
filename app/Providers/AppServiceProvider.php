<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // URL::forceScheme('https');

        // Pastikan disk 'web_public' dan 'public' menggunakan domain & skema aktif saat diakses via HTTP
        // agar preview/hapus/upload gambar FilePond di Filament CMS selalu berfungsi di produksi maupun lokal.
        try {
            if (! $this->app->runningInConsole() && request()->getHost()) {
                $baseUrl = rtrim(request()->getSchemeAndHttpHost(), '/');
                config([
                    'filesystems.disks.web_public.url' => $baseUrl,
                    'filesystems.disks.public.url' => $baseUrl . '/storage',
                ]);
            }
        } catch (\Throwable $e) {
        }

        // Jika terdapat file route cache lama di server produksi yang belum memuat route 'site.home',
        // hapus file cache tersebut secara otomatis agar '/' tidak lagi mengarah ke '/booking'.
        try {
            if ($this->app->routesAreCached() && \Illuminate\Support\Facades\Route::getRoutes()->getByName('site.home') === null) {
                @unlink($this->app->getCachedRoutesPath());
            }
        } catch (\Throwable $e) {
        }
    }
}
