<?php

namespace App\Filament\Actions;

use App\Models\User;
use App\Services\WebsiteImpersonationTokenService;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\View;
use Filament\Support\Enums\TextSize;

final class OpenWebsiteImpersonationAction
{
    public static function make(): Action
    {
        return Action::make('openWebsiteImpersonation')
            ->label(__('filament.resources.user_actions.open_website_impersonation'))
            ->tooltip(__('filament.resources.user_actions.open_website_impersonation_tooltip'))
            ->icon('heroicon-o-link')
            ->modalHeading(__('filament.resources.user_actions.impersonation_modal_heading'))
            ->modalWidth('2xl')
            ->fillForm(function (Action $action): array {
                $record = $action->getRecord();
                if (! $record instanceof User) {
                    return ['error' => __('filament.resources.user_actions.impersonation_forbidden'), 'url' => ''];
                }
                $admin = Filament::auth()->user();
                if (! $admin instanceof User || ! $admin->belongsToPlatformAccount()) {
                    return ['error' => __('filament.resources.user_actions.impersonation_forbidden'), 'url' => ''];
                }
                if ((int) $admin->id === (int) $record->id || $record->belongsToPlatformAccount()) {
                    return ['error' => __('filament.resources.user_actions.impersonation_invalid_target'), 'url' => ''];
                }
                try {
                    $svc = app(WebsiteImpersonationTokenService::class);
                    $plain = $svc->createToken($record, $admin);
                    $url = $svc->urlForPlainToken($plain);

                    return ['error' => null, 'url' => $url];
                } catch (\InvalidArgumentException $e) {
                    return ['error' => $e->getMessage(), 'url' => ''];
                }
            })
            ->schema(fn (): array => [
                Text::make(fn (Get $get): string => (string) $get('error'))
                    ->color('danger')
                    ->visible(fn (Get $get): bool => filled($get('error'))),
                Text::make(__('filament.resources.user_actions.impersonation_modal_help'))
                    ->color('gray')
                    ->size(TextSize::Small)
                    ->visible(fn (Get $get): bool => blank($get('error'))),
                View::make('filament.actions.impersonation-link-field')
                    ->viewData(fn (Get $get): array => [
                        'url' => (string) $get('url'),
                    ])
                    ->visible(fn (Get $get): bool => blank($get('error'))),
                Text::make(__('filament.resources.user_actions.impersonation_copy_hint'))
                    ->color('gray')
                    ->size(TextSize::Small)
                    ->visible(fn (Get $get): bool => blank($get('error'))),
            ])
            ->modalCancelActionLabel(__('filament.common.close'))
            ->modalSubmitAction(false)
            ->action(fn () => null)
            ->visible(fn (): bool => Filament::auth()->user() instanceof User
                && Filament::auth()->user()->belongsToPlatformAccount());
    }
}
