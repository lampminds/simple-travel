<!-- resources/views/maintenance.blade.php -->
@extends('site::layouts.no-header')

@section('title', 'Maintenance Page')

@section('content')

<section class="relative h-screen flex items-center justify-center text-center bg-no-repeat bg-center bg-cover" style="background-image: url('{{ site_asset('images/bg/cta.jpg') }}');">
    <div class="absolute inset-0 bg-slate-900/25"></div>
    <div class="absolute inset-0 bg-gradient-to-b from-transparent via-slate-900/40 to-slate-900"></div>
    <div class="container relative">
        <div class="grid grid-cols-1">
            <img src="{{ site_asset('images/logo-light.png') }}" class="mx-auto" alt="">
            <h1 class="text-white mb-6 mt-8 md:text-5xl text-3xl font-bold">We Are Back Soon...</h1>
            <p class="text-white/70 text-lg max-w-xl mx-auto">Start working with Site that can provide everything you need to generate awareness, drive traffic, connect.</p>
        </div><!--end grid-->

        <div class="grid grid-cols-1 mt-10">
            <div class="text-center">
                <span id="maintenance" class="timer text-white text-[56px] tracking-[1px]"></span>
                <span class="block text-base font-semibold uppercase text-white">Minutes</span>
            </div>
        </div><!--end grid-->

        <div class="grid grid-cols-1 mt-6">
            <div class="text-center subcribe-form">
                <form class="relative mx-auto max-w-xl">
                    <input type="email" id="subemail" name="name" class="py-4 pe-40 ps-6 w-full h-[50px] outline-none text-slate-900 dark:text-white rounded-full bg-white/70 dark:bg-slate-900/70 border border-gray-100 dark:border-gray-700" placeholder="Enter your email id..">
                    <button type="submit" class="py-2 px-5 inline-block font-semibold tracking-wide align-middle duration-500 text-base text-center absolute top-[2px] end-[3px] h-[46px] bg-red-500 text-white rounded-full">Subcribe Now</button>
                </form><!--end form-->
            </div>
        </div><!--end grid-->
    </div><!--end container-->
</section><!--end section-->

@endsection