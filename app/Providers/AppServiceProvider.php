<?php

namespace App\Providers;

use App\Models\Language;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

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
        View::addNamespace('site', resource_path('site/resources/views'));

        // Override Filament panel views (e.g. sidebar) so translation keys for nav groups are resolved
        View::prependNamespace('filament-panels', resource_path('views/vendor/filament-panels'));

        View::composer('layouts.partials.navbar', function ($view) {
            $view->with('languages', Language::with('locale')->orderBy('id')->get());
        });
    }
}
