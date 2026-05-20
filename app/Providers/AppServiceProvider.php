<?php

namespace App\Providers;

use App\Filament\Forms\Components\Actions\HttpSafeCopyAction;
use App\Models\AccountNotification;
use App\Models\Language;
use App\Models\User;
use App\Support\Filament\CopyableTextCell;
use Closure;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\File;
use Illuminate\Support\HtmlString;
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
            $view->with('languages', Language::with('locale')->get());
        });

        $this->registerFilamentCopyMacros();

        View::composer(['layouts.partials.dashboard-navbar'], function ($view): void {
            /** @var User|null $user */
            $user = auth()->user();
            if (! $user instanceof User) {
                $view->with('accountNavbarNotifications', collect());
                $view->with('accountNavbarUnreadNotificationsCount', 0);

                return;
            }

            $accountId = $user->currentAccountId();
            if ($accountId === null) {
                $view->with('accountNavbarNotifications', collect());
                $view->with('accountNavbarUnreadNotificationsCount', 0);

                return;
            }

            $items = AccountNotification::query()
                ->forAccount($accountId)
                ->visibleToUser($user)
                ->unread()
                ->latest()
                ->with('createdByUser')
                ->limit(7)
                ->get();

            $unreadCount = AccountNotification::query()
                ->forAccount($accountId)
                ->visibleToUser($user)
                ->unread()
                ->count();

            $view->with('accountNavbarNotifications', $items);
            $view->with('accountNavbarUnreadNotificationsCount', $unreadCount);
        });
    }

    /**
     * Prototype helpers: HTTP-safe copy (same idea as Filament's copyable(), without requiring HTTPS).
     */
    protected function registerFilamentCopyMacros(): void
    {
        if (! class_exists(TextInput::class) || ! class_exists(TextColumn::class)) {
            return;
        }

        if (! TextInput::hasMacro('copyableWithHttpFallback')) {
            TextInput::macro('copyableWithHttpFallback', function (
                bool | Closure $condition = true,
                string | Closure | null $copyMessage = null,
                int | Closure | null $copyMessageDuration = null,
            ): TextInput {
                /** @var TextInput $this */
                $this->suffixAction(
                    HttpSafeCopyAction::make()
                        ->copyMessage($copyMessage)
                        ->copyMessageDuration($copyMessageDuration)
                        ->visible($condition),
                );

                return $this;
            });
        }

        if (! TextColumn::hasMacro('httpSafeCopyableUsing')) {
            TextColumn::macro('httpSafeCopyableUsing', function (Closure $resolveCopyText): TextColumn {
                /** @var TextColumn $this */
                return $this->formatStateUsing(function ($state, $record) use ($resolveCopyText): HtmlString {
                    return CopyableTextCell::span((string) $resolveCopyText($record, $state));
                });
            });
        }
    }
}
