<?php

namespace App\Filament\Widgets\Concerns;

use App\Filament\Actions\OpenWebsiteImpersonationAction;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Support\Enums\IconSize;
use Illuminate\Database\Eloquent\Model;

trait InteractsWithUserImpersonation
{
    public function openWebsiteImpersonationAction(): Action
    {
        return OpenWebsiteImpersonationAction::make()
            ->iconButton()
            ->iconSize(IconSize::Small);
    }

    public function resolveImpersonationDefaultActionRecord(Action $action): ?Model
    {
        if ($action->getName() !== 'openWebsiteImpersonation') {
            return null;
        }

        $record = $action->getArguments()['record'] ?? null;

        if ($record instanceof User) {
            return $record;
        }

        if (is_numeric($record)) {
            return User::query()->find((int) $record);
        }

        return null;
    }

    /**
     * @return class-string<Model>|null
     */
    public function resolveImpersonationDefaultActionModel(Action $action): ?string
    {
        if ($action->getName() !== 'openWebsiteImpersonation') {
            return null;
        }

        return User::class;
    }

    public function canImpersonateUsers(): bool
    {
        $admin = Filament::auth()->user();

        return $admin instanceof User && $admin->belongsToPlatformAccount();
    }

    public function canImpersonateUser(User $user): bool
    {
        $admin = Filament::auth()->user();

        if (! $admin instanceof User || ! $admin->belongsToPlatformAccount()) {
            return false;
        }

        if ((int) $admin->id === (int) $user->id || $user->belongsToPlatformAccount()) {
            return false;
        }

        return true;
    }
}
