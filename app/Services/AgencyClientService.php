<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\AccountPerson;
use App\Models\Address;
use App\Models\CatDocument;
use App\Models\ContactDepartment;
use App\Models\ContactPosition;
use App\Models\ContactType;
use App\Models\OrganizationPerson;
use App\Models\LmpCity;
use App\Models\Organization;
use App\Models\OrganizationAddress;
use App\Models\Person;
use App\Models\PersonContactMethod;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

final class AgencyClientService
{
    /**
     * @return array{
     *     type: string,
     *     personClients: LengthAwarePaginator|\Illuminate\Support\Collection|null,
     *     organizationClients: LengthAwarePaginator|\Illuminate\Support\Collection|null
     * }
     */
    public function indexData(int $accountId, string $type, ?string $search, int $perPage = 25): array
    {
        $type = in_array($type, ['all', 'person', 'organization'], true) ? $type : 'all';
        $search = $search !== null ? trim($search) : null;
        if ($search === '') {
            $search = null;
        }

        if ($type === 'all') {
            return [
                'type' => $type,
                'personClients' => $this->personClientsQuery($accountId, $search)->limit($perPage)->get(),
                'organizationClients' => $this->organizationClientsQuery($accountId, $search)->limit($perPage)->get(),
            ];
        }

        return [
            'type' => $type,
            'personClients' => $type === 'person'
                ? $this->personClientsQuery($accountId, $search)->paginate($perPage)->withQueryString()
                : null,
            'organizationClients' => $type === 'organization'
                ? $this->organizationClientsQuery($accountId, $search)->paginate($perPage)->withQueryString()
                : null,
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function createPersonClient(int $accountId, array $data): Person
    {
        return DB::transaction(function () use ($accountId, $data): Person {
            $person = Person::query()->create([
                'name' => trim((string) $data['name']),
                'document_name' => $this->nullableString($data['document_name'] ?? null),
                'given_name' => $this->nullableString($data['given_name'] ?? null),
                'family_name' => $this->nullableString($data['family_name'] ?? null),
                'date_of_birth' => $data['date_of_birth'] ?? null,
                'gender_id' => isset($data['gender_id']) ? (int) $data['gender_id'] : null,
                'nationality_id' => isset($data['nationality_id']) ? (int) $data['nationality_id'] : null,
            ]);

            AccountPerson::query()->create([
                'account_id' => $accountId,
                'person_id' => $person->id,
                'link_type' => AccountPerson::LINK_CLIENT,
                'contact_department_id' => null,
                'contact_position_id' => null,
                'is_primary' => false,
                'is_active' => true,
                'is_public_contact' => false,
                'is_preferred_contact_mode' => false,
            ]);

            $this->syncPersonContactChannels($person, $data);
            $this->syncPersonOrganizationLink($person, $accountId, $data);
            $this->syncPersonTaxDocuments($person, $data['tax_ids'] ?? []);

            return $person->fresh(['contactMethods.contactType']);
        });
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function updatePersonClient(Person $person, int $accountId, array $data): Person
    {
        $this->assertPersonIsClientOfAccount($person, $accountId);

        return DB::transaction(function () use ($person, $accountId, $data): Person {
            $person->update([
                'name' => trim((string) $data['name']),
                'document_name' => $this->nullableString($data['document_name'] ?? null),
                'given_name' => $this->nullableString($data['given_name'] ?? null),
                'family_name' => $this->nullableString($data['family_name'] ?? null),
                'date_of_birth' => $data['date_of_birth'] ?? null,
                'gender_id' => isset($data['gender_id']) ? (int) $data['gender_id'] : null,
                'nationality_id' => isset($data['nationality_id']) ? (int) $data['nationality_id'] : null,
            ]);

            $this->syncPersonContactChannels($person, $data);
            $this->syncPersonOrganizationLink($person, $accountId, $data);
            $this->syncPersonTaxDocuments($person, $data['tax_ids'] ?? []);

            return $person->fresh(['contactMethods.contactType']);
        });
    }

    public function deletePersonClient(Person $person, int $accountId): void
    {
        $this->assertPersonIsClientOfAccount($person, $accountId);

        DB::transaction(function () use ($person, $accountId): void {
            $this->deleteAgencyOrganizationLinksForPerson($person, $accountId);

            AccountPerson::query()
                ->where('account_id', $accountId)
                ->where('person_id', $person->id)
                ->where('link_type', AccountPerson::LINK_CLIENT)
                ->delete();

            $this->deletePersonIfOrphaned($person);
        });
    }

    public function personOrganizationLinkForPerson(Person $person, int $accountId): ?OrganizationPerson
    {
        return OrganizationPerson::query()
            ->where('person_id', $person->id)
            ->whereHas('organization', fn (Builder $query) => $query->where('agency_id', $accountId))
            ->with([
                'organization',
                'department.translations.language',
                'position.translations.language',
            ])
            ->first();
    }

    public function organizationLabelForPersonClient(Person $person, int $accountId): ?string
    {
        if ($person->relationLoaded('organizationPersons')) {
            $organization = $person->organizationPersons->first()?->organization;
            if ($organization !== null && (int) $organization->agency_id === $accountId) {
                return $organization->displayName();
            }
        }

        $link = $this->personOrganizationLinkForPerson($person, $accountId);
        if ($link?->organization === null) {
            return null;
        }

        return $link->organization->displayName();
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function createOrganizationClient(int $accountId, array $data): Organization
    {
        return DB::transaction(function () use ($accountId, $data): Organization {
            $organization = Organization::query()->create([
                'agency_id' => $accountId,
                'legal_name' => trim((string) $data['legal_name']),
                'trade_name' => $this->nullableString($data['trade_name'] ?? null),
                'website' => $this->nullableString($data['website'] ?? null),
            ]);

            $this->syncOrganizationBillingAddress($organization, $data);
            $this->syncEntityTaxDocuments($organization->documents(), $data['tax_ids'] ?? []);

            return $organization->fresh([
                'billingAddressLink.address.cityRelation.state.country',
                'documents.document.translations.language',
            ]);
        });
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function updateOrganizationClient(Organization $organization, int $accountId, array $data): Organization
    {
        $this->assertOrganizationIsClientOfAccount($organization, $accountId);

        return DB::transaction(function () use ($organization, $data): Organization {
            $organization->update([
                'legal_name' => trim((string) $data['legal_name']),
                'trade_name' => $this->nullableString($data['trade_name'] ?? null),
                'website' => $this->nullableString($data['website'] ?? null),
            ]);

            $this->syncOrganizationBillingAddress($organization, $data);
            $this->syncEntityTaxDocuments($organization->documents(), $data['tax_ids'] ?? []);

            return $organization->fresh([
                'billingAddressLink.address.cityRelation.state.country',
                'documents.document.translations.language',
            ]);
        });
    }

    public function deleteOrganizationClient(Organization $organization, int $accountId): void
    {
        $this->assertOrganizationIsClientOfAccount($organization, $accountId);

        DB::transaction(function () use ($organization): void {
            $this->purgeOrganizationRecord($organization);
        });
    }

    public function primaryEmailForPerson(Person $person): ?string
    {
        $person->loadMissing(['contactMethods.contactType']);

        return $this->primaryContactValueByCode($person, 'email');
    }

    public function primaryPhoneForPerson(Person $person): ?string
    {
        $person->loadMissing(['contactMethods.contactType']);

        return $this->primaryContactValueByCode($person, 'phone')
            ?? $this->primaryContactValueByCode($person, 'whatsapp');
    }

    public function billingCityForOrganization(Organization $organization): ?LmpCity
    {
        $organization->loadMissing(['billingAddressLink.address.cityRelation.state.country']);
        $cityId = $organization->billingAddressLink?->address?->city_id;

        if (! is_numeric($cityId)) {
            return null;
        }

        return $organization->billingAddressLink?->address?->cityRelation;
    }

    public function billingCityLabelForOrganization(Organization $organization): ?string
    {
        $city = $this->billingCityForOrganization($organization);
        if ($city !== null) {
            return $city->name;
        }

        $organization->loadMissing(['billingAddressLink.address']);
        $manualCity = $organization->billingAddressLink?->address?->city;

        return filled($manualCity) ? (string) $manualCity : null;
    }

    public function assertPersonIsClientOfAccount(Person $person, int $accountId): void
    {
        $linked = AccountPerson::query()
            ->where('account_id', $accountId)
            ->where('person_id', $person->id)
            ->where('link_type', AccountPerson::LINK_CLIENT)
            ->exists();

        abort_unless($linked, 404);
    }

    public function assertOrganizationIsClientOfAccount(Organization $organization, int $accountId): void
    {
        abort_unless((int) $organization->agency_id === $accountId, 404);
    }

    /**
     * @deprecated Use {@see assertPersonIsClientOfAccount()}
     */
    public function assertPersonBelongsToAgency(Person $person, int $accountId): void
    {
        $this->assertPersonIsClientOfAccount($person, $accountId);
    }

    /**
     * @deprecated Use {@see assertOrganizationIsClientOfAccount()}
     */
    public function assertOrganizationBelongsToAgency(Organization $organization, int $accountId): void
    {
        $this->assertOrganizationIsClientOfAccount($organization, $accountId);
    }

    /**
     * @return Builder<Person>
     */
    private function personClientsQuery(int $accountId, ?string $search): Builder
    {
        return Person::query()
            ->whereHas('accountPersons', function (Builder $query) use ($accountId): void {
                $query->where('account_id', $accountId)
                    ->where('link_type', AccountPerson::LINK_CLIENT);
            })
            ->with([
                'contactMethods.contactType',
                'gender.translations.language',
                'organizationPersons' => fn ($query) => $query
                    ->whereHas('organization', fn (Builder $orgs) => $orgs->where('agency_id', $accountId))
                    ->with('organization'),
            ])
            ->when($search !== null, function (Builder $query) use ($search, $accountId): void {
                $like = '%'.$search.'%';
                $query->where(function (Builder $inner) use ($like, $accountId): void {
                    $inner->where('name', 'like', $like)
                        ->orWhere('document_name', 'like', $like)
                        ->orWhere('given_name', 'like', $like)
                        ->orWhere('family_name', 'like', $like)
                        ->orWhereHas('contactMethods', fn (Builder $methods) => $methods->where('value', 'like', $like))
                        ->orWhereHas(
                            'organizationPersons',
                            fn (Builder $links) => $links
                                ->whereHas(
                                    'organization',
                                    fn (Builder $orgs) => $orgs
                                        ->where('agency_id', $accountId)
                                        ->where(function (Builder $orgQuery) use ($like): void {
                                            $orgQuery->where('legal_name', 'like', $like)
                                                ->orWhere('trade_name', 'like', $like);
                                        })
                                )
                        );
                });
            })
            ->orderBy('name')
            ->orderBy('id');
    }

    /**
     * @return Builder<Organization>
     */
    private function organizationClientsQuery(int $accountId, ?string $search): Builder
    {
        return Organization::query()
            ->where('agency_id', $accountId)
            ->with(['billingAddressLink.address.cityRelation.state.country', 'documents'])
            ->when($search !== null, function (Builder $query) use ($search): void {
                $like = '%'.$search.'%';
                $query->where(function (Builder $inner) use ($like): void {
                    $inner->where('legal_name', 'like', $like)
                        ->orWhere('trade_name', 'like', $like)
                        ->orWhere('website', 'like', $like);
                });
            })
            ->orderBy('legal_name')
            ->orderBy('id');
    }

    private function deletePersonIfOrphaned(Person $person): void
    {
        $stillLinked = $person->accountPersons()->exists()
            || $person->users()->exists()
            || $person->organizationPersons()->exists()
            || $person->contactLinks()->exists();

        if ($stillLinked) {
            return;
        }

        $person->contactMethods()->delete();
        $person->documents()->delete();
        $person->delete();
    }

    private function purgeOrganizationRecord(Organization $organization): void
    {
        $addressIds = $organization->organizationAddresses()->pluck('address_id');
        $organization->documents()->delete();
        $organization->organizationAddresses()->delete();
        $organization->organizationPersons()->delete();
        if ($addressIds->isNotEmpty()) {
            Address::query()->whereIn('id', $addressIds)->delete();
        }
        $organization->delete();
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function syncPersonOrganizationLink(Person $person, int $accountId, array $data): void
    {
        $organizationId = isset($data['organization_id']) && $data['organization_id'] !== ''
            ? (int) $data['organization_id']
            : null;

        if ($organizationId === null || $organizationId <= 0) {
            $this->deleteAgencyOrganizationLinksForPerson($person, $accountId);

            return;
        }

        $organization = Organization::query()->find($organizationId);
        abort_unless($organization instanceof Organization, 422);
        $this->assertOrganizationIsClientOfAccount($organization, $accountId);

        $this->deleteAgencyOrganizationLinksForPerson($person, $accountId, $organizationId);

        OrganizationPerson::query()->updateOrCreate(
            [
                'organization_id' => $organizationId,
                'person_id' => $person->id,
            ],
            [
                'contact_department_id' => (int) $data['contact_department_id'],
                'contact_position_id' => (int) $data['contact_position_id'],
                'is_primary' => false,
                'is_active' => true,
                'is_public_contact' => false,
                'is_preferred_contact_mode' => false,
            ]
        );
    }

    private function deleteAgencyOrganizationLinksForPerson(
        Person $person,
        int $accountId,
        ?int $exceptOrganizationId = null,
    ): void {
        OrganizationPerson::query()
            ->where('person_id', $person->id)
            ->whereHas('organization', fn (Builder $query) => $query->where('agency_id', $accountId))
            ->when($exceptOrganizationId !== null, fn (Builder $query) => $query->where('organization_id', '!=', $exceptOrganizationId))
            ->delete();
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function syncPersonContactChannels(Person $person, array $data): void
    {
        $email = trim((string) ($data['email'] ?? ''));
        $phone = trim((string) ($data['phone'] ?? ''));

        $emailTypeId = $this->contactTypeIdByCode('email');
        $phoneTypeId = $this->contactTypeIdByCode('phone');

        $this->upsertSingleContactMethod($person, $emailTypeId, $email);
        $this->upsertSingleContactMethod($person, $phoneTypeId, $phone);
    }

    private function upsertSingleContactMethod(Person $person, int $contactTypeId, string $value): void
    {
        $existing = $person->contactMethods()
            ->where('contact_type_id', $contactTypeId)
            ->orderByDesc('is_primary')
            ->orderBy('id')
            ->get();

        if ($value === '') {
            $existing->each->delete();

            return;
        }

        $primary = $existing->first();
        if ($primary instanceof PersonContactMethod) {
            $primary->update(['value' => $value]);
            $existing->slice(1)->each->delete();

            return;
        }

        $person->contactMethods()->create([
            'contact_type_id' => $contactTypeId,
            'value' => $value,
            'is_primary' => true,
            'is_verified' => false,
        ]);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function syncOrganizationBillingAddress(Organization $organization, array $data): void
    {
        $mode = (string) ($data['city_location_mode'] ?? 'catalog');

        $payload = [
            'address_line_1' => trim((string) $data['address_line_1']),
            'address_line_2' => $this->nullableString($data['address_line_2'] ?? null),
            'postal_code' => trim((string) $data['postal_code']),
        ];

        if ($mode === 'manual') {
            $payload = array_merge($payload, [
                'city_id' => null,
                'city' => trim((string) ($data['city'] ?? '')),
                'state_id' => null,
                'state' => $this->nullableString($data['state'] ?? null),
                'country_id' => isset($data['country_id']) ? (int) $data['country_id'] : null,
            ]);
        } else {
            $city = LmpCity::query()
                ->with(['state.country'])
                ->find((int) ($data['city_id'] ?? 0));

            abort_unless($city instanceof LmpCity, 422);

            $payload = array_merge($payload, [
                'city_id' => (int) $city->id,
                'city' => $city->name,
                'state_id' => $city->state_id,
                'state' => $city->state?->name,
                'country_id' => $city->state?->country_id,
            ]);
        }

        $link = $organization->billingAddressLink()->with('address')->first();
        if ($link?->address instanceof Address) {
            $link->address->update($payload);

            return;
        }

        $address = Address::query()->create($payload);
        OrganizationAddress::query()->create([
            'organization_id' => $organization->id,
            'address_id' => $address->id,
            'type' => 'billing',
        ]);
    }

    /**
     * @param  array<int, array<string, mixed>>  $rows
     */
    private function syncPersonTaxDocuments(Person $person, array $rows): void
    {
        $this->syncEntityTaxDocuments($person->documents(), $rows);
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Relations\HasMany<\Illuminate\Database\Eloquent\Model>  $documentsRelation
     * @param  array<int, array<string, mixed>>  $rows
     */
    private function syncEntityTaxDocuments($documentsRelation, array $rows): void
    {
        $this->assertNoDuplicateTaxIdTypes($rows);

        $allowedDocumentIds = CatDocument::query()
            ->byGroup('tax_id')
            ->where('active', true)
            ->pluck('id')
            ->map(fn ($id): int => (int) $id)
            ->all();

        foreach ($rows as $row) {
            $rowId = isset($row['id']) ? (int) $row['id'] : null;
            $delete = (bool) ($row['delete'] ?? false);
            $documentId = isset($row['document_id']) ? (int) $row['document_id'] : 0;
            $value = trim((string) ($row['value'] ?? ''));

            if ($rowId !== null) {
                $existing = $documentsRelation->whereKey($rowId)->first();
                if ($existing === null) {
                    continue;
                }
                if ($delete) {
                    $existing->delete();
                    continue;
                }
                if ($documentId < 1 || $value === '' || ! in_array($documentId, $allowedDocumentIds, true)) {
                    continue;
                }
                $existing->update([
                    'document_id' => $documentId,
                    'value' => $value,
                ]);
                continue;
            }

            if ($delete || $documentId < 1 || $value === '' || ! in_array($documentId, $allowedDocumentIds, true)) {
                continue;
            }

            $documentsRelation->create([
                'document_id' => $documentId,
                'value' => $value,
            ]);
        }
    }

    /**
     * @param  array<int, array<string, mixed>>  $rows
     */
    private function assertNoDuplicateTaxIdTypes(array $rows): void
    {
        $seen = [];
        foreach ($rows as $row) {
            if ((bool) ($row['delete'] ?? false)) {
                continue;
            }
            $documentId = isset($row['document_id']) ? (int) $row['document_id'] : 0;
            $value = trim((string) ($row['value'] ?? ''));
            if ($documentId < 1 || $value === '') {
                continue;
            }
            if (isset($seen[$documentId])) {
                throw ValidationException::withMessages([
                    'tax_ids' => __('account.clients.validation.duplicate_tax_id_type'),
                ]);
            }
            $seen[$documentId] = true;
        }
    }

    private function primaryContactValueByCode(Person $person, string $code): ?string
    {
        foreach ($person->contactMethods as $method) {
            $typeCode = Str::lower(trim((string) ($method->contactType?->getRawOriginal('code') ?? '')));
            if ($typeCode === $code && trim((string) $method->value) !== '') {
                return trim((string) $method->value);
            }
        }

        return null;
    }

    private function contactTypeIdByCode(string $code): int
    {
        $id = ContactType::query()
            ->where('code', $code)
            ->where('active', true)
            ->value('id');

        abort_unless(is_numeric($id), 500, 'Missing contact type: '.$code);

        return (int) $id;
    }

    private function nullableString(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $trimmed = trim((string) $value);

        return $trimmed === '' ? null : $trimmed;
    }
}
