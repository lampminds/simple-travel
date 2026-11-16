@extends('layouts.base', ['title' => $mode === 'switch'
    ? __('invitations.switch_session_title', ['operator' => $operatorName])
    : __('invitations.wrong_session_title')])

@section('content')
    <div class="bg-gradient2 min-vh-100 align-items-center d-flex justify-content-center pt-2 pt-sm-5 pb-4 pb-sm-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-7 col-xl-6">
                    <div class="card shadow-sm">
                        <div class="card-body p-4 p-xl-5">
                            <div class="mb-4">
                                <x-site-logo on-white class="d-flex align-self-center" />
                            </div>

                            @if ($mode === 'switch')
                                <h1 class="h5 mb-2">{{ __('invitations.switch_session_title', ['operator' => $operatorName]) }}</h1>
                                <p class="text-muted mb-4">
                                    {{ __('invitations.switch_session_body', [
                                        'email' => $currentEmail,
                                        'company' => $companyName,
                                        'operator' => $operatorName,
                                    ]) }}
                                </p>
                                <form method="POST" action="{{ route('invitations.accept.switch', ['token' => $token]) }}" class="d-flex flex-wrap justify-content-end gap-2 mb-0">
                                    @csrf
                                    <a href="{{ route('account.dashboard') }}" class="btn btn-outline-secondary">
                                        {{ __('invitations.wrong_session_stay') }}
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        {{ __('invitations.switch_session_action') }}
                                    </button>
                                </form>
                            @else
                                <h1 class="h5 mb-2">{{ __('invitations.wrong_session_title') }}</h1>
                                <p class="text-muted mb-4">
                                    @if ($kind === 'internal')
                                        {{ __('invitations.wrong_session_body_internal', [
                                            'email' => $currentEmail,
                                            'invite_email' => $inviteEmail,
                                        ]) }}
                                    @else
                                        {{ __('invitations.wrong_session_body', [
                                            'email' => $currentEmail,
                                            'invite_email' => $inviteEmail,
                                            'company' => $companyName,
                                            'operator' => $operatorName,
                                        ]) }}
                                    @endif
                                </p>
                                <form method="POST" action="{{ route('invitations.accept.leave', ['token' => $token]) }}" class="d-flex flex-wrap justify-content-end gap-2 mb-0">
                                    @csrf
                                    <a href="{{ route('account.dashboard') }}" class="btn btn-outline-secondary">
                                        {{ __('invitations.wrong_session_stay') }}
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        {{ __('invitations.wrong_session_leave') }}
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
