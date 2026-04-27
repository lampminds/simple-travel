<?php

namespace App\Services;

use App\Models\TodoCategory;
use App\Models\TodoTask;

final class AccountStartupService
{
    private const SETUP_CATEGORY_CODE = 'account_setup';

    private const PLATFORM_ACCOUNT_ID = 1;

    public function __construct(
        private readonly ReplicateDefaultRolesToAccountService $replicateDefaultRolesToAccount,
        private readonly TodoCategoryCopyTasksToAccountService $copyTasksToAccountService
    ) {
    }

    /**
     * Run startup routines for a newly created account.
     *
     * @param  int  $pivotUserId  User to scope Spatie `user_model_has_*` copy for (the member completing verification).
     */
    public function runForNewAccount(int $accountId, int $pivotUserId): void
    {
        if ($accountId < 1 || $accountId === self::PLATFORM_ACCOUNT_ID || $pivotUserId < 1) {
            return;
        }

        $this->replicateDefaultRolesToAccount->replicateTo(
            $accountId,
            self::PLATFORM_ACCOUNT_ID,
            $pivotUserId
        );

        $category = TodoCategory::query()
            ->where('code', self::SETUP_CATEGORY_CODE)
            ->first();

        if (! $category) {
            return;
        }

        // Avoid duplicate startup tasks if this process is retried.
        $alreadySeeded = TodoTask::query()
            ->where('account_id', $accountId)
            ->where('todo_category_id', (int) $category->getKey())
            ->exists();

        if ($alreadySeeded) {
            return;
        }

        $this->copyTasksToAccountService->copy($category, $accountId, self::PLATFORM_ACCOUNT_ID);
    }
}
