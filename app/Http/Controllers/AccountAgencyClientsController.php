<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\CatDocument;
use App\Models\CatGender;
use App\Models\ContactDepartment;
use App\Models\ContactPosition;
use App\Models\LmpCity;
use App\Models\LmpCountry;
use App\Models\Organization;
use App\Models\Person;
use App\Services\AgencyClientService;
use App\Support\AccountBusinessTypeGate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

final class AccountAgencyClientsController extends Controller
{
    public function __construct(
        private readonly AgencyClientService $clients,
    ) {
    }

    public function index(Request $request): View
    {
        $account = AccountBusinessTypeGate::assertAgencyAccount($request);
        $type = (string) $request->query('type', 'all');
        $search = $request->query('search');

        $data = $this->clients->indexData(
            accountId: (int) $account->id,
            type: $type,
            search: is_string($search) ? $search : null,
        );

        return view('account.clients.index', [
            'account' => $account,
            'type' => $data['type'],
            'search' => is_string($search) ? trim($search) : '',
            'personClients' => $data['personClients'],
            'organizationClients' => $data['organizationClients'],
        ]);
    }

    public function createPerson(Request $request): View
    {
        $account = AccountBusinessTypeGate::assertAgencyAccount($request);

        return view('account.clients.person-form', array_merge(
            $this->personFormViewData(null, (int) $account->id),
            [
                'account' => $account,
                'submitRoute' => route('account.clients.persons.store'),
                'submitMethod' => 'POST',
                'cancelRoute' => route('account.clients.index'),
            ]
        ));
    }

    public function storePerson(Request $request): RedirectResponse
    {
        $account = AccountBusinessTypeGate::assertAgencyAccount($request);
        $validated = $this->validatePersonPayload($request, (int) $account->id);

        $this->clients->createPersonClient((int) $account->id, $validated);

        return redirect()
            ->route('account.clients.index', ['type' => 'person'])
            ->with('status', __('account.clients.status_person_created'));
    }

    public function editPerson(Request $request, Person $person): View
    {
        $account = AccountBusinessTypeGate::assertAgencyAccount($request);
        $this->clients->assertPersonBelongsToAgency($person, (int) $account->id);
        $person->load([
            'contactMethods.contactType',
            'documents.document.translations.language',
        ]);

        return view('account.clients.person-form', array_merge(
            $this->personFormViewData($person, (int) $account->id),
            [
                'account' => $account,
                'submitRoute' => route('account.clients.persons.update', $person),
                'submitMethod' => 'PUT',
                'cancelRoute' => route('account.clients.index', ['type' => 'person']),
            ]
        ));
    }

    public function updatePerson(Request $request, Person $person): RedirectResponse
    {
        $account = AccountBusinessTypeGate::assertAgencyAccount($request);
        $validated = $this->validatePersonPayload($request, (int) $account->id);

        $this->clients->updatePersonClient($person, (int) $account->id, $validated);

        return redirect()
            ->route('account.clients.index', ['type' => 'person'])
            ->with('status', __('account.clients.status_person_updated'));
    }

    public function destroyPerson(Request $request, Person $person): RedirectResponse
    {
        $account = AccountBusinessTypeGate::assertAgencyAccount($request);
        $this->clients->deletePersonClient($person, (int) $account->id);

        return redirect()
            ->route('account.clients.index', ['type' => 'person'])
            ->with('status', __('account.clients.status_person_deleted'));
    }

    public function createOrganization(Request $request): View
    {
        $account = AccountBusinessTypeGate::assertAgencyAccount($request);

        return view('account.clients.organization-form', array_merge(
            $this->organizationFormViewData(null, null),
            [
                'account' => $account,
                'submitRoute' => route('account.clients.organizations.store'),
                'submitMethod' => 'POST',
                'cancelRoute' => route('account.clients.index'),
            ]
        ));
    }

    public function storeOrganization(Request $request): RedirectResponse
    {
        $account = AccountBusinessTypeGate::assertAgencyAccount($request);
        $validated = $this->validateOrganizationPayload($request);

        $this->clients->createOrganizationClient((int) $account->id, $validated);

        return redirect()
            ->route('account.clients.index', ['type' => 'organization'])
            ->with('status', __('account.clients.status_organization_created'));
    }

    public function editOrganization(Request $request, Organization $organization): View
    {
        $account = AccountBusinessTypeGate::assertAgencyAccount($request);
        $this->clients->assertOrganizationBelongsToAgency($organization, (int) $account->id);

        $organization->load([
            'billingAddressLink.address.cityRelation.state.country',
            'documents.document.translations.language',
        ]);

        $billingAddress = $organization->billingAddressLink?->address;
        $currentCity = $this->clients->billingCityForOrganization($organization);

        return view('account.clients.organization-form', array_merge(
            $this->organizationFormViewData($organization, $billingAddress, $currentCity),
            [
                'account' => $account,
                'submitRoute' => route('account.clients.organizations.update', $organization),
                'submitMethod' => 'PUT',
                'cancelRoute' => route('account.clients.index', ['type' => 'organization']),
            ]
        ));
    }

    public function updateOrganization(Request $request, Organization $organization): RedirectResponse
    {
        $account = AccountBusinessTypeGate::assertAgencyAccount($request);
        $validated = $this->validateOrganizationPayload($request);

        $this->clients->updateOrganizationClient($organization, (int) $account->id, $validated);

        return redirect()
            ->route('account.clients.index', ['type' => 'organization'])
            ->with('status', __('account.clients.status_organization_updated'));
    }

    public function destroyOrganization(Request $request, Organization $organization): RedirectResponse
    {
        $account = AccountBusinessTypeGate::assertAgencyAccount($request);
        $this->clients->deleteOrganizationClient($organization, (int) $account->id);

        return redirect()
            ->route('account.clients.index', ['type' => 'organization'])
            ->with('status', __('account.clients.status_organization_deleted'));
    }

    /**
     * @return array<string, mixed>
     */
    private function validatePersonPayload(Request $request, int $accountId): array
    {
        return $request->validate(
            [
                'name' => ['required', 'string', 'max:255'],
                'document_name' => ['nullable', 'string', 'max:255'],
                'given_name' => ['nullable', 'string', 'max:255'],
                'family_name' => ['nullable', 'string', 'max:255'],
                'date_of_birth' => ['nullable', 'date', 'before_or_equal:today'],
                'gender_id' => ['nullable', 'integer', Rule::exists(CatGender::class, 'id')],
                'nationality_id' => ['nullable', 'integer', Rule::exists(LmpCountry::class, 'id')],
                'email' => ['nullable', 'email', 'max:255'],
                'phone' => ['nullable', 'string', 'max:255'],
                'organization_id' => [
                    'nullable',
                    'integer',
                    Rule::exists('organizations', 'id')->where(fn ($query) => $query->where('agency_id', $accountId)),
                ],
                'contact_department_id' => [
                    Rule::requiredIf(fn (): bool => filled($request->input('organization_id'))),
                    'nullable',
                    'integer',
                    Rule::exists('cat_contact_departments', 'id')->where('active', true),
                ],
                'contact_position_id' => [
                    Rule::requiredIf(fn (): bool => filled($request->input('organization_id'))),
                    'nullable',
                    'integer',
                    Rule::exists('cat_contact_positions', 'id')->where('active', true),
                ],
                ...$this->taxIdsValidationRules('person_documents'),
            ],
            [
                'tax_ids.*.document_id.required_with' => __('account.clients.validation.tax_id_type_required'),
                'tax_ids.*.value.required_with' => __('account.clients.validation.tax_id_value_required'),
            ],
            [
                'name' => __('account.clients.fields.person_name'),
                'email' => __('account.clients.fields.email'),
                'phone' => __('account.clients.fields.phone'),
                'organization_id' => __('account.clients.fields.organization'),
                'contact_department_id' => __('account.clients.fields.contact_department'),
                'contact_position_id' => __('account.clients.fields.contact_position'),
            ]
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function personFormViewData(?Person $person, int $accountId): array
    {
        $organizationLink = $person !== null
            ? $this->clients->personOrganizationLinkForPerson($person, $accountId)
            : null;

        return [
            'person' => $person,
            'organizationLink' => $organizationLink,
            'genders' => $this->activeGenders(),
            'countries' => $this->countries(),
            'organizations' => Organization::query()
                ->where('agency_id', $accountId)
                ->orderBy('legal_name')
                ->orderBy('id')
                ->get(['id', 'legal_name', 'trade_name']),
            'contactDepartments' => $this->activeContactDepartments(),
            'contactPositions' => $this->activeContactPositions(),
            'taxIdCategories' => $this->taxIdCategories(),
            'taxIds' => $person !== null
                ? $person->documents()->with(['document.translations.language'])->orderBy('id')->get()
                : collect(),
        ];
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    private function taxIdsValidationRules(string $documentsTable): array
    {
        return [
            'tax_ids' => ['array'],
            'tax_ids.*.id' => ['nullable', 'integer', 'exists:'.$documentsTable.',id'],
            'tax_ids.*.document_id' => [
                'nullable',
                'integer',
                Rule::exists('cat_documents', 'id')->where('group', 'tax_id'),
                'required_with:tax_ids.*.value',
            ],
            'tax_ids.*.value' => ['nullable', 'string', 'max:255', 'required_with:tax_ids.*.document_id'],
            'tax_ids.*.delete' => ['nullable', 'boolean'],
        ];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, ContactDepartment>
     */
    private function activeContactDepartments()
    {
        return ContactDepartment::query()
            ->with(['translations.language'])
            ->where('active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, ContactPosition>
     */
    private function activeContactPositions()
    {
        return ContactPosition::query()
            ->with(['translations.language'])
            ->where('active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();
    }

    /**
     * @return array<string, mixed>
     */
    private function validateOrganizationPayload(Request $request): array
    {
        return $request->validate(
            [
                'legal_name' => ['required', 'string', 'max:255'],
                'trade_name' => ['nullable', 'string', 'max:255'],
                'website' => ['nullable', 'string', 'max:255', 'url'],
                'address_line_1' => ['required', 'string', 'max:255'],
                'address_line_2' => ['nullable', 'string', 'max:255'],
                'city_location_mode' => ['required', Rule::in(['catalog', 'manual'])],
                'city_id' => [
                    Rule::requiredIf(fn (): bool => $request->input('city_location_mode') === 'catalog'),
                    'nullable',
                    'integer',
                    Rule::exists(LmpCity::class, 'id'),
                ],
                'city' => [
                    Rule::requiredIf(fn (): bool => $request->input('city_location_mode') === 'manual'),
                    'nullable',
                    'string',
                    'max:255',
                ],
                'state' => ['nullable', 'string', 'max:255'],
                'country_id' => [
                    Rule::requiredIf(fn (): bool => $request->input('city_location_mode') === 'manual'),
                    'nullable',
                    'integer',
                    Rule::exists(LmpCountry::class, 'id'),
                ],
                'postal_code' => ['required', 'string', 'max:255'],
                ...$this->taxIdsValidationRules('organization_documents'),
            ],
            [
                'tax_ids.*.document_id.required_with' => __('account.clients.validation.tax_id_type_required'),
                'tax_ids.*.value.required_with' => __('account.clients.validation.tax_id_value_required'),
            ],
            [
                'legal_name' => __('account.clients.fields.legal_name'),
                'website' => __('account.clients.fields.website'),
                'city_id' => __('account.clients.fields.city'),
                'city' => __('account.clients.fields.city'),
                'country_id' => __('account.clients.fields.country'),
                'address_line_1' => __('account.clients.fields.address_line_1'),
                'postal_code' => __('account.clients.fields.postal_code'),
            ]
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function organizationFormViewData(
        ?Organization $organization,
        ?Address $billingAddress = null,
        ?LmpCity $currentCity = null,
    ): array {
        if ($organization !== null && $billingAddress === null) {
            $billingAddress = $organization->billingAddressLink?->address;
        }

        if ($organization !== null && $currentCity === null) {
            $currentCity = $this->clients->billingCityForOrganization($organization);
        }

        return [
            'organization' => $organization,
            'currentCity' => $currentCity,
            'billingAddress' => $billingAddress,
            'cityLocationMode' => $this->resolveCityLocationMode($billingAddress),
            'countries' => $this->countries(),
            'taxIdCategories' => $this->taxIdCategories(),
            'taxIds' => $organization !== null
                ? $organization->documents()->with(['document.translations.language'])->orderBy('id')->get()
                : collect(),
        ];
    }

    private function resolveCityLocationMode(?Address $billingAddress): string
    {
        $fromOld = old('city_location_mode');
        if (in_array($fromOld, ['catalog', 'manual'], true)) {
            return $fromOld;
        }

        if ($billingAddress === null) {
            return 'catalog';
        }

        if (is_numeric($billingAddress->city_id) && (int) $billingAddress->city_id > 0) {
            return 'catalog';
        }

        if (filled($billingAddress->city)) {
            return 'manual';
        }

        return 'catalog';
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, CatGender>
     */
    private function activeGenders()
    {
        return CatGender::query()
            ->with(['translations.language'])
            ->where('active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, LmpCountry>
     */
    private function countries()
    {
        return LmpCountry::query()->orderBy('name')->get(['id', 'name']);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, CatDocument>
     */
    private function taxIdCategories()
    {
        return CatDocument::query()
            ->byGroup('tax_id')
            ->where('active', true)
            ->with(['translations.language'])
            ->ordered()
            ->get();
    }
}
