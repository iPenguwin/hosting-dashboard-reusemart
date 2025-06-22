<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use App\Filament\Penitip\Pages\Auth\Login;
use App\Filament\Penitip\Pages\Auth\RequestPasswordReset;
use App\Filament\Penitip\Pages\Auth\ResetPassword;
use App\Filament\Resources\BarangResource;

class PenitipPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('penitip')
            ->path('penitip')
            ->authGuard('penitip')
            ->authPasswordBroker('penitip')
            ->login(Login::class)
            ->passwordReset(RequestPasswordReset::class, ResetPassword::class)
            ->colors([
                'primary' => '#3C686A',
            ])
            ->brandName('Penitip ReuseMart')
            ->discoverResources(in: app_path('Filament/Penitip/Resources'), for: 'App\\Filament\\Penitip\\Resources')
            ->discoverPages(in: app_path('Filament/Penitip/Pages'), for: 'App\\Filament\\Penitip\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->sidebarCollapsibleOnDesktop()
            ->discoverWidgets(in: app_path('Filament/Penitip/Widgets'), for: 'App\\Filament\\Penitip\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                // Widgets\FilamentInfoWidget::class,
            ])
            ->resources([
                BarangResource::class,
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
            ])
            ->databaseNotifications()
            ->databaseNotificationsPolling('2s')
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
