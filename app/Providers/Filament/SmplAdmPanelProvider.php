<?php

namespace App\Providers\Filament;

use App\Http\Controllers\Filament\SetPanelLocaleController;
use App\Http\Middleware\SetLocaleFromSession;
use App\Models\Language;
use Filament\Navigation\NavigationItem;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
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
            ->sidebarFullyCollapsibleOnDesktop()
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverClusters(in: app_path('Filament/Clusters'), for: 'App\Filament\Clusters')
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->navigationItems([
                NavigationItem::make('back-to-site')
                    ->label(fn (): string => __('filament.panel.back_to_site'))
                    ->url(fn (): string => route('home'))
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->sort(999),
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
            ->renderHook(PanelsRenderHook::HEAD_END, fn (): string => view(
                'filament.styles.filament-help-tooltip',
            )->render().view(
                'filament.styles.cluster-sub-navigation-toggle',
            )->render())
            ->renderHook(PanelsRenderHook::PAGE_HEADER_ACTIONS_BEFORE, fn (): string => view(
                'filament.components.cluster-sub-navigation-toggle',
            )->render())
            ->renderHook(PanelsRenderHook::SCRIPTS_BEFORE, fn (): string => view(
                'filament.scripts.copy-text-to-clipboard',
            )->render())
            ->renderHook(PanelsRenderHook::SCRIPTS_AFTER, fn (): string => view(
                'filament.scripts.auto-collapse-main-sidebar-when-sub-navigation',
            )->render().view(
                'filament.scripts.cluster-sub-navigation-toggle',
            )->render())
            ->renderHook(PanelsRenderHook::USER_MENU_BEFORE, function (): string {
                $languages = Language::with('locale')->get();

                return view('filament.components.language-switcher', [
                    'languages' => $languages,
                ])->render();
            });
    }
}

