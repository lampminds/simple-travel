@extends('layouts.base', ['title' => __('invitations.page_title')])

@section('content')

    @include('layouts.partials.dashboard-navbar', ['fixedWidth' => true, 'sticky' => false,'topbarColor' => 'navbar-light', 'classList' => 'mx-auto' ])

    <section class="position-relative p-3 bg-gradient2">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="page-title">
                        <h3 class="my-0">{{ __('invitations.section_title') }}</h3>
                        <p class="mt-1 fw-medium">{{ __('invitations.section_intro', ['days' => $invitationExpirationDays ?? 7]) }}</p>
                    </div>
                </div>
            </div>

            @if (session('status'))
                <div class="alert alert-success mb-3" role="alert">{{ session('status') }}</div>
            @endif

            <div class="row mt-2">
                <div class="col-lg-12">
                    <div class="card mb-4 border-primary border-opacity-25">
                        <div class="card-body">
                            <form method="post" action="{{ route('account.invitations.store') }}" class="row g-2 align-items-end mb-4">
                                @csrf
                                <x-form-validation-summary />
                                <div class="col-md-4">
                                    <label for="invite_email" class="form-label">{{ __('invitations.email') }}</label>
                                    <input type="email" name="email" id="invite_email" class="form-control @error('email') is-invalid @enderror"
                                           value="{{ old('email') }}" required autocomplete="off"/>
                                    <x-form-field-error name="email" />
                                </div>
                                <div class="col-md-4">
                                    <label for="invite_type" class="form-label">{{ __('invitations.type') }}</label>
                                    <select name="type" id="invite_type" class="form-select @error('type') is-invalid @enderror" required>
                                        <option value="internal" @selected(old('type', 'internal') === 'internal')>{{ __('invitations.type_internal') }}</option>
                                        <option value="external" @selected(old('type', 'internal') === 'external')>{{ __('invitations.type_external') }}</option>
                                    </select>
                                    <x-form-field-error name="type" />
                                </div>
                                <div class="col-md-4">
                                    <button type="submit" class="btn btn-primary w-100">{{ __('invitations.send') }}</button>
                                </div>
                            </form>

                            <form method="get" action="{{ route('account.invitations.index') }}" class="row g-2 align-items-end mb-3">
                                <div class="col-auto">
                                    <label for="invitations_status_filter" class="form-label mb-0">{{ __('invitations.filter_status') }}</label>
                                </div>
                                <div class="col-md-3 col-lg-2">
                                    <select name="status" id="invitations_status_filter" class="form-select form-select-sm"
                                            onchange="this.form.submit()">
                                        <option value="{{ \App\Models\UserInvitation::STATUS_PENDING }}" @selected(($statusFilter ?? \App\Models\UserInvitation::STATUS_PENDING) === \App\Models\UserInvitation::STATUS_PENDING)>
                                            {{ __('invitations.status_pending') }}
                                        </option>
                                        <option value="{{ \App\Models\UserInvitation::STATUS_ACCEPTED }}" @selected(($statusFilter ?? '') === \App\Models\UserInvitation::STATUS_ACCEPTED)>
                                            {{ __('invitations.status_accepted') }}
                                        </option>
                                        <option value="{{ \App\Models\UserInvitation::STATUS_DECLINED }}" @selected(($statusFilter ?? '') === \App\Models\UserInvitation::STATUS_DECLINED)>
                                            {{ __('invitations.status_declined') }}
                                        </option>
                                        <option value="{{ \App\Models\UserInvitation::STATUS_EXPIRED }}" @selected(($statusFilter ?? '') === \App\Models\UserInvitation::STATUS_EXPIRED)>
                                            {{ __('invitations.status_expired') }}
                                        </option>
                                        <option value="{{ \App\Models\UserInvitation::STATUS_REVOKED }}" @selected(($statusFilter ?? '') === \App\Models\UserInvitation::STATUS_REVOKED)>
                                            {{ __('invitations.status_revoked') }}
                                        </option>
                                        <option value="all" @selected(($statusFilter ?? '') === 'all')>
                                            {{ __('invitations.filter_all') }}
                                        </option>
                                    </select>
                                </div>
                            </form>

                            <div class="table-responsive">
                                <table class="table table-sm align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th>{{ __('invitations.col_email') }}</th>
                                            <th>{{ __('invitations.col_type') }}</th>
                                            <th>{{ __('invitations.col_status') }}</th>
                                            <th>{{ __('invitations.col_expires') }}</th>
                                            <th class="text-end">{{ __('invitations.col_actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($invitations as $inv)
                                            @php
                                                $isSoon = $inv->status === \App\Models\UserInvitation::STATUS_PENDING && $inv->isExpiringSoon();
                                            @endphp
                                            <tr @class(['table-warning' => $isSoon])>
                                                <td>{{ $inv->email }}</td>
                                                <td>{{ $inv->type === \App\Models\UserInvitation::TYPE_INTERNAL ? __('invitations.type_internal') : __('invitations.type_external') }}</td>
                                                <td>
                                                    @if ($isSoon)
                                                        <span class="badge text-bg-warning">{{ __('invitations.status_expiring') }}</span>
                                                    @endif
                                                    <span class="badge {{ $inv->status === \App\Models\UserInvitation::STATUS_PENDING ? 'text-bg-secondary' : 'text-bg-light text-dark' }}">
                                                        {{ __('invitations.status_'.$inv->status) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @if ($inv->expires_at)
                                                        <span title="{{ $inv->expires_at->timezone(config('app.timezone'))->format('Y-m-d H:i') }}">
                                                            {{ $inv->expires_at->diffForHumans() }}
                                                        </span>
                                                    @else
                                                        —
                                                    @endif
                                                </td>
                                                <td class="text-end">
                                                    @if ($inv->status === \App\Models\UserInvitation::STATUS_PENDING)
                                                        <form method="post" action="{{ route('account.invitations.revoke', $inv) }}" class="d-inline"
                                                              onsubmit="return confirm(@json(__('invitations.revoke_confirm')));">
                                                            @csrf
                                                            <input type="hidden" name="return_status" value="{{ $statusFilter ?? \App\Models\UserInvitation::STATUS_PENDING }}"/>
                                                            <button type="submit" class="btn btn-outline-danger btn-sm">{{ __('invitations.revoke') }}</button>
                                                        </form>
                                                    @else
                                                        —
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-muted">
                                                    @if (($statusFilter ?? \App\Models\UserInvitation::STATUS_PENDING) === \App\Models\UserInvitation::STATUS_PENDING)
                                                        {{ __('invitations.empty') }}
                                                    @else
                                                        {{ __('invitations.empty_filtered') }}
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <x-site-footer-simple />

@endsection
