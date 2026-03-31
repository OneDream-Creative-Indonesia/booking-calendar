<?php

namespace App\Providers\Filament;

use Filament\Pages;
use Filament\Panel;
use Filament\Widgets;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Http\Middleware\Authenticate;
use App\Filament\Widgets\AccountRewriteWidgetw;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Filament\Enums\ThemeMode;
use Filament\View\PanelsRenderHook;
use Filament\Navigation\NavigationItem;
class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->favicon(asset('img/logo/Logo Snapfun-01.svg'))
            ->brandLogo(asset('img/logo/Logo Snapfun-01.svg'))
            ->darkModeBrandLogo(asset('img/logo/Logo Snapfun-02.png'))
            ->colors([
                'primary' => '#01013D',
            ])
            ->brandLogoHeight('7rem')
            ->defaultThemeMode(ThemeMode::Dark)
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                \App\Filament\Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                AccountRewriteWidgetw::class,
                // Widgets\AccountWidget::class,
                // Widgets\FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
                \Hasnayeen\Themes\Http\Middleware\SetTheme::class
            ])
            ->navigationItems([
                NavigationItem::make('⁠Waiting List') 
                    ->url('https://snapfunstudio.id/antrian', shouldOpenInNewTab: true) 
                    ->icon('heroicon-o-queue-list') 
                    ->group('Photobooth')
                    ->sort(2),
                NavigationItem::make('Photo Edit')
                    ->url('https://snapfunstudio.id/edit', shouldOpenInNewTab: true)
                    ->icon('heroicon-o-paint-brush') 
                    ->group('Studio')
                    ->sort(3),
                NavigationItem::make('Photo Link')
                    ->url('https://snapfunstudio.id/photo-link', shouldOpenInNewTab: true)
                    ->icon('heroicon-o-camera') 
                    ->group('Studio')
                    ->sort(3),

            ])
            ->navigationGroups([
                'Studio', 
                'Photobooth',        
                'Operational',
                'Settings',        
            ])
            ->authMiddleware([
                Authenticate::class,
            ])->plugins([
                \BezhanSalleh\FilamentShield\FilamentShieldPlugin::make(),
                \Hasnayeen\Themes\ThemesPlugin::make(),
                \Saade\FilamentFullCalendar\FilamentFullCalendarPlugin::make()
                ->selectable()
                ->editable()
                ->timezone('Asia/Jakarta')
                ->locale('id')
                ->plugins([])
                ->config([]),
            ])
            ->renderHook(
                'panels::auth.login.form.after',
                fn () => view('auth.socialite.google')
            );
    }
}
