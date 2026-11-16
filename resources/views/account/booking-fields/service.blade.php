@extends('layouts.base', ['title' => __('account.booking_fields.service_title')])

@section('content')

    @include('layouts.partials.dashboard-navbar', ['fixedWidth' => true, 'sticky' => false, 'topbarColor' => 'navbar-light', 'classList' => 'mx-auto'])

    <section class="position-relative p-3 bg-gradient2">
        <div class="container">
            @if (session('status'))
                <div class="alert alert-success mb-3" role="alert">{{ session('status') }}</div>
            @endif

            <div class="d-flex flex-column flex-md-row justify-content-md-between align-items-md-start gap-3">
                <x-account-page-header
                    class="flex-grow-1"
                    :title="__('account.booking_fields.service_heading', ['service' => $service->name !== '' ? $service->name : '#'.$service->id])"
                    :subtitle="$account->commercial_name ?? $account->name ?? $account->nick"
                    :instructions="__('account.booking_fields.service_instructions')"
                />
                <div class="d-flex flex-wrap gap-2">
                    <a class="btn btn-outline-primary" href="{{ route('account.booking-templates.index') }}">
                        {{ __('account.booking_fields.templates.link') }}
                    </a>
                    <a class="btn btn-outline-primary" href="{{ route('account.booking-fields.create') }}">
                        {{ __('account.booking_fields.add') }}
                    </a>
                </div>
            </div>

            @if ($templates->isNotEmpty())
                <form method="POST" action="{{ route('account.services.booking-fields.copy', $service) }}" class="card mt-4">
                    @csrf
                    <div class="card-body">
                        <h2 class="h5">{{ __('account.booking_fields.templates.copy_heading') }}</h2>
                        <p class="text-muted">{{ __('account.booking_fields.templates.copy_intro') }}</p>
                        <div class="row g-3 align-items-end">
                            <div class="col-md-6">
                                <label class="form-label" for="booking-template">{{ __('account.booking_fields.templates.field_template') }}</label>
                                <select id="booking-template" name="template_id" class="form-select @error('template_id') is-invalid @enderror" required>
                                    <option value="">{{ __('account.booking_fields.templates.copy_placeholder') }}</option>
                                    @foreach ($templates as $template)
                                        <option value="{{ $template->id }}" @selected((string) old('template_id') === (string) $template->id)>{{ $template->name }}</option>
                                    @endforeach
                                </select>
                                @error('template_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" class="btn btn-primary">{{ __('account.booking_fields.templates.copy_action') }}</button>
                        </div>
                    </div>
                </form>
            @endif

            @if ($fields->isEmpty())
                <div class="alert alert-info mt-4" role="alert">
                    @if ($templates->isNotEmpty())
                        {{ __('account.booking_fields.service_empty_with_templates') }}
                    @else
                        {{ __('account.booking_fields.service_empty') }}
                    @endif
                    <a href="{{ route('account.booking-fields.index') }}">{{ __('account.booking_fields.service_open_catalog') }}</a>
                </div>
                <div class="d-flex justify-content-end mt-3">
                    <a class="btn btn-outline-secondary" href="{{ route('catalog') }}">{{ __('account.booking_fields.back_catalog') }}</a>
                </div>
            @else
                @php
                    $posted = old('assignments');
                    $usePosted = is_array($posted);
                    $orderedFields = $fields->sort(function ($left, $right) use ($usePosted, $posted, $assignments): int {
                        $sortOf = function ($field) use ($usePosted, $posted, $assignments): int {
                            if ($usePosted) {
                                $row = $posted[$field->id] ?? $posted[(string) $field->id] ?? [];

                                return (int) ($row['sort_order'] ?? 9999);
                            }

                            $stored = $assignments->get($field->id);

                            return $stored !== null ? (int) $stored->sort_order : 100000 + (int) $field->sort_order;
                        };

                        return [$sortOf($left), (int) $left->id] <=> [$sortOf($right), (int) $right->id];
                    })->values();
                @endphp
                <form method="POST" action="{{ route('account.services.booking-fields.update', $service) }}" class="card mt-4">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        @error('assignments')
                            <div class="alert alert-danger" role="alert">{{ $message }}</div>
                        @enderror
                        <div class="table-responsive">
                            <table class="table align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>{{ __('account.booking_fields.assigned') }}</th>
                                        <th class="text-center text-nowrap" style="width: 1%;">{{ __('account.booking_fields.col_order') }}</th>
                                        <th>{{ __('account.booking_fields.col_label') }}</th>
                                        <th>{{ __('account.booking_fields.col_scope') }}</th>
                                        <th>{{ __('account.booking_fields.col_type') }}</th>
                                        <th>{{ __('account.booking_fields.required') }}</th>
                                    </tr>
                                </thead>
                                <tbody id="booking-order-body">
                                    @foreach ($orderedFields as $field)
                                        @php
                                            $stored = $assignments->get($field->id);
                                            $postedRow = $usePosted ? ($posted[$field->id] ?? $posted[(string) $field->id] ?? []) : null;
                                            $isAssigned = $usePosted
                                                ? ! empty($postedRow['assigned'])
                                                : $stored !== null;
                                            $isRequired = $usePosted
                                                ? ! empty($postedRow['is_required'])
                                                : ($stored === null || (bool) $stored->is_required);
                                            $sortOrder = $usePosted
                                                ? ($postedRow['sort_order'] ?? $field->sort_order)
                                                : ($stored->sort_order ?? $field->sort_order);
                                            $label = $field->labelForDisplay();
                                        @endphp
                                        <tr class="booking-order-row">
                                            <td>
                                                <input
                                                    type="checkbox"
                                                    class="form-check-input"
                                                    name="assignments[{{ $field->id }}][assigned]"
                                                    value="1"
                                                    aria-label="{{ __('account.booking_fields.assigned') }}: {{ $label !== '' ? $label : __('account.booking_fields.untitled') }}"
                                                    @checked($isAssigned)
                                                >
                                            </td>
                                            <td class="text-center text-nowrap">
                                                <input type="hidden" class="booking-order-input" name="assignments[{{ $field->id }}][sort_order]" value="{{ $sortOrder }}">
                                                @include('account.booking-fields.partials.order-buttons')
                                            </td>
                                            <td>
                                                {{ $label !== '' ? $label : __('account.booking_fields.untitled') }}
                                                @if (! $field->is_active)
                                                    <span class="badge bg-secondary">{{ __('account.booking_fields.inactive') }}</span>
                                                @endif
                                            </td>
                                            <td>{{ $field->scopeLabel() }}</td>
                                            <td>{{ $field->inputTypeLabel() }}</td>
                                            <td>
                                                <input
                                                    type="checkbox"
                                                    class="form-check-input"
                                                    name="assignments[{{ $field->id }}][is_required]"
                                                    value="1"
                                                    aria-label="{{ __('account.booking_fields.required') }}"
                                                    @checked($isRequired)
                                                >
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex flex-wrap gap-2 justify-content-end mt-4">
                            <a class="btn btn-outline-secondary" href="{{ route('catalog') }}">{{ __('account.booking_fields.back_catalog') }}</a>
                            <button type="submit" class="btn btn-primary">{{ __('account.booking_fields.save') }}</button>
                        </div>
                    </div>
                </form>
            @endif
        </div>
    </section>

    <x-site-footer-simple />

@endsection

@section('script-bottom')
    @include('account.booking-fields.partials.order-script')
@endsection
