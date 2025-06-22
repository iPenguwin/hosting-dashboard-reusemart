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
use App\Filament\Pegawai\Pages\Auth\Login;
use App\Filament\Pegawai\Pages\Auth\RequestPasswordReset;
use App\Filament\Pegawai\Pages\Auth\ResetPassword;
use App\Models\Pegawai;
use Filament\Navigation\NavigationGroup;

class PegawaiPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('pegawai')
            ->path('pegawai')
            ->authGuard('pegawai')
            ->authPasswordBroker('pegawai')
            ->login(Login::class)
            // ->login()
            ->brandName('Pegawai ReuseMart')
            ->passwordReset(RequestPasswordReset::class, ResetPassword::class)
            ->colors([
                'primary' => '#3C686A',
            ])
            ->discoverResources(in: app_path('Filament/Pegawai/Resources'), for: 'App\\Filament\\Pegawai\\Resources')
            ->discoverPages(in: app_path('Filament/Pegawai/Pages'), for: 'App\\Filament\\Pegawai\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->sidebarCollapsibleOnDesktop()
            ->resources([
                \App\Filament\Resources\BarangResource::class,
                \App\Filament\Resources\PenitipResource::class,
                \App\Filament\Resources\KategoribarangResource::class,
                // \App\Filament\Resources\RequestResource::class,
                \App\Filament\Resources\TransaksiPenitipanBarangResource::class,
                \App\Filament\Resources\TransaksiPembelianBarangResource::class,
                \App\Filament\Resources\DetailTransaksiPenitipBarangResource::class,
                \App\Filament\Resources\PegawaiTransaksiPenitipanResource::class,
                \App\Filament\Resources\PegawaiTransaksiPembelianResource::class,
                \App\Filament\Resources\RequestResource::class,

            ])
            ->discoverWidgets(in: app_path('Filament/Pegawai/Widgets'), for: 'App\\Filament\\Pegawai\\Widgets')
            ->widgets([
                \App\Filament\Widgets\Account\PegawaiAccountWidget::class,
                // Widgets\AccountWidget::class,
                // Widgets\FilamentInfoWidget::class,
                \App\Filament\Widgets\PenitipanStatsOverview::class,
                \App\Filament\Widgets\PenitipanBarangChart::class,
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
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
