<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\AccountNotification;
use App\Services\AccountNotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class AccountNotificationsController extends Controller
{
    /**
     * List shared notifications for the active account.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        abort_unless($user !== null, 401);

        $account = $user->currentAccount();
        abort_unless($account instanceof Account, 404);

        $notifications = AccountNotification::query()
            ->forAccount($account->id)
            ->visibleToUser($user)
            ->latest()
            ->with(['readByUser', 'recipientUser', 'createdByUser'])
            ->paginate(20);

        return view('account.notifications', [
            'account' => $account,
            'notifications' => $notifications,
            'recipientUsers' => $account->users()->orderBy('name')->get(['users.id', 'users.name']),
        ]);
    }

    /**
     * Create one notification in current account (broadcast or private).
     */
    public function store(Request $request, AccountNotificationService $notificationService): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user !== null, 401);

        $account = $user->currentAccount();
        abort_unless($account instanceof Account, 404);

        $validated = $request->validate([
            'recipient_user_id' => ['nullable', 'integer'],
            'title' => ['required', 'string', 'max:180'],
            'message' => ['required', 'string', 'max:4000'],
        ]);

        $recipientUserId = isset($validated['recipient_user_id']) && $validated['recipient_user_id'] !== ''
            ? (int) $validated['recipient_user_id']
            : null;

        if ($recipientUserId !== null) {
            $belongsToAccount = $account->users()->where('users.id', $recipientUserId)->exists();
            abort_unless($belongsToAccount, 422);
        }

        $notificationService->createForAccount(
            accountId: (int) $account->id,
            type: 'internal_message',
            title: (string) $validated['title'],
            message: (string) $validated['message'],
            recipientUserId: $recipientUserId,
            data: [
                'created_by_user_id' => $user->id,
                'created_by_user_name' => $user->name,
            ],
        );

        return back()->with('status', __('account.notifications.status_created'));
    }

    /**
     * Mark one account notification as read for allowed viewers.
     */
    public function markAsRead(Request $request, AccountNotification $notification): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user !== null, 401);

        $account = $user->currentAccount();
        abort_unless($account instanceof Account, 404);
        abort_unless((int) $notification->account_id === (int) $account->id, 404);
        abort_unless($notification->recipient_user_id === null || (int) $notification->recipient_user_id === (int) $user->id, 404);

        $notification->markAsReadBy($user);

        return back()->with('status', __('account.notifications.status_marked_as_read'));
    }
}
