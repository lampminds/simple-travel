<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Account;
use App\Models\AccountPerson;
use App\Models\AccountRelationship;
use App\Models\ContactType;
use App\Models\Person;
use App\Models\PersonContactMethod;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * Public contacts visible across approved commercial relationships (does not use account_contact_links).
 */
final class AccountPublicContactsService
{
    private ?int $emailContactTypeId = null;

    /**
     * Account IDs linked to the viewer via approved relationships (both directions).
     *
     * @return array<int, int>
     */
    public function counterpartAccountIds(int $viewerAccountId): array
    {
        $ids = [];

        $rows = AccountRelationship::query()
            ->where('status', AccountRelationship::STATUS_APPROVED)
            ->where(function (Builder $query) use ($viewerAccountId): void {
                $query->where('operator_account_id', $viewerAccountId)
                    ->orWhere('provider_account_id', $viewerAccountId);
            })
            ->get(['operator_account_id', 'provider_account_id']);

        foreach ($rows as $row) {
            $operatorId = (int) $row->operator_account_id;
            $providerId = (int) $row->provider_account_id;
            $counterpartId = $operatorId === $viewerAccountId ? $providerId : $operatorId;
            if ($counterpartId !== $viewerAccountId) {
                $ids[$counterpartId] = $counterpartId;
            }
        }

        return array_values($ids);
    }

    /**
     * Whether the viewer may see a public contact row on a counterpart account.
     */
    public function viewerCanAccessAccountPerson(int $viewerAccountId, AccountPerson $accountPerson): bool
    {
        if (! $accountPerson->is_active || ! $accountPerson->is_public_contact) {
            return false;
        }

        return in_array((int) $accountPerson->account_id, $this->counterpartAccountIds($viewerAccountId), true);
    }

    /**
     * @return Collection<int, array{account: Account, contacts: Collection<int, array{
     *     account_person: AccountPerson,
     *     person: Person,
     *     email: ?string,
     *     is_preferred: bool
     * }>}>
     */
    public function groupedByCounterpartAccount(int $viewerAccountId): Collection
    {
        $counterpartIds = $this->counterpartAccountIds($viewerAccountId);
        if ($counterpartIds === []) {
            return collect();
        }

        $memberships = AccountPerson::query()
            ->whereIn('account_id', $counterpartIds)
            ->where('is_active', true)
            ->where('is_public_contact', true)
            ->with([
                'person.contactMethods.contactType',
                'person.users',
                'account',
                'department.translations.language',
                'position.translations.language',
            ])
            ->orderByDesc('is_preferred_contact_mode')
            ->orderByDesc('is_primary')
            ->orderBy('id')
            ->get();

        return $memberships
            ->groupBy('account_id')
            ->map(function (Collection $rows, int $accountId): array {
                /** @var AccountPerson $first */
                $first = $rows->first();
                $account = $first->account;

                $contacts = $rows->map(function (AccountPerson $accountPerson): array {
                    $person = $accountPerson->person;

                    return [
                        'account_person' => $accountPerson,
                        'person' => $person,
                        'email' => $person instanceof Person ? $this->primaryEmailForPerson($person) : null,
                        'is_preferred' => (bool) $accountPerson->is_preferred_contact_mode,
                    ];
                })->values();

                return [
                    'account' => $account,
                    'contacts' => $contacts,
                ];
            })
            ->sortBy(fn (array $group): string => $this->accountDisplayName($group['account']))
            ->values();
    }

    public function primaryEmailForPerson(Person $person): ?string
    {
        $emailTypeId = $this->emailContactTypeId();
        if ($emailTypeId === null) {
            return null;
        }

        if (! $person->relationLoaded('contactMethods')) {
            $person->load(['contactMethods.contactType']);
        }

        $emails = $person->contactMethods
            ->filter(fn (PersonContactMethod $method): bool => (int) $method->contact_type_id === $emailTypeId)
            ->sortByDesc(fn (PersonContactMethod $method): int => $method->is_primary ? 1 : 0)
            ->values();

        $first = $emails->first();

        return $first !== null ? trim((string) $first->value) : null;
    }

    public function accountDisplayName(?Account $account): string
    {
        if (! $account instanceof Account) {
            return '';
        }

        return (string) ($account->commercial_name ?? $account->name ?? $account->nick ?? ('#'.$account->id));
    }

    private function emailContactTypeId(): ?int
    {
        if ($this->emailContactTypeId !== null) {
            return $this->emailContactTypeId;
        }

        $id = ContactType::query()
            ->where('active', true)
            ->where('code', 'email')
            ->value('id');

        $this->emailContactTypeId = $id !== null ? (int) $id : 0;

        return $this->emailContactTypeId > 0 ? $this->emailContactTypeId : null;
    }
}
