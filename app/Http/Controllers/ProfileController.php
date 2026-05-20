<?php

namespace App\Http\Controllers;

use App\Models\AccountPerson;
use App\Models\ContactDepartment;
use App\Models\ContactPosition;
use App\Models\ContactType;
use App\Models\Person;
use App\Models\PersonContactMethod;
use App\Models\TodoTask;
use App\Models\TodoTaskUserAssignment;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Access edit form (user login email and password).
     */
    public function editAccess(Request $request): View
    {
        return view('account.access', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Person profile edit form.
     */
    public function editProfile(Request $request): View
    {
        $user = $request->user();
        $profilePerson = $this->resolveProfilePerson($user);
        $accountPerson = $this->resolveCurrentAccountPerson($user, $profilePerson);

        return view('account.profile', [
            'user' => $user,
            'profilePerson' => $profilePerson,
            'accountPerson' => $accountPerson,
            'departments' => ContactDepartment::query()
                ->with(['translations.language'])
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get(),
            'positions' => ContactPosition::query()
                ->with(['translations.language'])
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get(),
        ]);
    }

    /**
     * Contact methods edit form for the profile person.
     */
    public function editContact(Request $request): View
    {
        $user = $request->user();
        $profilePerson = $this->resolveProfilePerson($user);

        $contactMethods = $profilePerson?->contactMethods()
            ->with(['contactType.translations.language'])
            ->orderByDesc('is_primary')
            ->orderBy('id')
            ->get() ?? collect();

        $protectedMethodIds = $contactMethods
            ->filter(fn (PersonContactMethod $method): bool => $this->isProtectedRegistrationEmailMethod($user, $method))
            ->pluck('id')
            ->map(fn ($id): int => (int) $id)
            ->values()
            ->all();

        return view('account.profile-contact', [
            'user' => $user,
            'profilePerson' => $profilePerson,
            'contactTypes' => $this->activeContactTypes(),
            'contactMethods' => $contactMethods,
            'protectedMethodIds' => $protectedMethodIds,
        ]);
    }

    /**
     * Update profile person contact methods.
     */
    public function updateContact(Request $request): RedirectResponse
    {
        $user = $request->user();
        $profilePerson = $this->resolveProfilePerson($user);
        if (! $profilePerson instanceof Person) {
            return redirect()
                ->route('account.contact.edit')
                ->with('status', __('profile.contact_person_missing'));
        }

        $validated = $request->validateWithBag(
            'contact',
            [
                'methods' => ['array'],
                'methods.*.id' => ['nullable', 'integer', 'exists:person_contact_methods,id'],
                'methods.*.contact_type_id' => ['nullable', 'integer', 'exists:cat_contact_types,id', 'required_with:methods.*.value'],
                'methods.*.value' => ['nullable', 'string', 'max:255', 'required_with:methods.*.contact_type_id'],
                'methods.*.delete' => ['nullable', 'boolean'],
            ],
            [
                'methods.*.contact_type_id.required_with' => __('profile.contact_type_required'),
                'methods.*.value.required_with' => __('profile.contact_value_required'),
            ]
        );

        $typesById = $this->activeContactTypes()->keyBy('id');
        $rows = $validated['methods'] ?? [];
        foreach ($rows as $row) {
            $methodId = isset($row['id']) ? (int) $row['id'] : null;
            $delete = (bool) ($row['delete'] ?? false);
            $contactTypeId = isset($row['contact_type_id']) ? (int) $row['contact_type_id'] : 0;
            $value = trim((string) ($row['value'] ?? ''));

            if ($methodId !== null) {
                $existing = $profilePerson->contactMethods()->whereKey($methodId)->first();
                if (! $existing instanceof PersonContactMethod) {
                    continue;
                }
                if ($this->isProtectedRegistrationEmailMethod($user, $existing)) {
                    continue;
                }
                if ($delete) {
                    $existing->delete();
                    continue;
                }
                if ($contactTypeId < 1 || $value === '') {
                    continue;
                }
                if (! $typesById->has($contactTypeId)) {
                    continue;
                }
                $this->assertNoDuplicateContactTypeAndValue($profilePerson, $contactTypeId, $value, $methodId, $typesById);
                $existing->update([
                    'contact_type_id' => $contactTypeId,
                    'value' => $value,
                ]);
                continue;
            }

            if ($delete || $contactTypeId < 1 || $value === '' || ! $typesById->has($contactTypeId)) {
                continue;
            }

            $this->assertNoDuplicateContactTypeAndValue($profilePerson, $contactTypeId, $value, null, $typesById);
            $profilePerson->contactMethods()->create([
                'contact_type_id' => $contactTypeId,
                'value' => $value,
                'is_primary' => false,
                'is_verified' => false,
            ]);
        }

        return redirect()
            ->route('account.contact.edit')
            ->with('status', __('profile.contact_updated'));
    }

    /**
     * Store or replace the profile image (Spatie media collection "avatar").
     */
    public function updateAvatar(Request $request): RedirectResponse
    {
        $request->validateWithBag('avatar', [
            'avatar' => ['required', 'image', 'max:2048'],
        ]);

        $person = $this->resolveProfilePerson($request->user());
        if (! $person instanceof Person) {
            return redirect()
                ->route('account.profile.edit')
                ->with('status', __('profile.contact_person_missing'));
        }

        $person
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
        $person = $this->resolveProfilePerson($request->user());
        if ($person instanceof Person) {
            $person->clearMediaCollection('avatar');
        }

        return redirect()
            ->route('account.profile.edit')
            ->with('status', __('profile.avatar_removed'));
    }

    /**
     * Update user login email (access section).
     */
    public function updateAccess(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validateWithBag('access', [
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
        ]);

        $emailChanged = $user->email !== $validated['email'];

        $user->fill([
            'email' => $validated['email'],
        ]);

        if ($emailChanged) {
            $user->email_verified_at = null;
        }

        $user->save();
        $this->registerCompleteUserProfileTaskCompletion(
            accountId: $user->currentAccountId(),
            userId: (int) $user->getKey()
        );

        if ($emailChanged) {
            $user->sendEmailVerificationNotification();

            return redirect()
                ->route('verification.notice')
                ->with('status', __('profile.email_changed_verify'));
        }

        return redirect()
            ->route('account.access.edit')
            ->with('status', __('profile.updated'));
    }

    /**
     * Update person profile data (name, department, position).
     */
    public function updateProfile(Request $request): RedirectResponse
    {
        $user = $request->user();
        $person = $this->resolveProfilePerson($user);
        $accountPerson = $this->resolveCurrentAccountPerson($user, $person);
        if (! $person instanceof Person || ! $accountPerson instanceof AccountPerson) {
            return redirect()
                ->route('account.profile.edit')
                ->with('status', __('profile.contact_person_missing'));
        }

        $validated = $request->validateWithBag('profile', [
            'name' => ['required', 'string', 'max:255'],
            'contact_department_id' => ['required', 'integer', 'exists:cat_contact_departments,id'],
            'contact_position_id' => ['required', 'integer', 'exists:cat_contact_positions,id'],
            'is_public_contact' => ['nullable', 'boolean'],
            'is_preferred_contact_mode' => ['nullable', 'boolean'],
        ]);

        $person->update([
            'name' => $validated['name'],
        ]);
        $accountPerson->update([
            'contact_department_id' => (int) $validated['contact_department_id'],
            'contact_position_id' => (int) $validated['contact_position_id'],
            'is_public_contact' => $request->boolean('is_public_contact'),
            'is_preferred_contact_mode' => $request->boolean('is_preferred_contact_mode'),
        ]);

        return redirect()
            ->route('account.profile.edit')
            ->with('status', __('profile.profile_updated'));
    }

    /**
     * Update password (requires current password).
     */
    public function updateAccessPassword(Request $request): RedirectResponse
    {
        $validated = $request->validateWithBag('password', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'confirmed', 'min:8'],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()
            ->route('account.access.edit')
            ->with('status', __('profile.password_updated'));
    }

    /**
     * Mark "complete_user_profile" as completed for the same user who updated profile.
     */
    private function registerCompleteUserProfileTaskCompletion(?int $accountId, int $userId): void
    {
        if ($accountId === null) {
            return;
        }

        $task = TodoTask::query()
            ->where('account_id', $accountId)
            ->where('code', 'complete_user_profile')
            ->first();

        if (! $task) {
            return;
        }

        $alreadyCompletedByUser = $task->userAssignments()
            ->where('user_id', $userId)
            ->where('status', 'completed')
            ->exists();

        if ($alreadyCompletedByUser) {
            return;
        }

        TodoTaskUserAssignment::query()->updateOrCreate(
            [
                'todo_task_id' => $task->id,
                'user_id' => $userId,
            ],
            [
                'status' => 'completed',
                'completed_at' => now(),
                'ignored_at' => null,
            ]
        );
    }

    /**
     * Resolve the person linked to the user for the current account context.
     */
    private function resolveProfilePerson(User $user): ?Person
    {
        $currentAccountId = $user->currentAccountId();
        $personIds = $user->persons()->pluck('persons.id');
        if ($personIds->isEmpty()) {
            return null;
        }

        if ($currentAccountId !== null) {
            $accountPerson = AccountPerson::query()
                ->where('account_id', $currentAccountId)
                ->whereIn('person_id', $personIds->all())
                ->orderByDesc('is_primary')
                ->orderBy('id')
                ->first();
            if ($accountPerson !== null) {
                return $accountPerson->person()->first();
            }
        }

        return Person::query()->whereIn('id', $personIds->all())->orderBy('id')->first();
    }

    private function resolveCurrentAccountPerson(User $user, ?Person $person): ?AccountPerson
    {
        if (! $person instanceof Person) {
            return null;
        }

        $currentAccountId = $user->currentAccountId();
        if ($currentAccountId === null) {
            return null;
        }

        return $person->accountPersons()
            ->where('account_id', $currentAccountId)
            ->orderByDesc('is_primary')
            ->orderBy('id')
            ->first();
    }

    /**
     * Active contact types for contact method editing.
     *
     * @return EloquentCollection<int, ContactType>
     */
    private function activeContactTypes(): EloquentCollection
    {
        return ContactType::query()
            ->with(['translations.language'])
            ->where('active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();
    }

    /**
     * Prevent exact duplicates by contact type + value for the same person.
     */
    private function assertNoDuplicateContactTypeAndValue(
        Person $person,
        int $contactTypeId,
        string $value,
        ?int $ignoreMethodId,
        EloquentCollection $typesById
    ): void
    {
        $normalizedValue = Str::lower(trim($value));
        if ($normalizedValue === '') {
            return;
        }

        $exists = $person->contactMethods()
            ->where('contact_type_id', $contactTypeId)
            ->whereRaw('LOWER(TRIM(value)) = ?', [$normalizedValue])
            ->when($ignoreMethodId !== null, fn ($q) => $q->where('id', '!=', $ignoreMethodId))
            ->exists();

        if ($exists) {
            $type = $typesById->get($contactTypeId);
            $typeLabel = $type instanceof ContactType ? (string) $type->code : (string) $contactTypeId;
            throw ValidationException::withMessages([
                'methods' => __('profile.contact_duplicate_type_value_error', ['type' => $typeLabel]),
            ])->errorBag('contact');
        }
    }

    /**
     * The registration/login email contact method must not be editable/deletable from contact screen.
     * We protect the primary email method, and also any email method matching current access email.
     */
    private function isProtectedRegistrationEmailMethod(User $user, PersonContactMethod $method): bool
    {
        $method->loadMissing('contactType');

        $userEmail = Str::lower(trim((string) $user->email));
        $methodValue = Str::lower(trim((string) $method->value));
        $typeCode = Str::lower(trim((string) ($method->contactType?->getRawOriginal('code') ?? $method->contactType?->code ?? '')));

        if (! in_array($typeCode, ['email', 'e-mail'], true)) {
            return false;
        }

        if ((bool) $method->is_primary) {
            return true;
        }

        if ($userEmail === '' || $methodValue === '') {
            return false;
        }

        return $methodValue === $userEmail;
    }
}
