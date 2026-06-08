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
                    <x-account-page-header
                        :title="($invitationType ?? \App\Models\UserInvitation::TYPE_INTERNAL) === \App\Models\UserInvitation::TYPE_EXTERNAL
                            ? __('invitations.section_title_company')
                            : __('invitations.section_title_employee')"
                        :instructions="($invitationType ?? \App\Models\UserInvitation::TYPE_INTERNAL) === \App\Models\UserInvitation::TYPE_EXTERNAL
                            ? __('invitations.section_intro_external', ['days' => $invitationExpirationDays ?? 7])
                            : __('invitations.section_intro_internal', ['days' => $invitationExpirationDays ?? 7])"
                    />
                </div>
            </div>

            @if (session('status'))
                <div class="alert alert-success mb-3" role="alert">{{ session('status') }}</div>
            @endif

            <div class="row mt-2">
                <div class="col-lg-12">
                    <div class="card mb-3 border-primary border-opacity-25 shadow-sm">
                        <div class="card-body">
                            <h2 class="h6 fw-semibold text-body-secondary mb-3 pb-2 border-bottom border-light">
                                {{ __('invitations.card_form_heading') }}
                            </h2>
                            <form method="post" action="{{ route($storeRoute ?? 'account.invitations.store_employee') }}" class="mb-0">
                                @csrf
                                <x-form-validation-summary />
                                <div class="row g-2 align-items-end">
                                    @if (($invitationType ?? \App\Models\UserInvitation::TYPE_INTERNAL) === \App\Models\UserInvitation::TYPE_EXTERNAL)
                                        <div class="col-12 col-md-6 col-lg-4 col-xl-3">
                                            <label for="invite_company_name" class="form-label">{{ __('invitations.company_name') }}</label>
                                            <input type="text" name="company_name" id="invite_company_name"
                                                   class="form-control @error('company_name') is-invalid @enderror"
                                                   value="{{ old('company_name') }}" autocomplete="organization" maxlength="255"/>
                                            <x-form-field-error name="company_name" />
                                        </div>
                                        <div class="col-12 col-md-6 col-lg-4 col-xl-3">
                                            <label for="invite_name" class="form-label">{{ __('invitations.invitee_name') }}</label>
                                            <input type="text" name="name" id="invite_name" class="form-control @error('name') is-invalid @enderror"
                                                   value="{{ old('name') }}" required autocomplete="name" maxlength="255"/>
                                            <x-form-field-error name="name" />
                                        </div>
                                    @else
                                        <div class="col-12 col-md-6 col-lg-4 col-xl-3">
                                            <label for="invite_name" class="form-label">{{ __('invitations.name') }}</label>
                                            <input type="text" name="name" id="invite_name" class="form-control @error('name') is-invalid @enderror"
                                                   value="{{ old('name') }}" required autocomplete="name" maxlength="255"/>
                                            <x-form-field-error name="name" />
                                        </div>
                                    @endif
                                    <div class="col-12 col-md-6 col-lg-4 col-xl-3">
                                        <label for="invite_email" class="form-label">{{ __('invitations.email') }}</label>
                                        <input type="email" name="email" id="invite_email" class="form-control @error('email') is-invalid @enderror"
                                               value="{{ old('email') }}" required autocomplete="off"/>
                                        <x-form-field-error name="email" />
                                    </div>
                                    @if (($invitationType ?? \App\Models\UserInvitation::TYPE_INTERNAL) === \App\Models\UserInvitation::TYPE_EXTERNAL && ! empty($targetAccountChoices))
                                        <div class="col-12">
                                            <div class="alert alert-info py-2 mb-0">
                                                {{ __('invitations.choose_target_account_help') }}
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-8 col-lg-6">
                                            <label for="invited_account_id" class="form-label">{{ __('invitations.target_account') }}</label>
                                            <select name="invited_account_id" id="invited_account_id"
                                                    class="form-select @error('invited_account_id') is-invalid @enderror" required>
                                                <option value="">{{ __('invitations.target_account_placeholder') }}</option>
                                                @foreach ($targetAccountChoices as $choice)
                                                    <option value="{{ $choice['id'] }}" @selected((string) old('invited_account_id') === (string) $choice['id'])>
                                                        {{ $choice['label'] }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <x-form-field-error name="invited_account_id" />
                                        </div>
                                    @endif
                                    @if (($invitationType ?? \App\Models\UserInvitation::TYPE_INTERNAL) === \App\Models\UserInvitation::TYPE_INTERNAL)
                                        <div class="col-12 col-md-6 col-lg-4 col-xl-3">
                                            <label for="invite_role_id" class="form-label">{{ __('invitations.role') }}</label>
                                            <select name="role_id" id="invite_role_id" class="form-select @error('role_id') is-invalid @enderror" required>
                                                <option value="">{{ __('invitations.role_placeholder') }}</option>
                                                @foreach ($assignableRoles ?? [] as $id => $label)
                                                    <option value="{{ $id }}" @selected((string) old('role_id') === (string) $id)>{{ $label }}</option>
                                                @endforeach
                                            </select>
                                            <x-form-field-error name="role_id" />
                                        </div>
                                        <div class="col-12 col-md-6 col-lg-4 col-xl-3 d-grid d-lg-none">
                                            <label class="form-label d-md-none">&nbsp;</label>
                                            <button type="submit" class="btn btn-primary">{{ __('invitations.send') }}</button>
                                        </div>
                                    @else
                                        <div class="col-md-4">
                                            <label class="form-label d-none d-md-block">&nbsp;</label>
                                            <button type="submit" class="btn btn-primary w-100">{{ __('invitations.send') }}</button>
                                        </div>
                                    @endif
                                </div>
                                @if (($invitationType ?? \App\Models\UserInvitation::TYPE_INTERNAL) === \App\Models\UserInvitation::TYPE_INTERNAL)
                                    <div class="row g-2 align-items-end mt-2 mt-lg-3">
                                        <div class="col-12 col-md-6 col-lg-4 col-xl-3">
                                            <label for="invite_contact_department_id" class="form-label">{{ __('invitations.invitee_department') }}</label>
                                            <select name="contact_department_id" id="invite_contact_department_id"
                                                    class="form-select @error('contact_department_id') is-invalid @enderror" required>
                                                <option value="">{{ __('invitations.invitee_department_placeholder') }}</option>
                                                @foreach ($contactDepartments ?? [] as $dept)
                                                    <option value="{{ $dept->id }}" @selected((string) old('contact_department_id') === (string) $dept->id)>
                                                        {{ $dept->code }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <x-form-field-error name="contact_department_id" />
                                        </div>
                                        <div class="col-12 col-md-6 col-lg-4 col-xl-3">
                                            <label for="invite_contact_position_id" class="form-label">{{ __('invitations.invitee_position') }}</label>
                                            <select name="contact_position_id" id="invite_contact_position_id"
                                                    class="form-select @error('contact_position_id') is-invalid @enderror" required>
                                                <option value="">{{ __('invitations.invitee_position_placeholder') }}</option>
                                                @foreach ($contactPositions ?? [] as $pos)
                                                    <option value="{{ $pos->id }}" @selected((string) old('contact_position_id') === (string) $pos->id)>
                                                        {{ $pos->code }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <x-form-field-error name="contact_position_id" />
                                        </div>
                                        <div class="col-12 col-md-6 col-lg-4 col-xl-3 d-none d-lg-grid">
                                            <label class="form-label d-none d-lg-block">&nbsp;</label>
                                            <button type="submit" class="btn btn-primary">{{ __('invitations.send') }}</button>
                                        </div>
                                    </div>
                                @endif
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="card mb-4 border-primary border-opacity-25 shadow-sm">
                        <div class="card-body">
                            <h2 class="h6 fw-semibold text-body-secondary mb-3 pb-2 border-bottom border-light">
                                {{ __('invitations.card_list_heading') }}
                            </h2>
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
                                            @if (($invitationType ?? \App\Models\UserInvitation::TYPE_INTERNAL) === \App\Models\UserInvitation::TYPE_EXTERNAL)
                                                <th>{{ __('invitations.col_company_name') }}</th>
                                                <th class="text-nowrap">{{ __('invitations.col_company_kind') }}</th>
                                                <th>{{ __('invitations.col_invitee_name') }}</th>
                                            @else
                                                <th>{{ __('invitations.col_name') }}</th>
                                            @endif
                                            <th>{{ __('invitations.col_email') }}</th>
                                            @if (($invitationType ?? \App\Models\UserInvitation::TYPE_INTERNAL) === \App\Models\UserInvitation::TYPE_EXTERNAL)
                                                <th class="text-nowrap">{{ __('invitations.col_target_company') }}</th>
                                            @endif
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
                                                @if (($invitationType ?? \App\Models\UserInvitation::TYPE_INTERNAL) === \App\Models\UserInvitation::TYPE_EXTERNAL)
                                                    <td>{{ $inv->company_name ?? '—' }}</td>
                                                    <td class="text-nowrap">
                                                        @php $inviteeKind = $inv->resolveInviteeCompanyKind(); @endphp
                                                        @if ($inviteeKind === 'agency')
                                                            {{ __('invitations.company_kind_agency') }}
                                                        @elseif ($inviteeKind === 'provider')
                                                            {{ __('invitations.company_kind_provider') }}
                                                        @else
                                                            <span class="text-muted">{{ __('invitations.company_kind_pending') }}</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $inv->name ?? '—' }}</td>
                                                @else
                                                    <td>{{ $inv->name ?? '—' }}</td>
                                                @endif
                                                <td>{{ $inv->email }}</td>
                                                @if (($invitationType ?? \App\Models\UserInvitation::TYPE_INTERNAL) === \App\Models\UserInvitation::TYPE_EXTERNAL)
                                                    <td class="text-nowrap">
                                                        @if ($inv->invitedAccount)
                                                            {{ $inv->invitedAccount->commercial_name ?? $inv->invitedAccount->name ?? '#'.$inv->invitedAccount->id }}
                                                        @else
                                                            —
                                                        @endif
                                                    </td>
                                                @endif
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
                                                        <span title="{{ locale_datetime($inv->expires_at->timezone(config('app.timezone'))) }}">
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
                                                <td colspan="{{ ($invitationType ?? \App\Models\UserInvitation::TYPE_INTERNAL) === \App\Models\UserInvitation::TYPE_EXTERNAL ? 10 : 7 }}" class="text-muted">
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
