<!-- resources/views/list.blade.php -->
@extends('site::layouts.main')

@section('title', 'List Page')

@section('content')

@include('site::includes.navbar4')
        
<!-- Start Hero -->
<section class="relative table w-full items-center py-36 bg-top bg-no-repeat bg-cover" style="background-image: url('{{ site_asset('images/bg/cta.jpg') }}');">
    <div class="absolute inset-0 bg-gradient-to-b from-slate-900/60 via-slate-900/80 to-slate-900"></div>
    <div class="container relative">
        <div class="grid grid-cols-1 pb-8 text-center mt-10">
            <h3 class="text-4xl leading-normal tracking-wider font-semibold text-white">Tour Packages</h3>
        </div><!--end grid-->
    </div><!--end container-->
    
    <div class="absolute text-center z-10 bottom-5 start-0 end-0 mx-3">
        <ul class="tracking-[0.5px] mb-0 inline-block">
            <li class="inline-block uppercase text-[13px] font-bold duration-500 ease-in-out text-white/50 hover:text-white"><a href="{{ url('/') }}">Site</a></li>
            <li class="inline-block text-base text-white/50 mx-0.5 ltr:rotate-0 rtl:rotate-180"><i class="mdi mdi-chevron-right"></i></li>
            <li class="inline-block uppercase text-[13px] font-bold duration-500 ease-in-out text-white" aria-current="page">Tour</li>
        </ul>
    </div>
</section><!--end section-->
<!-- End Hero -->

<!-- Start -->
<section class="relative md:py-24 py-16">
    <div class="container relative">
        <div class="grid lg:grid-cols-2 grid-cols-1 gap-6">                    

            <!-- includes/Listing/packages2.blade.php -->
            @include('site::includes.Listing.packages2')

        </div><!--end grid-->

        <div class="grid md:grid-cols-12 grid-cols-1 mt-6">
            <div class="md:col-span-12 text-center">
                
                <!-- includes/Listing/navigation.blade.php -->
                @include('site::includes.Listing.navigation')

            </div><!--end col-->
        </div><!--end grid-->
    </div><!--end container-->
</section><!--end section-->
<!-- End -->

@endsection