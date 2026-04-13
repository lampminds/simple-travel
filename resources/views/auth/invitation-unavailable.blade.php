@php
    app()->setLocale(session('locale', config('app.locale')));
@endphp
@extends('errors.minimal')

@section('title', __('errors.invitation.title').' — '.config('app.name'))

@push('styles')
    @include('errors.partials.robot-error-styles')
@endpush

@section('content')
    @php
        $description = match ($reason ?? 'invalid') {
            'expired' => __('errors.invitation.description_expired'),
            'revoked' => __('errors.invitation.description_revoked'),
            'declined' => __('errors.invitation.description_declined'),
            'accepted' => __('errors.invitation.description_accepted'),
            default => __('errors.invitation.description_invalid'),
        };
    @endphp
    <main class="err-robot">
        <div class="err-robot__stage">
            <p class="err-robot__code">{{ __('errors.invitation.code') }}</p>
            <h1 class="err-robot__title">{{ __('errors.invitation.title') }}</h1>
            <p class="err-robot__desc">{{ $description }}</p>
        </div>
        <div class="err-robot__visual">
            <img
                src="{{ asset('images/error/403-access denied.png') }}"
                width="800"
                height="600"
                alt=""
                decoding="async"
            >
            <div class="err-robot__cta-wrap">
                <a class="err-robot__cta" href="{{ url('/') }}">{{ __('errors.invitation.home') }}</a>
            </div>
        </div>
    </main>
@endsection
