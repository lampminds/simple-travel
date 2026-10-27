<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\AccountPublicProfile;
use App\Models\Language;
use App\Support\AccountBusinessTypeGate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\View\View;

final class AccountPublicProfileController extends Controller
{
    public function edit(Request $request): View
    {
        $account = AccountBusinessTypeGate::assertOperatorAccount($request);
        $account->load('publicProfile.translations');

        $languages = Language::query()->with('locale')->get();
        $profile = $account->publicProfile;
        $translations = [];
        foreach ($languages as $language) {
            $row = $profile?->translations->firstWhere('language_id', $language->id);
            $translations[$language->id] = [
                'title' => $row?->title ?? '',
                'subtitle' => $row?->subtitle ?? '',
                'about' => $row?->about ?? '',
            ];
        }

        return view('account.public-profile', [
            'account' => $account,
            'languages' => $languages,
            'videoUrl' => $profile?->video_url ?? '',
            'businessHours' => $profile?->business_hours ?? '',
            'homeBackgroundUrl' => $account->homeBackgroundUrl(),
            'translations' => $translations,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $account = AccountBusinessTypeGate::assertOperatorAccount($request);
        $languages = Language::query()->with('locale')->get();

        $videoUrl = trim((string) $request->input('video_url'));
        $request->merge([
            'video_url' => $videoUrl !== '' ? $videoUrl : null,
            'business_hours' => trim((string) $request->input('business_hours')) ?: null,
        ]);

        $rules = [
            'video_url' => ['nullable', 'string', 'max:500', 'url'],
            'business_hours' => ['nullable', 'string', 'max:120'],
            'home_background' => ['nullable', 'file', 'mimetypes:'.implode(',', Account::HOME_BACKGROUND_MIME_TYPES), 'max:'.Account::MEDIA_MAX_HOME_BACKGROUND_KB],
            'remove_home_background' => ['nullable', 'boolean'],
        ];
        $attributes = [
            'video_url' => __('public_profile.video_url'),
            'business_hours' => __('public_profile.business_hours'),
            'home_background' => __('public_profile.home_background'),
        ];

        foreach ($languages as $language) {
            $prefix = 'translations.'.$language->id;
            $rules[$prefix.'.title'] = ['nullable', 'string', 'max:255'];
            $rules[$prefix.'.subtitle'] = ['nullable', 'string', 'max:255'];
            $rules[$prefix.'.about'] = ['nullable', 'string', 'max:60000'];
            $label = $language->display_name;
            $attributes[$prefix.'.title'] = $label.' — '.__('public_profile.title');
            $attributes[$prefix.'.subtitle'] = $label.' — '.__('public_profile.subtitle');
            $attributes[$prefix.'.about'] = $label.' — '.__('public_profile.about');
        }

        $validated = $request->validate($rules, [], $attributes);

        AccountPublicProfile::syncForAccount($account, [
            'video_url' => $validated['video_url'] ?? null,
            'business_hours' => $validated['business_hours'] ?? null,
            'translations' => is_array($validated['translations'] ?? null) ? $validated['translations'] : [],
        ]);

        $upload = $request->file('home_background');

        if ($upload instanceof UploadedFile) {
            $account->addMedia($upload)
                ->usingFileName(Account::homeBackgroundFileName($upload))
                ->toMediaCollection(Account::MEDIA_COLLECTION_HOME_BACKGROUND);
        } elseif ($request->boolean('remove_home_background')) {
            $account->clearMediaCollection(Account::MEDIA_COLLECTION_HOME_BACKGROUND);
        }

        return redirect()
            ->route('account.public-profile.edit')
            ->with('status', __('public_profile.updated'));
    }
}
