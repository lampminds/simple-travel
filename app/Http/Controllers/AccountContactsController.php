<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\AccountPerson;
use App\Models\Person;
use App\Services\AccountNotificationService;
use App\Services\AccountPublicContactsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class AccountContactsController extends Controller
{
    public function __construct(
        private readonly AccountPublicContactsService $contacts,
        private readonly AccountNotificationService $notifications,
    ) {
    }

    public function index(Request $request): View
    {
        $account = $this->resolveCurrentAccount($request);

        return view('account.contacts.index', [
            'account' => $account,
            'groups' => $this->contacts->groupedByCounterpartAccount((int) $account->id),
        ]);
    }

    public function show(Request $request, AccountPerson $accountPerson): View
    {
        $account = $this->resolveCurrentAccount($request);
        abort_unless($this->contacts->viewerCanAccessAccountPerson((int) $account->id, $accountPerson), 404);

        $accountPerson->load([
            'person.contactMethods.contactType.translations.language',
            'person.users',
            'account',
            'department.translations.language',
            'position.translations.language',
        ]);

        $person = $accountPerson->person;
        abort_unless($person !== null, 404);

        return view('account.contacts.show', [
            'viewerAccount' => $account,
            'accountPerson' => $accountPerson,
            'person' => $person,
            'sourceAccount' => $accountPerson->account,
            'primaryEmail' => $this->contacts->primaryEmailForPerson($person),
        ]);
    }

    public function storeMessage(Request $request, AccountPerson $accountPerson): RedirectResponse
    {
        $viewerAccount = $this->resolveCurrentAccount($request);
        $user = $request->user();
        abort_unless($user !== null, 401);
        abort_unless($this->contacts->viewerCanAccessAccountPerson((int) $viewerAccount->id, $accountPerson), 404);

        $accountPerson->load(['person.users', 'account']);
        $person = $accountPerson->person;
        abort_unless($person !== null, 404);

        $targetAccount = $accountPerson->account;
        abort_unless($targetAccount instanceof Account, 404);

        $validated = $request->validate([
            'message' => ['required', 'string', 'max:4000'],
        ]);

        $recipientUserId = $this->resolveRecipientUserId($person);

        $senderLabel = $this->contacts->accountDisplayName($viewerAccount);

        $this->notifications->createForAccount(
            accountId: (int) $targetAccount->id,
            type: 'contact_message',
            title: (string) __('account.contacts.message_notification_title', [
                'company' => $senderLabel,
                'name' => $user->name,
            ]),
            message: (string) $validated['message'],
            recipientUserId: $recipientUserId,
            data: [
                'sender_account_id' => (int) $viewerAccount->id,
                'sender_account_name' => $senderLabel,
                'sender_user_id' => (int) $user->id,
                'sender_user_name' => $user->name,
                'person_id' => (int) $person->id,
                'account_person_id' => (int) $accountPerson->id,
                'created_by_user_id' => (int) $user->id,
                'created_by_user_name' => $user->name,
            ],
        );

        return redirect()
            ->route('account.contacts.show', $accountPerson)
            ->with('status', __('account.contacts.message_sent'));
    }

    private function resolveCurrentAccount(Request $request): Account
    {
        $user = $request->user();
        abort_unless($user !== null, 401);

        $account = $user->currentAccount();
        abort_unless($account instanceof Account, 404);

        return $account;
    }

    /**
     * Single linked user → private notification; otherwise account-wide.
     */
    private function resolveRecipientUserId(Person $person): ?int
    {
        $userIds = $person->users()->pluck('users.id')->map(fn ($id): int => (int) $id)->values();

        if ($userIds->count() === 1) {
            return $userIds->first();
        }

        return null;
    }
}
