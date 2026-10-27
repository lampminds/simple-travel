@extends('site::layouts.auth')

@section('page_title', __('auth.verification.title'))

@section('auth_heading', __('auth.verification.heading'))

@section('auth_body')
    <p class="text-slate-400 mb-4">{{ __('auth.verification.intro') }}</p>

    @if (session('status'))
        <p class="text-slate-400 mb-4">
            @if (session('status') === 'verification-link-sent')
                {{ __('auth.verification.link_sent') }}
            @else
                {{ session('status') }}
            @endif
        </p>
    @endif

    <p class="text-slate-400 mb-6">{{ __('auth.verification.no_email') }}</p>

    <form method="POST" action="{{ route('verification.send') }}" class="text-start">
        @csrf
        <button type="submit" class="py-2 px-5 inline-block tracking-wide align-middle duration-500 text-base text-center bg-red-500 text-white rounded-md w-full">{{ __('auth.verification.resend_button') }}</button>
    </form>

    <form method="POST" action="{{ route('logout') }}" class="text-center mt-4">
        @csrf
        <button type="submit" class="text-slate-400 bg-transparent border-0 cursor-pointer">{{ __('tenant.menu_logout') }}</button>
    </form>
@endsection
