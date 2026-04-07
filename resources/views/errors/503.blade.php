@php
    app()->setLocale(session('locale', config('app.locale')));
@endphp
@extends('errors.minimal')

@section('title', __('errors.503.title').' — '.config('app.name'))

@push('styles')
    @include('errors.partials.robot-error-styles')
@endpush

@section('content')
    @include('errors.partials.robot-error-body', ['errorKey' => '503', 'image' => '503-under maintenance.png'])
@endsection
