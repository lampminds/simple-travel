<!-- resources/views/aboutus.blade.php -->
@extends('site::layouts.main')

@section('title', 'Aboutus Page')

@section('content')

@include('site::includes.navbar2')
        
<!-- Start Hero -->
<section class="relative table w-full items-center py-36 bg-top bg-no-repeat bg-cover" style="background-image: url('{{ site_asset('images/bg/cta.jpg') }}');">
    <div class="absolute inset-0 bg-gradient-to-b from-slate-900/60 via-slate-900/80 to-slate-900"></div>
    <div class="container relative">
        <div class="grid grid-cols-1 pb-8 text-center mt-10">
            <h3 class="text-4xl leading-normal tracking-wider font-semibold text-white">Site Travel Agency</h3>
        </div><!--end grid-->
    </div><!--end container-->
    
    <div class="absolute text-center z-10 bottom-5 start-0 end-0 mx-3">
        <ul class="tracking-[0.5px] mb-0 inline-block">
            <li class="inline-block uppercase text-[13px] font-bold duration-500 ease-in-out text-white/50 hover:text-white"><a href="{{ url('/') }}">Site</a></li>
            <li class="inline-block text-base text-white/50 mx-0.5 ltr:rotate-0 rtl:rotate-180"><i class="mdi mdi-chevron-right"></i></li>
            <li class="inline-block uppercase text-[13px] font-bold duration-500 ease-in-out text-white" aria-current="page">About Us</li>
        </ul>
    </div>
</section><!--end section-->
<!-- End Hero -->

<!-- Start -->
<section class="relative md:py-24 py-16">
    <div class="container relative">
        
        <!-- includes/Hero/about.blade.php -->
        @include('site::includes.Hero.about')

    </div><!--end container-->

    <div class="container relative md:mt-24 mt-16">
        <div class="grid grid-cols-1 pb-6 text-center">
            <h3 class="mb-6 md:text-3xl text-2xl md:leading-normal leading-normal font-semibold">Our Team</h3>

            <p class="text-slate-400 max-w-xl mx-auto">This is just a simple text made for this unique and awesome template, you can replace it with any text.</p>
        </div><!--end grid-->

        <div class="grid md:grid-cols-12 grid-cols-1 mt-8 gap-[30px]">

            <!-- includes/Pages/team.blade.php -->
            @include('site::includes.Pages.team')

        </div><!--end grid-->
    </div><!--end container-->

    <div class="container relative md:mt-24 mt-16">
        <div class="grid grid-cols-1 pb-6 text-center">
            <h3 class="mb-6 md:text-3xl text-2xl md:leading-normal leading-normal font-semibold">What Our Users Say</h3>

            <p class="text-slate-400 max-w-xl mx-auto">This is just a simple text made for this unique and awesome template, you can replace it with any text.</p>
        </div><!--end grid-->

        <div class="grid grid-cols-1 mt-6">
            <div class="tiny-three-item">
                
                <!-- includes/Hero/reviews.blade.php -->
                @include('site::includes.Hero.reviews')

            </div>
        </div><!--end grid-->
    </div><!--end container-->

    <div class="container relative md:mt-24 mt-16">
        <div class="grid grid-cols-1 pb-6 text-center">
            <h3 class="mb-6 md:text-3xl text-2xl md:leading-normal leading-normal font-semibold">Travel Blogs</h3>

            <p class="text-slate-400 max-w-xl mx-auto">This is just a simple text made for this unique and awesome template, you can replace it with any text.</p>
        </div><!--end grid-->

        <div class="grid lg:grid-cols-3 md:grid-cols-2 grid-cols-1 mt-6 gap-6">
            
            <!-- includes/Hero/blog.blade.php -->
            @include('site::includes.Hero.blog')

        </div><!--end grid-->
    </div><!--end container-->
</section><!--end section-->
<!-- End -->

<!-- Insta Post Start -->
<div class="container-fluid relative">
    <div class="grid grid-cols-1 relative">
        <div class="tiny-twelve-item">
            
            <!-- includes/Hero/insta-post.blade.php -->
            @include('site::includes.Hero.insta-post')

        </div>

        <div class="absolute top-2/4 -translate-y-2/4 start-2/4 ltr:-translate-x-2/4 rtl:translate-x-2/4 text-center">
            <a href="https://www.instagram.com/shreethemes/" target="_blank" class="size-8 inline-flex items-center justify-center tracking-wide align-middle duration-500 text-base text-center rounded-md border border-red-500 bg-red-500 text-white"><i data-feather="instagram" class="size-4"></i></a>
        </div>
    </div><!--end grid-->
</div><!--end container-->
<!-- Insta Post End -->

@endsection