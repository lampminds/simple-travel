@php
    app()->setLocale(session('locale', config('app.locale')));
@endphp
@extends('errors.minimal')

@section('title', __('errors.404.title').' — '.config('app.name'))

@push('styles')
    @include('errors.partials.robot-error-styles')
@endpush

@section('content')
    @include('errors.partials.robot-error-body', ['errorKey' => '404', 'image' => '404-not found.png'])
@endsection
