<?php

namespace App\Services;

use App\Models\ContactType;
use App\Models\PersonContactMethod;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * After email verification, persist the user's email on each linked person as a verified,
 * primary {@see PersonContactMethod} (catalog type {@code email}).
 */
final class SyncPersonPrimaryEmailFromUserService
{
    public function sync(User $user): void
    {
        if (! $user->hasVerifiedEmail()) {
            return;
        }

        $email = Str::lower(trim((string) $user->email));
        if ($email === '') {
            return;
        }

        $emailTypeId = ContactType::query()
            ->where('code', 'email')
            ->where('active', true)
            ->value('id');

        if ($emailTypeId === null) {
            return;
        }

        $personIds = $user->persons()->pluck('persons.id')->all();
        if ($personIds === []) {
            return;
        }

        DB::transaction(function () use ($personIds, $emailTypeId, $email) {
            foreach ($personIds as $personId) {
                PersonContactMethod::query()
                    ->where('person_id', $personId)
                    ->update(['is_primary' => false]);

                PersonContactMethod::query()->updateOrCreate(
                    [
                        'person_id' => $personId,
                        'contact_type_id' => $emailTypeId,
                    ],
                    [
                        'value' => $email,
                        'is_primary' => true,
                        'is_verified' => true,
                    ],
                );
            }
        });
    }
}
