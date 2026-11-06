@extends('layouts.base', ['title' => __('account.passenger_policies.provider_title')])

@section('content')

    @include('layouts.partials.dashboard-navbar', ['fixedWidth' => true, 'sticky' => false, 'topbarColor' => 'navbar-light', 'classList' => 'mx-auto'])

    <section class="position-relative p-3 bg-gradient2">
        <div class="container">
            @if (session('status'))
                <div class="alert alert-success mb-3" role="alert">{{ session('status') }}</div>
            @endif

            <x-account-page-header
                :title="$policy ? __('account.passenger_policies.edit_heading', ['name' => $policy->name]) : __('account.passenger_policies.create_heading')"
                :subtitle="$account->commercial_name ?? $account->name ?? $account->nick"
                :instructions="__('account.passenger_policies.form_instructions')"
            />

            <form method="POST" action="{{ $policy ? route('account.passenger-policies.update', $policy) : route('account.passenger-policies.store') }}" class="card mt-4">
                @csrf
                @if ($policy)
                    @method('PUT')
                @endif
                <div class="card-body">
                    <div class="mb-4">
                        <label class="form-label" for="policy-name">{{ __('account.passenger_policies.name') }}</label>
                        <input
                            type="text"
                            maxlength="120"
                            class="form-control @error('name') is-invalid @enderror"
                            id="policy-name"
                            name="name"
                            value="{{ old('name', $policy->name ?? '') }}"
                            required
                        >
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <p class="text-muted">{{ __('account.passenger_policies.age_help') }}</p>
                    @error('rules')
                        <div class="alert alert-danger py-2" role="alert">{{ $message }}</div>
                    @enderror

                    @foreach ($categories as $category)
                        @php $rule = $rules[$category->id] ?? ['included' => false, 'age_min' => 0, 'age_max' => '']; @endphp
                        <div class="row align-items-end border-top py-3">
                            <div class="col-lg-4">
                                <div class="form-check mb-3">
                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        name="rules[{{ $category->id }}][included]"
                                        id="category-{{ $category->id }}"
                                        value="1"
                                        @checked(old('rules.'.$category->id.'.included', $rule['included']))
                                    >
                                    <label class="form-check-label" for="category-{{ $category->id }}">{{ $category->name }}</label>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <label class="form-label" for="age-min-{{ $category->id }}">{{ __('account.passenger_policies.age_min') }}</label>
                                <input
                                    type="number"
                                    min="0"
                                    max="255"
                                    class="form-control @error('rules.'.$category->id.'.age_min') is-invalid @enderror"
                                    id="age-min-{{ $category->id }}"
                                    name="rules[{{ $category->id }}][age_min]"
                                    value="{{ old('rules.'.$category->id.'.age_min', $rule['age_min']) }}"
                                >
                                @error('rules.'.$category->id.'.age_min')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-lg-4">
                                <label class="form-label" for="age-max-{{ $category->id }}">{{ __('account.passenger_policies.age_max') }}</label>
                                <input
                                    type="number"
                                    min="0"
                                    max="255"
                                    class="form-control @error('rules.'.$category->id.'.age_max') is-invalid @enderror"
                                    id="age-max-{{ $category->id }}"
                                    name="rules[{{ $category->id }}][age_max]"
                                    value="{{ old('rules.'.$category->id.'.age_max', $rule['age_max']) }}"
                                    placeholder="{{ __('account.passenger_policies.age_max_placeholder') }}"
                                >
                                @error('rules.'.$category->id.'.age_max')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="card-footer d-flex justify-content-end gap-2">
                    <a class="btn btn-light" href="{{ route('account.passenger-policies.index') }}">{{ __('account.passenger_policies.cancel') }}</a>
                    <button type="submit" class="btn btn-primary">{{ __('account.passenger_policies.save') }}</button>
                </div>
            </form>
        </div>
    </section>

    <x-site-footer-simple />

@endsection
