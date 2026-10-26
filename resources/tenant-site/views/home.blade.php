@extends('site::layouts.main')

@section('meta_title', $title ?? $account->nick)

@section('content')
    @include('site::includes.navbar')

    {{-- Same vertical rhythm as site::index hero so the background and spacing match the template. --}}
    <section class="relative md:pt-72 md:pb-60 py-36 table w-full items-center bg-top bg-no-repeat bg-cover z-[1]"
        style="background-image: url('{{ site_asset('images/bg/1.jpg') }}');">
        <div class="absolute inset-0 bg-slate-900/40 z-0"></div>
        <div class="container relative z-[1]">
            <div class="grid md:grid-cols-12 grid-cols-1 items-center mt-10 gap-[30px]">
                <div class="lg:col-span-8 md:col-span-7 md:order-1 order-2">
                    <h5 class="text-3xl !font-dancing text-white">{{ __('tenant.hero_kicker') }}</h5>
                    <h1 class="font-bold text-white lg:leading-normal leading-normal text-4xl lg:text-6xl mb-6 mt-5">
                        {{ __('tenant.welcome_message', ['name' => $title ?? $account->nick]) }}
                    </h1>
                    <p class="text-white/70 text-xl max-w-xl">{{ __('tenant.hero_lead') }}</p>
                </div>

                <div class="lg:col-span-4 md:col-span-5 md:text-center md:order-2 order-1">
                    <a href="#!" data-type="youtube" data-id="S_CGed6E610" class="lightbox lg:h-24 h-20 lg:w-24 w-20 rounded-full shadow-lg dark:shadow-gray-800 inline-flex items-center justify-center bg-white hover:bg-red-500 text-red-500 hover:text-white duration-500 ease-in-out mx-auto">
                        <i class="mdi mdi-play inline-flex items-center justify-center text-3xl"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>

    @include('site::includes.Home.index-body')
@endsection
