<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Profile edit form (name, email, password).
     */
    public function edit(Request $request): View
    {
        return view('account.profile', [
            'user' => $request->user()->load('media'),
        ]);
    }

    /**
     * Store or replace the profile image (Spatie media collection "avatar").
     */
    public function updateAvatar(Request $request): RedirectResponse
    {
        $request->validateWithBag('avatar', [
            'avatar' => ['required', 'image', 'max:2048'],
        ]);

        $request->user()
            ->addMediaFromRequest('avatar')
            ->toMediaCollection('avatar');

        return redirect()
            ->route('account.profile.edit')
            ->with('status', __('profile.avatar_updated'));
    }

    /**
     * Remove the uploaded avatar; UI falls back to DiceBear.
     */
    public function destroyAvatar(Request $request): RedirectResponse
    {
        $request->user()->clearMediaCollection('avatar');

        return redirect()
            ->route('account.profile.edit')
            ->with('status', __('profile.avatar_removed'));
    }

    /**
     * Update name and email. If the email changes, verification is reset and a new link is sent.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validateWithBag('profile', [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
        ]);

        $emailChanged = $user->email !== $validated['email'];

        $user->fill([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        if ($emailChanged) {
            $user->email_verified_at = null;
        }

        $user->save();

        if ($emailChanged) {
            $user->sendEmailVerificationNotification();

            return redirect()
                ->route('verification.notice')
                ->with('status', __('profile.email_changed_verify'));
        }

        return redirect()
            ->route('account.profile.edit')
            ->with('status', __('profile.updated'));
    }

    /**
     * Update password (requires current password).
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validateWithBag('password', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'confirmed', 'min:8'],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()
            ->route('account.profile.edit')
            ->with('status', __('profile.password_updated'));
    }
}
