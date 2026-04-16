@php
    app()->setLocale(session('locale', config('app.locale')));
@endphp
@extends('errors.minimal')

@section('title', ($customTitle ?? __('errors.403.title')).' — '.config('app.name'))

@push('styles')
    @include('errors.partials.robot-error-styles')
@endpush

@section('content')
    @include('errors.partials.robot-error-body', [
        'errorKey' => '403',
        'image' => '403-access denied.png',
        'title' => $customTitle ?? null,
        'description' => $customDescription ?? null,
    ])
@endsection
