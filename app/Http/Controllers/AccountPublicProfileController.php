<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\AccountPublicProfile;
use App\Models\Language;
use App\Support\AccountBusinessTypeGate;
use App\Support\TenantWebsiteHostParser;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\ValidationException;
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
            'homeBackgroundSlides' => $this->slidesInSubmittedOrder($account->homeBackgroundSlides()),
            'logoUrl' => $account->portalLogoPath(),
            'logoOnWhiteUrl' => $account->portalLogoOnWhitePath(),
            'publicWebsiteUrl' => TenantWebsiteHostParser::publicWebsiteUrlForAccount($account),
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

        $logoMimes = implode(',', Account::LOGO_MIME_TYPES);
        $rules = [
            'video_url' => ['nullable', 'string', 'max:500', 'url'],
            'business_hours' => ['nullable', 'string', 'max:120'],
            'home_background' => ['nullable', 'array', 'max:'.Account::HOME_BACKGROUND_MAX_SLIDES],
            'home_background.*' => ['file', 'mimetypes:'.implode(',', Account::HOME_BACKGROUND_MIME_TYPES), 'max:'.Account::MEDIA_MAX_HOME_BACKGROUND_KB],
            'home_background_order' => ['nullable', 'array'],
            'home_background_order.*' => ['integer'],
            'remove_home_background' => ['nullable', 'array'],
            'remove_home_background.*' => ['integer'],
            'portal_logo' => ['nullable', 'file', 'mimetypes:'.$logoMimes, 'max:'.Account::MEDIA_MAX_LOGO_KB],
            'remove_portal_logo' => ['nullable', 'boolean'],
            'portal_logo_on_white' => ['nullable', 'file', 'mimetypes:'.$logoMimes, 'max:'.Account::MEDIA_MAX_LOGO_KB],
            'remove_portal_logo_on_white' => ['nullable', 'boolean'],
        ];
        $attributes = [
            'video_url' => __('public_profile.video_url'),
            'business_hours' => __('public_profile.business_hours'),
            'home_background' => __('public_profile.home_background'),
            'portal_logo' => __('public_profile.logo'),
            'portal_logo_on_white' => __('public_profile.logo_on_white'),
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

        $this->syncHomeSlides($account, $request);
        $this->syncSingleFile(
            $account,
            $request,
            'portal_logo',
            Account::MEDIA_COLLECTION_LOGO,
            fn (UploadedFile $file): string => Account::logoFileName($file),
        );
        $this->syncSingleFile(
            $account,
            $request,
            'portal_logo_on_white',
            Account::MEDIA_COLLECTION_LOGO_ON_WHITE,
            fn (UploadedFile $file): string => Account::logoFileName($file),
        );

        return redirect()
            ->route('account.public-profile.edit')
            ->with('status', __('public_profile.updated'));
    }

    /**
     * Keep the slides the operator left, in the submitted order, then append new files.
     */
    private function syncHomeSlides(Account $account, Request $request): void
    {
        $owned = $account->getMedia(Account::MEDIA_COLLECTION_HOME_BACKGROUND)
            ->keyBy(fn ($media): int => (int) $media->getKey());
        $ownedIds = $owned->keys()->map(fn ($id): int => (int) $id)->all();

        $removeIds = $this->integerList($request->input('remove_home_background'));
        $removeIds = array_values(array_intersect($removeIds, $ownedIds));

        $orderIds = $this->integerList($request->input('home_background_order'));
        $orderIds = array_values(array_filter(
            $orderIds,
            fn (int $id): bool => in_array($id, $ownedIds, true) && ! in_array($id, $removeIds, true),
        ));

        foreach ($ownedIds as $id) {
            if (! in_array($id, $removeIds, true) && ! in_array($id, $orderIds, true)) {
                $orderIds[] = $id;
            }
        }

        $uploads = $request->file('home_background', []);
        if ($uploads instanceof UploadedFile) {
            $uploads = [$uploads];
        }
        $uploads = array_values(array_filter(
            is_array($uploads) ? $uploads : [],
            fn ($file): bool => $file instanceof UploadedFile,
        ));

        if (count($orderIds) + count($uploads) > Account::HOME_BACKGROUND_MAX_SLIDES) {
            throw ValidationException::withMessages([
                'home_background' => __('public_profile.home_background_max', [
                    'max' => Account::HOME_BACKGROUND_MAX_SLIDES,
                ]),
            ]);
        }

        foreach ($removeIds as $id) {
            $owned->get($id)?->delete();
        }

        $newIds = [];
        foreach ($uploads as $upload) {
            $media = $account->addMedia($upload)
                ->usingFileName(Account::homeBackgroundFileName($upload))
                ->toMediaCollection(Account::MEDIA_COLLECTION_HOME_BACKGROUND);
            $newIds[] = (int) $media->getKey();
        }

        $orderedIds = array_merge($orderIds, $newIds);
        if ($orderedIds !== []) {
            $account->getMediaModel()::setNewOrder($orderedIds);
        }
    }

    /**
     * After a failed save, show the slides in the order that was submitted.
     *
     * @param  list<array{id: int, url: string}>  $slides
     * @return list<array{id: int, url: string}>
     */
    private function slidesInSubmittedOrder(array $slides): array
    {
        $submitted = old('home_background_order');
        if (! is_array($submitted) || $slides === []) {
            return $slides;
        }

        $byId = [];
        foreach ($slides as $slide) {
            $byId[$slide['id']] = $slide;
        }

        $ordered = [];
        foreach ($this->integerList($submitted) as $id) {
            if (! isset($byId[$id])) {
                continue;
            }

            $ordered[] = $byId[$id];
            unset($byId[$id]);
        }

        return array_merge($ordered, array_values($byId));
    }

    /**
     * @return list<int>
     */
    private function integerList(mixed $value): array
    {
        if (! is_array($value)) {
            return [];
        }

        $ids = [];
        foreach ($value as $item) {
            if (is_numeric($item)) {
                $ids[] = (int) $item;
            }
        }

        return $ids;
    }

    /**
     * Replace or clear one single-file media collection.
     * An uploaded file wins over the remove checkbox.
     *
     * @param  callable(UploadedFile): string  $fileName
     */
    private function syncSingleFile(Account $account, Request $request, string $field, string $collection, callable $fileName): void
    {
        $upload = $request->file($field);

        if ($upload instanceof UploadedFile) {
            $account->addMedia($upload)
                ->usingFileName($fileName($upload))
                ->toMediaCollection($collection);

            return;
        }

        if ($request->boolean('remove_'.$field)) {
            $account->clearMediaCollection($collection);
        }
    }
}
