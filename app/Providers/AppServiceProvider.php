<?php

namespace App\Providers;

use App\Models\Language;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\File;

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
        // Looks for all migrations in the database/migrations directory, excluding those in a wip subdirectory
        $basePath = database_path('migrations');

        $directories = collect(File::allDirectories($basePath))
            ->reject(fn ($dir) => str_contains($dir, DIRECTORY_SEPARATOR . 'wip'));

        foreach ($directories as $dir) {
            $this->loadMigrationsFrom($dir);
        }


        View::addNamespace('site', resource_path('site/resources/views'));

        // Override Filament panel views (e.g. sidebar) so translation keys for nav groups are resolved
        View::prependNamespace('filament-panels', resource_path('views/vendor/filament-panels'));

        View::composer(['layouts.partials.navbar', 'layouts.partials.dashboard-navbar'], function ($view) {
            $view->with('languages', Language::with('locale')->orderBy('id')->get());
        });
    }
}
