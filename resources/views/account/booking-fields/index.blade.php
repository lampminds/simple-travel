@extends('layouts.base', ['title' => __('account.booking_fields.page_title')])

@section('content')

    @include('layouts.partials.dashboard-navbar', ['fixedWidth' => true, 'sticky' => false, 'topbarColor' => 'navbar-light', 'classList' => 'mx-auto'])

    <section class="position-relative p-3 bg-gradient2">
        <div class="container">
            @if (session('status'))
                <div class="alert alert-success mb-3" role="alert">{{ session('status') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger mb-3" role="alert">{{ session('error') }}</div>
            @endif

            <div class="d-flex flex-column flex-md-row justify-content-md-between align-items-md-start gap-3">
                <x-account-page-header
                    class="flex-grow-1"
                    :title="__('account.booking_fields.heading')"
                    :subtitle="$account->commercial_name ?? $account->name ?? $account->nick"
                    :instructions="__('account.booking_fields.instructions')"
                />
                <div class="d-flex flex-wrap gap-2">
                    <a class="btn btn-outline-primary" href="{{ route('account.booking-templates.index') }}">
                        {{ __('account.booking_fields.templates.link') }}
                    </a>
                    <a class="btn btn-primary" href="{{ route('account.booking-fields.create') }}">
                        {{ __('account.booking_fields.add') }}
                    </a>
                </div>
            </div>

            <div class="card mt-4">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr>
                                <th style="width: 3rem"></th>
                                <th class="text-center text-nowrap" style="width: 1%;">{{ __('account.booking_fields.col_order') }}</th>
                                <th>{{ __('account.booking_fields.col_label') }}</th>
                                <th>{{ __('account.booking_fields.col_scope') }}</th>
                                <th>{{ __('account.booking_fields.col_type') }}</th>
                                <th>{{ __('account.booking_fields.col_status') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($fields as $field)
                                <tr>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-link text-body px-2" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="{{ __('account.booking_fields.actions') }}">
                                                <i class="icon-xs" data-feather="more-vertical"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('account.booking-fields.edit', $field) }}">{{ __('account.booking_fields.edit') }}</a>
                                                </li>
                                                <li>
                                                    <button class="dropdown-item" type="submit" form="toggle-field-{{ $field->id }}">
                                                        {{ $field->is_active ? __('account.booking_fields.deactivate') : __('account.booking_fields.activate') }}
                                                    </button>
                                                </li>
                                                <li>
                                                    <button class="dropdown-item" type="submit" form="delete-field-{{ $field->id }}">
                                                        {{ __('account.booking_fields.delete') }}
                                                    </button>
                                                </li>
                                            </ul>
                                        </div>
                                        <form id="toggle-field-{{ $field->id }}" method="POST" action="{{ route('account.booking-fields.toggle', $field) }}" class="d-none">
                                            @csrf
                                        </form>
                                        <form id="delete-field-{{ $field->id }}" method="POST" action="{{ route('account.booking-fields.destroy', $field) }}" class="d-none" onsubmit="return confirm(@js(__('account.booking_fields.delete_confirm')))">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </td>
                                    <td class="text-center text-nowrap">
                                        <form method="POST" action="{{ route('account.booking-fields.move', [$field, 'up']) }}" class="d-inline-block">
                                            @csrf
                                            @method('PATCH')
                                            <div class="btn-group btn-group-sm" role="group" aria-label="{{ __('account.booking_fields.col_order') }}">
                                                <button
                                                    type="submit"
                                                    class="btn btn-outline-secondary"
                                                    title="{{ __('account.booking_fields.move_up') }}"
                                                    @disabled($loop->first)
                                                >
                                                    <i data-feather="chevron-up" class="icon icon-xs" aria-hidden="true"></i>
                                                </button>
                                                <button
                                                    type="submit"
                                                    class="btn btn-outline-secondary"
                                                    title="{{ __('account.booking_fields.move_down') }}"
                                                    formaction="{{ route('account.booking-fields.move', [$field, 'down']) }}"
                                                    @disabled($loop->last)
                                                >
                                                    <i data-feather="chevron-down" class="icon icon-xs" aria-hidden="true"></i>
                                                </button>
                                            </div>
                                        </form>
                                    </td>
                                    <td>{{ $field->labelForDisplay() !== '' ? $field->labelForDisplay() : __('account.booking_fields.untitled') }}</td>
                                    <td>{{ $field->scopeLabel() }}</td>
                                    <td>{{ $field->inputTypeLabel() }}</td>
                                    <td>
                                        @if ($field->is_active)
                                            <span class="badge bg-success">{{ __('account.booking_fields.active') }}</span>
                                        @else
                                            <span class="badge bg-secondary">{{ __('account.booking_fields.inactive') }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-muted">{{ __('account.booking_fields.empty') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

    <x-site-footer-simple />

@endsection

@section('script-bottom')
    @include('partials.feather-livewire-refresh')
@endsection
