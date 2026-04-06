<?php

namespace App\Http\Controllers;

use App\Http\Middleware\SetPermissionsTeamForRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Spatie\Permission\PermissionRegistrar;

class AccountSwitchController extends Controller
{
    /**
     * Set the active account (Spatie team) for this session.
     */
    public function __invoke(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'account_id' => ['required', 'integer'],
        ]);

        $accountId = $validated['account_id'];

        if (! $request->user()->belongsToAccount($accountId)) {
            return redirect()
                ->back()
                ->withErrors(['account' => __('account.switch_forbidden')]);
        }

        $request->session()->put(SetPermissionsTeamForRequest::SESSION_CURRENT_ACCOUNT_ID, $accountId);

        app(PermissionRegistrar::class)->setPermissionsTeamId($accountId);

        return redirect()
            ->back()
            ->with('status', __('account.switch_success'));
    }
}
