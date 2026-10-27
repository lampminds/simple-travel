@extends('site::layouts.main')

@section('meta_title', $title ?? $account->nick)

@section('content')
    @include('site::includes.navbar4')

    <section class="relative table w-full items-center py-36 bg-top bg-no-repeat bg-cover" style="background-image: url('{{ $account->homeBackgroundUrl() ?: site_asset('images/bg/cta.jpg') }}');">
        <div class="absolute inset-0 bg-gradient-to-b from-slate-900/60 via-slate-900/80 to-slate-900"></div>
        <div class="container relative">
            <div class="grid grid-cols-1 pb-8 text-center mt-10">
                <h3 class="text-4xl leading-normal tracking-wider font-semibold text-white">{{ $title }}</h3>
                @if (trim((string) $category->description) !== '')
                    <p class="text-white/70 text-lg max-w-xl mx-auto mt-4">{{ $category->description }}</p>
                @endif
            </div>
        </div>
    </section>
@endsection
