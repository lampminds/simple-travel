<?php

namespace App\Support;

/**
 * Optional attribution context when logging AI / translation usage.
 */
final class AiUsageContext
{
    public function __construct(
        public int $userId,
        public ?int $accountId = null,
        public ?int $accountTypeId = null,
        public ?int $languageId = null,
        public string $source = 'unknown',
        public bool $useSystemAccount = false,
    ) {}

    public function resolvedAccountId(): ?int
    {
        return $this->useSystemAccount ? SystemAccount::ACCOUNT_ID : $this->accountId;
    }

    public function resolvedUserId(): int
    {
        return $this->useSystemAccount && $this->userId < 1
            ? SystemAccount::USER_ID
            : $this->userId;
    }
}
