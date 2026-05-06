<?php

namespace App\Listeners;

use App\Models\User;
use App\Services\SyncPersonPrimaryEmailFromUserService;
use Illuminate\Auth\Events\Verified;

/**
 * When a user verifies their login email, mirror it onto linked persons as the primary email channel.
 */
final class SyncVerifiedEmailToPersonContactMethods
{
    public function __construct(
        private readonly SyncPersonPrimaryEmailFromUserService $syncService,
    ) {
    }

    public function handle(Verified $event): void
    {
        $user = $event->user;
        if (! $user instanceof User) {
            return;
        }

        $this->syncService->sync($user);
    }
}
