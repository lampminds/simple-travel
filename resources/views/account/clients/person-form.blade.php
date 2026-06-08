@php
    $isEdit = $person !== null;
    $clientService = app(\App\Services\AgencyClientService::class);
    $emailValue = old('email', $isEdit ? $clientService->primaryEmailForPerson($person) : '');
    $phoneValue = old('phone', $isEdit ? $clientService->primaryPhoneForPerson($person) : '');

    $errorKeys = $errors->keys();
    $hasTaxErrors = collect($errorKeys)->contains(fn (string $key): bool => \Illuminate\Support\Str::startsWith($key, 'tax_ids.'));
    $hasCompanyErrors = $errors->hasAny(['organization_id', 'contact_department_id', 'contact_position_id']);
    $hasGeneralErrors = collect($errorKeys)->contains(function (string $key): bool {
        if (\Illuminate\Support\Str::startsWith($key, 'tax_ids.')) {
            return false;
        }

        return ! in_array($key, ['organization_id', 'contact_department_id', 'contact_position_id'], true);
    });

    $activeTab = 'general';
    if ($hasTaxErrors && ! $hasGeneralErrors && ! $hasCompanyErrors) {
        $activeTab = 'tax_ids';
    } elseif ($hasCompanyErrors && ! $hasGeneralErrors && ! $hasTaxErrors) {
        $activeTab = 'company';
    } elseif ($hasTaxErrors && ! $hasGeneralErrors) {
        $activeTab = 'tax_ids';
    } elseif ($hasCompanyErrors && ! $hasGeneralErrors) {
        $activeTab = 'company';
    }

    $oldTaxIds = old('tax_ids');
    $taxRows = [];
    if (is_array($oldTaxIds)) {
        $taxRows = array_values($oldTaxIds);
    } else {
        foreach ($taxIds as $taxId) {
            $taxRows[] = [
                'id' => $taxId->id,
                'document_id' => $taxId->document_id,
                'value' => $taxId->value,
                'delete' => false,
            ];
        }
    }
    $nextTaxIndex = count($taxRows);
@endphp

@extends('layouts.base', ['title' => $isEdit ? __('account.clients.edit_person_page_title') : __('account.clients.create_person_page_title')])

@section('css')
    <style>
        .client-person-tabs-wrap {
            background-color: var(--bs-tertiary-bg);
            border-radius: var(--bs-border-radius);
            padding: 0.5rem 0.5rem 0;
        }
        .client-person-tabs-wrap .nav-tabs {
            border-bottom: none;
            gap: 0.25rem;
        }
        .client-person-tabs-wrap .nav-tabs .nav-link {
            border: 1px solid transparent;
            border-radius: 0.375rem 0.375rem 0 0;
            color: var(--bs-secondary-color);
            background-color: rgba(var(--bs-emphasis-color-rgb), 0.06);
            margin-bottom: 0;
        }
        .client-person-tabs-wrap .nav-tabs .nav-link.active {
            color: var(--bs-primary);
            font-weight: 600;
            background-color: var(--bs-body-bg);
            border-color: var(--bs-border-color) var(--bs-border-color) var(--bs-body-bg);
            box-shadow: 0 -0.2rem 0 0 var(--bs-primary);
        }
        .client-person-tabs-wrap .nav-tabs .nav-link.active.text-danger,
        .client-person-tabs-wrap .nav-tabs .nav-link.text-danger {
            color: var(--bs-danger) !important;
            box-shadow: 0 -0.2rem 0 0 var(--bs-danger);
        }
    </style>
@endsection

@section('content')

    @include('layouts.partials.dashboard-navbar', ['fixedWidth' => true, 'sticky' => false, 'topbarColor' => 'navbar-light', 'classList' => 'mx-auto'])

    <section class="position-relative p-3 bg-gradient2">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <x-account-page-header
                        :title="$isEdit ? __('account.clients.edit_person_heading') : __('account.clients.create_person_heading')"
                        :subtitle="$isEdit ? $person->resolveFullName() : null"
                        :instructions="__('account.clients.person_form_instructions')"
                    />
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-lg-10 col-xl-9">
                    <div class="card">
                        <div class="card-body">
                            <form method="POST" action="{{ $submitRoute }}" id="client-person-form" novalidate>
                                @csrf
                                @if ($submitMethod !== 'POST')
                                    @method($submitMethod)
                                @endif

                                @if ($errors->any())
                                    <div id="client-person-form-errors" class="alert alert-danger mb-3" role="alert" tabindex="-1">
                                        <div class="fw-semibold">{{ __('account.clients.validation.form_errors_heading') }}</div>
                                        <p class="mb-2 mt-1 small">{{ __('account.clients.validation.tabs_hint') }}</p>
                                        <ul class="mb-0 ps-3">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                <div class="client-person-tabs-wrap mb-3">
                                    <ul class="nav nav-tabs" role="tablist">
                                        <li class="nav-item" role="presentation">
                                            <button
                                                class="nav-link @if ($activeTab === 'general') active @endif @if ($hasGeneralErrors) text-danger fw-semibold @endif"
                                                type="button"
                                                data-bs-toggle="tab"
                                                data-bs-target="#client-person-tab-general"
                                                role="tab"
                                            >
                                                {{ __('account.clients.tab_general') }}
                                            </button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button
                                                class="nav-link @if ($activeTab === 'company') active @endif @if ($hasCompanyErrors) text-danger fw-semibold @endif"
                                                type="button"
                                                data-bs-toggle="tab"
                                                data-bs-target="#client-person-tab-company"
                                                role="tab"
                                            >
                                                {{ __('account.clients.tab_company') }}
                                            </button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button
                                                class="nav-link @if ($activeTab === 'tax_ids') active @endif @if ($hasTaxErrors) text-danger fw-semibold @endif"
                                                type="button"
                                                data-bs-toggle="tab"
                                                data-bs-target="#client-person-tab-tax"
                                                role="tab"
                                            >
                                                {{ __('account.clients.tab_tax_ids') }}
                                            </button>
                                        </li>
                                    </ul>
                                </div>

                                <div class="tab-content">
                                    <div class="tab-pane fade @if ($activeTab === 'general') show active @endif" id="client-person-tab-general" role="tabpanel">
                                        <div class="row g-3">
                                            <div class="col-12">
                                                <label class="form-label" for="client-person-name">{{ __('account.clients.fields.person_name') }}</label>
                                                <input
                                                    type="text"
                                                    class="form-control @error('name') is-invalid @enderror"
                                                    id="client-person-name"
                                                    name="name"
                                                    value="{{ old('name', $person?->name) }}"
                                                    required
                                                    maxlength="255"
                                                >
                                                @error('name')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-12 col-md-6">
                                                <label class="form-label" for="client-person-document-name">{{ __('account.clients.fields.document_name') }}</label>
                                                <input
                                                    type="text"
                                                    class="form-control @error('document_name') is-invalid @enderror"
                                                    id="client-person-document-name"
                                                    name="document_name"
                                                    value="{{ old('document_name', $person?->document_name) }}"
                                                    maxlength="255"
                                                >
                                                @error('document_name')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-12 col-md-6">
                                                <label class="form-label" for="client-person-dob">{{ __('account.clients.fields.date_of_birth') }}</label>
                                                <input
                                                    type="date"
                                                    class="form-control @error('date_of_birth') is-invalid @enderror"
                                                    id="client-person-dob"
                                                    name="date_of_birth"
                                                    value="{{ old('date_of_birth', $person?->date_of_birth?->format('Y-m-d')) }}"
                                                    max="{{ now()->format('Y-m-d') }}"
                                                >
                                                @error('date_of_birth')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-12 col-md-6">
                                                <label class="form-label" for="client-person-given-name">{{ __('account.clients.fields.given_name') }}</label>
                                                <input
                                                    type="text"
                                                    class="form-control @error('given_name') is-invalid @enderror"
                                                    id="client-person-given-name"
                                                    name="given_name"
                                                    value="{{ old('given_name', $person?->given_name) }}"
                                                    maxlength="255"
                                                >
                                                @error('given_name')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-12 col-md-6">
                                                <label class="form-label" for="client-person-family-name">{{ __('account.clients.fields.family_name') }}</label>
                                                <input
                                                    type="text"
                                                    class="form-control @error('family_name') is-invalid @enderror"
                                                    id="client-person-family-name"
                                                    name="family_name"
                                                    value="{{ old('family_name', $person?->family_name) }}"
                                                    maxlength="255"
                                                >
                                                @error('family_name')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-12 col-md-6">
                                                <label class="form-label" for="client-person-gender">{{ __('account.clients.fields.gender') }}</label>
                                                <select
                                                    class="form-select @error('gender_id') is-invalid @enderror"
                                                    id="client-person-gender"
                                                    name="gender_id"
                                                >
                                                    <option value="">{{ __('account.clients.fields.gender_placeholder') }}</option>
                                                    @foreach ($genders as $gender)
                                                        <option value="{{ $gender->id }}" @selected((string) old('gender_id', $person?->gender_id) === (string) $gender->id)>
                                                            {{ $gender->name !== '' ? $gender->name : $gender->getRawOriginal('code') }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('gender_id')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-12 col-md-6">
                                                <label class="form-label" for="client-person-nationality">{{ __('account.clients.fields.nationality') }}</label>
                                                <select
                                                    class="form-select @error('nationality_id') is-invalid @enderror"
                                                    id="client-person-nationality"
                                                    name="nationality_id"
                                                >
                                                    <option value="">{{ __('account.clients.fields.nationality_placeholder') }}</option>
                                                    @foreach ($countries as $country)
                                                        <option value="{{ $country->id }}" @selected((string) old('nationality_id', $person?->nationality_id) === (string) $country->id)>
                                                            {{ $country->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('nationality_id')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-12">
                                                <hr class="my-1">
                                                <h6 class="text-muted mb-0">{{ __('account.clients.contact_section_heading') }}</h6>
                                            </div>

                                            <div class="col-12 col-md-6">
                                                <label class="form-label" for="client-person-email">{{ __('account.clients.fields.email') }}</label>
                                                <input
                                                    type="email"
                                                    class="form-control @error('email') is-invalid @enderror"
                                                    id="client-person-email"
                                                    name="email"
                                                    value="{{ $emailValue }}"
                                                    maxlength="255"
                                                >
                                                @error('email')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-12 col-md-6">
                                                <label class="form-label" for="client-person-phone">{{ __('account.clients.fields.phone') }}</label>
                                                <input
                                                    type="text"
                                                    class="form-control @error('phone') is-invalid @enderror"
                                                    id="client-person-phone"
                                                    name="phone"
                                                    value="{{ $phoneValue }}"
                                                    maxlength="255"
                                                >
                                                @error('phone')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="tab-pane fade @if ($activeTab === 'company') show active @endif" id="client-person-tab-company" role="tabpanel">
                                        <p class="text-muted small mb-3">{{ __('account.clients.company_tab_hint') }}</p>

                                        @if ($organizations->isEmpty())
                                            <div class="alert alert-light border mb-0" role="status">
                                                {{ __('account.clients.company_tab_no_organizations') }}
                                                <a href="{{ route('account.clients.organizations.create') }}">{{ __('account.clients.create_organization_button') }}</a>
                                            </div>
                                        @else
                                            <div class="row g-3">
                                                <div class="col-12">
                                                    <label class="form-label" for="client-person-organization">{{ __('account.clients.fields.organization') }}</label>
                                                    <select
                                                        class="form-select @error('organization_id') is-invalid @enderror"
                                                        id="client-person-organization"
                                                        name="organization_id"
                                                        data-company-field
                                                    >
                                                        <option value="">{{ __('account.clients.fields.organization_placeholder') }}</option>
                                                        @foreach ($organizations as $organization)
                                                            <option
                                                                value="{{ $organization->id }}"
                                                                @selected((string) old('organization_id', $organizationLink?->organization_id) === (string) $organization->id)
                                                            >
                                                                {{ $organization->displayName() }}
                                                                @if ($organization->trade_name && $organization->legal_name !== $organization->trade_name)
                                                                    ({{ $organization->legal_name }})
                                                                @endif
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('organization_id')
                                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <div class="col-12 col-md-6">
                                                    <label class="form-label" for="client-person-department">{{ __('account.clients.fields.contact_department') }}</label>
                                                    <select
                                                        class="form-select @error('contact_department_id') is-invalid @enderror"
                                                        id="client-person-department"
                                                        name="contact_department_id"
                                                        data-company-field
                                                        data-company-required
                                                    >
                                                        <option value="">{{ __('account.clients.fields.contact_department_placeholder') }}</option>
                                                        @foreach ($contactDepartments as $department)
                                                            <option
                                                                value="{{ $department->id }}"
                                                                @selected((string) old('contact_department_id', $organizationLink?->contact_department_id) === (string) $department->id)
                                                            >
                                                                {{ $department->code }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('contact_department_id')
                                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <div class="col-12 col-md-6">
                                                    <label class="form-label" for="client-person-position">{{ __('account.clients.fields.contact_position') }}</label>
                                                    <select
                                                        class="form-select @error('contact_position_id') is-invalid @enderror"
                                                        id="client-person-position"
                                                        name="contact_position_id"
                                                        data-company-field
                                                        data-company-required
                                                    >
                                                        <option value="">{{ __('account.clients.fields.contact_position_placeholder') }}</option>
                                                        @foreach ($contactPositions as $position)
                                                            <option
                                                                value="{{ $position->id }}"
                                                                @selected((string) old('contact_position_id', $organizationLink?->contact_position_id) === (string) $position->id)
                                                            >
                                                                {{ $position->code }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('contact_position_id')
                                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="tab-pane fade @if ($activeTab === 'tax_ids') show active @endif" id="client-person-tab-tax" role="tabpanel">
                                        <div id="client-person-tax-rows" class="d-flex flex-column gap-3">
                                            @forelse ($taxRows as $idx => $row)
                                                @include('account.clients.partials.tax-id-row', [
                                                    'idx' => $idx,
                                                    'row' => $row,
                                                    'taxIdCategories' => $taxIdCategories,
                                                ])
                                            @empty
                                                <p class="text-muted mb-0" id="client-person-tax-empty">{{ __('account.clients.tax_ids_empty') }}</p>
                                            @endforelse
                                        </div>
                                        <button type="button" class="btn btn-outline-secondary mt-3" id="client-person-add-tax-row">
                                            {{ __('account.clients.add_tax_id_button') }}
                                        </button>
                                    </div>
                                </div>

                                <div class="d-flex gap-2 mt-4">
                                    <a class="btn btn-light" href="{{ $cancelRoute }}">{{ __('account.clients.cancel_button') }}</a>
                                    <button type="submit" class="btn btn-primary">
                                        {{ $isEdit ? __('account.clients.update_button') : __('account.clients.save_button') }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <template id="client-person-tax-row-template">
        @include('account.clients.partials.tax-id-row', [
            'idx' => '__INDEX__',
            'row' => ['id' => null, 'document_id' => null, 'value' => '', 'delete' => false],
            'taxIdCategories' => $taxIdCategories,
        ])
    </template>

    <x-site-footer-simple />

@endsection

@section('script-bottom')
    <script>
        (function () {
            const orgSelect = document.getElementById('client-person-organization');
            const requiredFields = document.querySelectorAll('[data-company-required]');
            const taxRowsContainer = document.getElementById('client-person-tax-rows');
            const addTaxRowBtn = document.getElementById('client-person-add-tax-row');
            const taxRowTemplate = document.getElementById('client-person-tax-row-template');
            const taxEmptyMsg = document.getElementById('client-person-tax-empty');
            let nextTaxIndex = {{ (int) $nextTaxIndex }};

            function syncCompanyFields() {
                const hasOrg = orgSelect !== null && orgSelect.value !== '';
                requiredFields.forEach((field) => {
                    field.disabled = !hasOrg;
                });
            }

            orgSelect?.addEventListener('change', syncCompanyFields);
            syncCompanyFields();

            document.getElementById('client-person-form')?.addEventListener('submit', () => {
                requiredFields.forEach((field) => {
                    field.disabled = false;
                });
            });

            if (addTaxRowBtn && taxRowsContainer && taxRowTemplate) {
                addTaxRowBtn.addEventListener('click', () => {
                    taxEmptyMsg?.remove();
                    const html = taxRowTemplate.innerHTML.replaceAll('__INDEX__', String(nextTaxIndex));
                    const wrapper = document.createElement('div');
                    wrapper.innerHTML = html.trim();
                    taxRowsContainer.appendChild(wrapper.firstElementChild);
                    nextTaxIndex += 1;
                });
            }

            const errorAlert = document.getElementById('client-person-form-errors');
            if (errorAlert) {
                requestAnimationFrame(() => {
                    errorAlert.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    errorAlert.focus({ preventScroll: true });
                });
                const firstInvalid = document.querySelector('#client-person-form .tab-pane.active .is-invalid');
                if (firstInvalid instanceof HTMLElement) {
                    firstInvalid.focus({ preventScroll: true });
                }
            }
        })();
    </script>
@endsection
