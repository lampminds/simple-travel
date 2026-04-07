@php
    app()->setLocale(session('locale', config('app.locale')));
@endphp
@extends('errors.minimal')

@section('title', __('errors.500.title').' — '.config('app.name'))

@push('styles')
    @include('errors.partials.robot-error-styles')
@endpush

@section('content')
    @include('errors.partials.robot-error-body', ['errorKey' => '500', 'image' => '500-internal error.png'])
@endsection
