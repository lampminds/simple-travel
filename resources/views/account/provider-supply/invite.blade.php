@extends('layouts.base', ['title' => __('account.provider_supply.invite_page_title')])

@section('content')
    @include('layouts.partials.dashboard-navbar', ['fixedWidth' => true, 'sticky' => false, 'topbarColor' => 'navbar-light', 'classList' => 'mx-auto'])

    <section class="position-relative p-3 bg-gradient2">
        <div class="container">
            <x-account-page-header
                :title="__('account.provider_supply.invite')"
                :instructions="__('account.provider_supply.invite_intro')"
            />

            <div class="card border-0 shadow-sm mt-4">
                <div class="card-body">
                    <form method="post" action="{{ route('account.provider-supply.invite.store') }}">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">{{ __('account.provider_supply.field_contact_name') }}</label>
                                <input id="name" name="name" type="text" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">{{ __('account.provider_supply.field_email') }}</label>
                                <input id="email" name="email" type="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
                                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="company_name" class="form-label">{{ __('account.provider_supply.field_company') }}</label>
                                <input id="company_name" name="company_name" type="text" class="form-control @error('company_name') is-invalid @enderror" value="{{ old('company_name') }}">
                                @error('company_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            @if (! empty($targetAccountChoices))
                                <div class="col-md-6">
                                    <label for="invited_account_id" class="form-label">{{ __('account.provider_supply.field_account') }}</label>
                                    <select id="invited_account_id" name="invited_account_id" class="form-select @error('invited_account_id') is-invalid @enderror" required>
                                        <option value="">{{ __('account.provider_supply.field_account_placeholder') }}</option>
                                        @foreach ($targetAccountChoices as $choice)
                                            <option value="{{ $choice['id'] }}" @selected((int) old('invited_account_id') === (int) $choice['id'])>{{ $choice['label'] }}</option>
                                        @endforeach
                                    </select>
                                    @error('invited_account_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            @endif
                        </div>
                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('account.relationships.index') }}" class="btn btn-outline-secondary">{{ __('account.provider_supply.back') }}</a>
                            <button type="submit" class="btn btn-primary">{{ __('account.provider_supply.invite_send') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection
