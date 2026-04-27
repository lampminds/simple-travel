@extends('layouts.base', [
    'title' => ($invitationType ?? \App\Models\UserInvitation::TYPE_INTERNAL) === \App\Models\UserInvitation::TYPE_EXTERNAL
        ? __('invitations.page_title_company')
        : __('invitations.page_title_employee'),
])

@section('css')
    <style>
        .table-invitations-compact > :not(caption) > * > * {
            padding: 0.35rem 0.5rem !important;
        }
    </style>
@endsection

@section('content')

    @include('layouts.partials.dashboard-navbar', ['fixedWidth' => true, 'sticky' => false,'topbarColor' => 'navbar-light', 'classList' => 'mx-auto' ])

    <section class="position-relative p-3 bg-gradient2">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="page-title">
                        <h3 class="my-0">
                            {{ ($invitationType ?? \App\Models\UserInvitation::TYPE_INTERNAL) === \App\Models\UserInvitation::TYPE_EXTERNAL
                                ? __('invitations.section_title_company')
                                : __('invitations.section_title_employee') }}
                        </h3>
                        <p class="mt-1 fw-medium">
                            @if(($invitationType ?? \App\Models\UserInvitation::TYPE_INTERNAL) === \App\Models\UserInvitation::TYPE_EXTERNAL)
                                {{ __('invitations.section_intro_external', ['days' => $invitationExpirationDays ?? 7]) }}
                            @else
                                {{ __('invitations.section_intro_internal', ['days' => $invitationExpirationDays ?? 7]) }}
                            @endif
                        </p>
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
                            <form method="post" action="{{ route($storeRoute ?? 'account.invitations.store_employee') }}" class="row g-2 align-items-end mb-4">
                                @csrf
                                <x-form-validation-summary />
                                <div class="col-md-4">
                                    <label for="invite_name" class="form-label">{{ __('invitations.name') }}</label>
                                    <input type="text" name="name" id="invite_name" class="form-control @error('name') is-invalid @enderror"
                                           value="{{ old('name') }}" required autocomplete="name" maxlength="255"/>
                                    <x-form-field-error name="name" />
                                </div>
                                <div class="col-md-4">
                                    <label for="invite_email" class="form-label">{{ __('invitations.email') }}</label>
                                    <input type="email" name="email" id="invite_email" class="form-control @error('email') is-invalid @enderror"
                                           value="{{ old('email') }}" required autocomplete="off"/>
                                    <x-form-field-error name="email" />
                                </div>
                                @if (($invitationType ?? \App\Models\UserInvitation::TYPE_INTERNAL) === \App\Models\UserInvitation::TYPE_INTERNAL)
                                    <div class="col-md-2">
                                        <label for="invite_role_id" class="form-label">{{ __('invitations.role') }}</label>
                                        <select name="role_id" id="invite_role_id" class="form-select @error('role_id') is-invalid @enderror" required>
                                            <option value="">{{ __('invitations.role_placeholder') }}</option>
                                            @foreach ($assignableRoles ?? [] as $id => $label)
                                                <option value="{{ $id }}" @selected((string) old('role_id') === (string) $id)>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                        <x-form-field-error name="role_id" />
                                    </div>
                                @endif
                                <div class="col-md-{{ ($invitationType ?? \App\Models\UserInvitation::TYPE_INTERNAL) === \App\Models\UserInvitation::TYPE_INTERNAL ? '2' : '4' }}">
                                    <button type="submit" class="btn btn-primary w-100">{{ __('invitations.send') }}</button>
                                </div>
                            </form>

                            <form method="get" action="{{ route($indexRoute ?? 'account.invitations.employee') }}" class="row g-2 align-items-end mb-3">
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
                                <table class="table table-invitations-compact align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th>{{ __('invitations.col_name') }}</th>
                                            <th>{{ __('invitations.col_email') }}</th>
                                            <th class="text-nowrap">{{ __('invitations.col_role') }}</th>
                                            <th class="text-nowrap">{{ __('invitations.col_retries') }}</th>
                                            <th class="text-nowrap">{{ __('invitations.col_status') }}</th>
                                            <th class="text-nowrap">{{ __('invitations.col_expires') }}</th>
                                            <th class="text-end text-nowrap">{{ __('invitations.col_actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($invitations as $inv)
                                            @php
                                                $isSoon = $inv->status === \App\Models\UserInvitation::STATUS_PENDING && $inv->isExpiringSoon();
                                            @endphp
                                            <tr @class(['table-warning' => $isSoon])>
                                                <td>{{ $inv->name ?? '—' }}</td>
                                                <td>{{ $inv->email }}</td>
                                                <td class="text-nowrap">{{ $inv->role?->name ?? '—' }}</td>
                                                <td class="text-nowrap">
                                                    {{ (int) ($inv->send_attempts ?? 1) }} {{ __('invitations.retries_of') }} {{ (int) ($maxInvitationsRetries ?? 3) }}
                                                </td>
                                                <td class="text-nowrap">
                                                    @if ($isSoon)
                                                        <span class="badge text-bg-warning">{{ __('invitations.status_expiring') }}</span>
                                                    @endif
                                                    <span class="badge {{ $inv->status === \App\Models\UserInvitation::STATUS_PENDING ? 'text-bg-secondary' : 'text-bg-light text-dark' }}">
                                                        {{ __('invitations.status_'.$inv->status) }}
                                                    </span>
                                                </td>
                                                <td class="text-nowrap">
                                                    @if ($inv->expires_at)
                                                        <span title="{{ $inv->expires_at->timezone(config('app.timezone'))->format('Y-m-d H:i') }}">
                                                            {{ $inv->expires_at->diffForHumans() }}
                                                        </span>
                                                    @else
                                                        —
                                                    @endif
                                                </td>
                                                <td class="text-end text-nowrap">
                                                    @if ($inv->status === \App\Models\UserInvitation::STATUS_PENDING)
                                                        @if ((int) ($inv->send_attempts ?? 1) < (int) ($maxInvitationsRetries ?? 3))
                                                            <form method="post" action="{{ route('account.invitations.resend', $inv) }}" class="d-inline"
                                                                  onsubmit="return window.confirm(@json(__('invitations.resend_confirm')));">
                                                                @csrf
                                                                <input type="hidden" name="return_status" value="{{ $statusFilter ?? \App\Models\UserInvitation::STATUS_PENDING }}"/>
                                                                <button type="submit" class="btn btn-outline-primary btn-sm">{{ __('invitations.resend') }}</button>
                                                            </form>
                                                        @endif
                                                        <form method="post" action="{{ route('account.invitations.revoke', $inv) }}" class="d-inline ms-1"
                                                              onsubmit="return window.confirm(@json(__('invitations.revoke_confirm')));">
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
                                                <td colspan="7" class="text-muted">
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
