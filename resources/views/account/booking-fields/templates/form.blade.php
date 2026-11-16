@extends('layouts.base', ['title' => $template ? __('account.booking_fields.templates.edit_page_title') : __('account.booking_fields.templates.create_page_title')])

@section('content')

    @include('layouts.partials.dashboard-navbar', ['fixedWidth' => true, 'sticky' => false, 'topbarColor' => 'navbar-light', 'classList' => 'mx-auto'])

    <section class="position-relative p-3 bg-gradient2">
        <div class="container">
            <x-account-page-header
                :title="$template ? __('account.booking_fields.templates.edit_heading', ['name' => $template->name]) : __('account.booking_fields.templates.create_heading')"
                :subtitle="$account->commercial_name ?? $account->name ?? $account->nick"
                :instructions="__('account.booking_fields.templates.form_instructions')"
            />

            <form method="POST" action="{{ $template ? route('account.booking-templates.update', $template) : route('account.booking-templates.store') }}" class="card mt-4">
                @csrf
                @if ($template)
                    @method('PUT')
                @endif
                <div class="card-body">
                    <div class="mb-4">
                        <label class="form-label" for="template-name">{{ __('account.booking_fields.templates.col_name') }}</label>
                        <input
                            type="text"
                            id="template-name"
                            name="name"
                            class="form-control @error('name') is-invalid @enderror"
                            value="{{ old('name', $template?->name ?? '') }}"
                            maxlength="120"
                            required
                        >
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    @error('items')
                        <div class="alert alert-danger" role="alert">{{ $message }}</div>
                    @enderror

                    <div id="booking-order-body">
                        @foreach ($rows as $index => $row)
                            @include('account.booking-fields.templates.partials.item', ['index' => $index, 'row' => $row])
                        @endforeach
                    </div>

                    <div class="d-flex flex-wrap gap-2 justify-content-between mt-4">
                        <button type="button" class="btn btn-outline-primary" id="add-template-item">
                            {{ __('account.booking_fields.templates.add_item') }}
                        </button>
                        <div class="d-flex flex-wrap gap-2">
                            <a class="btn btn-outline-secondary" href="{{ route('account.booking-templates.index') }}">{{ __('account.booking_fields.cancel') }}</a>
                            <button type="submit" class="btn btn-primary">{{ __('account.booking_fields.save') }}</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>

    <template id="booking-template-item-prototype">
        @include('account.booking-fields.templates.partials.item', [
            'index' => '__INDEX__',
            'row' => [
                'id' => null,
                'scope' => \App\Models\ProviderBookingField::SCOPE_PASSENGER,
                'input_type' => \App\Models\ProviderBookingField::TYPE_TEXT,
                'is_required' => true,
                'sort_order' => 10,
                'translations' => [],
            ],
        ])
    </template>

    <x-site-footer-simple />

@endsection

@section('script-bottom')
    @include('account.booking-fields.partials.order-script')
    @include('account.booking-fields.templates.partials.items-script')
    @include('partials.feather-livewire-refresh')
@endsection
