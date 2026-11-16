@extends('layouts.base', ['title' => __('account.booking_fields.templates.page_title')])

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
                    :title="__('account.booking_fields.templates.heading')"
                    :subtitle="$account->commercial_name ?? $account->name ?? $account->nick"
                    :instructions="__('account.booking_fields.templates.instructions')"
                />
                <a class="btn btn-primary" href="{{ route('account.booking-templates.create') }}">
                    {{ __('account.booking_fields.templates.add') }}
                </a>
            </div>

            <div class="card mt-4">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr>
                                <th style="width: 3rem"></th>
                                <th>{{ __('account.booking_fields.templates.col_name') }}</th>
                                <th>{{ __('account.booking_fields.templates.col_items') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($templates as $template)
                                <tr>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-link text-body px-2" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="{{ __('account.booking_fields.actions') }}">
                                                <i class="icon-xs" data-feather="more-vertical"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('account.booking-templates.edit', $template) }}">{{ __('account.booking_fields.edit') }}</a>
                                                </li>
                                                <li>
                                                    <button class="dropdown-item" type="submit" form="delete-template-{{ $template->id }}">{{ __('account.booking_fields.templates.delete') }}</button>
                                                </li>
                                            </ul>
                                        </div>
                                        <form id="delete-template-{{ $template->id }}" method="POST" action="{{ route('account.booking-templates.destroy', $template) }}" class="d-none" onsubmit="return confirm(@js(__('account.booking_fields.templates.delete_confirm')))">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </td>
                                    <td>{{ $template->name }}</td>
                                    <td>{{ $template->items_count }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-muted">{{ __('account.booking_fields.templates.empty') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="d-flex justify-content-end mt-3">
                <a class="btn btn-outline-secondary" href="{{ route('account.booking-fields.index') }}">{{ __('account.booking_fields.templates.back_fields') }}</a>
            </div>
        </div>
    </section>

    <x-site-footer-simple />

@endsection

@section('script-bottom')
    @include('partials.feather-livewire-refresh')
@endsection
