@extends('layouts.base', ['title' => $title ?? $account->nick])

@section('content')
    <div class="container py-5">
        <h1 class="h3 mb-0">{{ __('tenant.welcome_message', ['name' => $title ?? $account->nick]) }}</h1>
    </div>
@endsection
