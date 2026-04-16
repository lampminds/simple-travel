<?php

namespace App\Http\Controllers;

use App\Support\CurrentAccountSession;
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

        CurrentAccountSession::put($request, $request->user(), (int) $accountId);

        app(PermissionRegistrar::class)->setPermissionsTeamId($accountId);

        $redirectTo = $request->input('redirect_to');
        if (is_string($redirectTo) && str_starts_with($redirectTo, '/')) {
            return redirect()->to($redirectTo);
        }

        return redirect()
            ->back()
            ->with('status', __('account.switch_success'));
    }
}
