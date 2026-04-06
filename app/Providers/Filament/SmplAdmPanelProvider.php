<?php

namespace App\Providers\Filament;

use App\Filament\Resources\Settings\ParameterResource;
use App\Filament\Widgets\LatestSignupsWidget;
use App\Http\Controllers\Filament\SetPanelLocaleController;
use App\Http\Middleware\SetLocaleFromSession;
use App\Models\Language;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Route;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class SmplAdmPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('smpl_adm')
            ->path('smpl_adm')
            ->login()
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->resources([
                ParameterResource::class,
            ])
            ->navigationGroups([
                NavigationGroup::make(__('filament.resources.nav_contacts')),
                NavigationGroup::make(__('filament.resources.nav_plans')),
                NavigationGroup::make(__('filament.resources.nav_services')),
                NavigationGroup::make(__('filament.resources.nav_hotels')),
                NavigationGroup::make(__('filament.resources.nav_excursions')),
                NavigationGroup::make(__('filament.resources.nav_gastronomy')),
                NavigationGroup::make(__('filament.resources.nav_parameters')),
                NavigationGroup::make(__('filament.resources.nav_users')),
                NavigationGroup::make(__('filament.resources.nav_authorization')),
            ])
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                LatestSignupsWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                SetLocaleFromSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->authenticatedRoutes(function (Panel $panel): void {
                Route::get('locale/{language}', SetPanelLocaleController::class)
                    ->name('locale');
            })
            ->renderHook(PanelsRenderHook::USER_MENU_BEFORE, function (): string {
                $languages = Language::with('locale')->orderBy('id')->get();

                return view('filament.components.language-switcher', [
                    'languages' => $languages,
                ])->render();
            });
    }
}
