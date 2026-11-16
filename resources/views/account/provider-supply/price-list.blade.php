@extends('layouts.base', ['title' => __('account.provider_supply.price_list_page_title', ['company' => $counterpartLabel])])

@section('content')
    @include('layouts.partials.dashboard-navbar', ['fixedWidth' => true, 'sticky' => false, 'topbarColor' => 'navbar-light', 'classList' => 'mx-auto'])

    <section class="position-relative p-3 bg-gradient2">
        <div class="container">
            @if (session('status'))
                <div class="alert alert-success mb-3" role="alert">{{ session('status') }}</div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger mb-3" role="alert">{{ $errors->first() }}</div>
            @endif

            <x-account-page-header
                :title="__('account.provider_supply.price_list_heading')"
                :subtitle="$counterpartLabel"
                :instructions="__('account.provider_supply.price_list_intro')"
            />

            <div class="card border-0 shadow-sm mt-4">
                <div class="card-body">
                    <form method="post" action="{{ route('account.provider-supply.price-list.update', $counterpart) }}">
                        @csrf
                        @method('PUT')
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="provider_price_list_id" class="form-label">{{ __('account.provider_supply.field_price_list') }}</label>
                                <select id="provider_price_list_id" name="provider_price_list_id" class="form-select @error('provider_price_list_id') is-invalid @enderror">
                                    <option value="">{{ __('account.provider_supply.field_price_list_none') }}</option>
                                    @foreach ($lists as $list)
                                        <option value="{{ $list->id }}" @selected((int) old('provider_price_list_id', $assignment?->provider_price_list_id) === (int) $list->id)>
                                            {{ $list->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('provider_price_list_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-3">
                                <label for="valid_from" class="form-label">{{ __('account.provider_supply.field_valid_from') }}</label>
                                <input id="valid_from" name="valid_from" type="date" class="form-control @error('valid_from') is-invalid @enderror" value="{{ old('valid_from', optional($assignment?->valid_from)->toDateString()) }}">
                                @error('valid_from') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-3">
                                <label for="valid_to" class="form-label">{{ __('account.provider_supply.field_valid_to') }}</label>
                                <input id="valid_to" name="valid_to" type="date" class="form-control @error('valid_to') is-invalid @enderror" value="{{ old('valid_to', optional($assignment?->valid_to)->toDateString()) }}">
                                @error('valid_to') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('account.relationships.index') }}" class="btn btn-outline-secondary">{{ __('account.provider_supply.back') }}</a>
                            <button type="submit" class="btn btn-primary">{{ __('account.provider_supply.save') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection
