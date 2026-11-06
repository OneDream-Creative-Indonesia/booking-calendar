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
