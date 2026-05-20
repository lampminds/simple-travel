@extends('layouts.base', ['title' => __('account.contacts.detail_page_title', ['name' => $person->name])])

@section('content')

    @include('layouts.partials.dashboard-navbar', ['fixedWidth' => true, 'sticky' => false, 'topbarColor' => 'navbar-light', 'classList' => 'mx-auto'])

    <section class="position-relative p-3 bg-gradient2">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="page-title">
                        <nav aria-label="breadcrumb" class="mb-2">
                            <ol class="breadcrumb mb-0 small">
                                <li class="breadcrumb-item"><a href="{{ route('account.contacts.index') }}">{{ __('account.contacts.heading') }}</a></li>
                                <li class="breadcrumb-item active" aria-current="page">{{ $person->name }}</li>
                            </ol>
                        </nav>
                        <div class="d-flex flex-wrap align-items-center gap-2">
                            <h3 class="my-0">{{ $person->name }}</h3>
                            @if ($accountPerson->is_preferred_contact_mode)
                                <span class="badge bg-primary-subtle text-primary-emphasis border border-primary-subtle">
                                    {{ __('account.contacts.preferred_badge') }}
                                </span>
                            @endif
                        </div>
                        <p class="mt-1 fw-medium text-muted mb-0">
                            {{ $sourceAccount->commercial_name ?? $sourceAccount->name ?? $sourceAccount->nick }}
                            @if ($accountPerson->department?->code || $accountPerson->position?->code)
                                <span class="mx-1">·</span>
                                {{ $accountPerson->department?->code }}
                                @if ($accountPerson->department?->code && $accountPerson->position?->code)
                                    /
                                @endif
                                {{ $accountPerson->position?->code }}
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            @if (session('status'))
                <div class="alert alert-success mt-3 mb-0" role="alert">{{ session('status') }}</div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger mt-3 mb-0" role="alert">
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="row mt-4 g-4">
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <h5 class="card-title mb-3">{{ __('account.contacts.detail_methods_heading') }}</h5>
                            @if ($person->contactMethods->isEmpty())
                                <p class="text-muted mb-0">{{ __('account.contacts.detail_methods_empty') }}</p>
                            @else
                                <ul class="list-group list-group-flush">
                                    @foreach ($person->contactMethods->sortByDesc('is_primary') as $method)
                                        <li class="list-group-item px-0 d-flex justify-content-between align-items-start gap-2">
                                            <div>
                                                <span class="fw-medium">{{ $method->contactType?->code ?? '—' }}</span>
                                                @if ($method->is_primary)
                                                    <span class="badge bg-secondary-subtle text-secondary-emphasis border border-secondary-subtle ms-1">
                                                        {{ __('account.contacts.method_primary') }}
                                                    </span>
                                                @endif
                                                <div class="text-muted small break-all">{{ $method->value }}</div>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <h5 class="card-title mb-3">{{ __('account.contacts.message_heading') }}</h5>
                            <p class="text-muted small">{{ __('account.contacts.message_intro') }}</p>
                            <form method="post" action="{{ route('account.contacts.message', $accountPerson) }}">
                                @csrf
                                <div class="mb-3">
                                    <label for="contact-message" class="form-label">{{ __('account.contacts.message_label') }}</label>
                                    <textarea
                                        id="contact-message"
                                        name="message"
                                        class="form-control @error('message') is-invalid @enderror"
                                        rows="5"
                                        required
                                        maxlength="4000"
                                    >{{ old('message') }}</textarea>
                                    @error('message')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                <button type="submit" class="btn btn-primary">{{ __('account.contacts.message_send') }}</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <x-site-footer-simple />

@endsection
