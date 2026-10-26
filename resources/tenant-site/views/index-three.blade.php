<!-- resources/views/index-three.blade.php -->
@extends('site::layouts.main')

@section('title', 'Index-Three Page')

@section('content')

@include('site::includes.navbar')
        
<!-- Hero Start -->
<section class="relative md:pt-48 md:pb-36 py-36 table w-full items-center bg-center" id="home">
    <div class="absolute inset-0 bg-slate-900/40"></div>
    <div class="container relative">
        <div class="grid grid-cols-1">
            <div class="text-center">
                <h1 class="font-bold text-white lg:leading-normal leading-normal text-4xl lg:text-6xl mb-6 mt-5">Let the journey begin...</h1>
                <p class="text-white/70 text-xl max-w-xl mx-auto">Planning for a trip? We will organize your trip with the best places and within best budget!</p>
                
                <div class="mt-6">
                    <form class="p-4 bg-white dark:bg-slate-900 rounded-xl shadow-sm dark:shadow-gray-700">
                        <div class="registration-form text-slate-900 text-start">
                            <div class="grid lg:grid-cols-5 md:grid-cols-2 grid-cols-1 gap-4">
                                <div>
                                    <label class="form-label font-medium text-slate-900 dark:text-white">Search:</label>
                                    <div class="relative mt-2">
                                        <i data-feather="search" class="size-[18px] absolute top-[10px] start-3 text-slate-400"></i>
                                        <input name="name" type="text" id="job-keyword" class="w-full py-2 px-3 ps-10 h-10 bg-transparent dark:bg-slate-900 dark:text-slate-200 rounded-md outline-none border border-gray-100 dark:border-gray-800 focus:ring-0" placeholder="Search">
                                    </div>
                                </div>
    
                                <div>
                                    <label class="form-label font-medium text-slate-900 dark:text-white">Select Your Date:</label>
                                    <div class="relative mt-2">
                                        <i data-feather="calendar" class="size-[18px] absolute top-[10px] start-3 text-slate-400"></i>
                                        <input name="name" type="text" id="job-keyword" class="w-full py-2 px-3 ps-10 h-10 bg-transparent dark:bg-slate-900 dark:text-slate-200 rounded-md outline-none border border-gray-100 dark:border-gray-800 focus:ring-0 start" placeholder="Select Your Date">
                                    </div>
                                </div>
    
                                <div>
                                    <label class="form-label font-medium text-slate-900 dark:text-white">Select Your Date:</label>
                                    <div class="relative mt-2">
                                        <i data-feather="calendar" class="size-[18px] absolute top-[10px] start-3 text-slate-400"></i>
                                        <input name="name" type="text" id="job-keyword" class="w-full py-2 px-3 ps-10 h-10 bg-transparent dark:bg-slate-900 dark:text-slate-200 rounded-md outline-none border border-gray-100 dark:border-gray-800 focus:ring-0 end" placeholder="Select Your Date">
                                    </div>
                                </div>
    
                                <div>
                                    <label class="form-label font-medium text-slate-900 dark:text-white">No. of person:</label>
                                    <div class="relative mt-2">
                                        <i data-feather="users" class="size-[18px] absolute top-[10px] start-3 text-slate-400"></i>
                                        <select class="form-select w-full py-2 px-3 ps-10 h-10 bg-transparent dark:bg-slate-900 dark:text-slate-200 rounded-md outline-none border border-gray-100 dark:border-gray-800 focus:ring-0">
                                            <option disabled selected>No. of person</option>
                                            <option>1</option>
                                            <option>2</option>
                                            <option>3</option>
                                            <option>4</option>
                                            <option>5</option>
                                        </select>
                                    </div>
                                </div>
    
                                <div class="lg:mt-[35px]">
                                    <input type="submit" id="search-buy" name="search" class="py-1 px-5 h-10 inline-block tracking-wide align-middle duration-500 text-base text-center bg-red-500 text-white rounded-md w-full cursor-pointer" value="Search">
                                </div>
                            </div><!--end grid-->
                        </div><!--end container-->
                    </form><!--end form-->
                </div>
            </div>
        </div><!--end grid-->
    </div><!--end container-->
</section><!--end section-->
<!-- Hero End -->

<!-- Start -->
<section class="relative md:py-24 py-16 overflow-hidden">
    <div class="container relative">
        <div class="grid grid-cols-1 pb-8 text-center">
            <h3 class="mb-6 md:text-3xl text-2xl md:leading-normal leading-normal font-semibold">Top Destinations</h3>

            <p class="text-slate-400 max-w-xl mx-auto">Planning for a trip? We will organize your trip with the best places and within best budget!</p>
        </div><!--end grid-->

        <div class="grid lg:grid-cols-4 md:grid-cols-3 sm:grid-cols-2 grid-cols-1 relative mt-6 gap-6">

            <!-- includes/Hero/top-destinations2.blade.php -->
            @include('site::includes.Hero.top-destinations2')

        </div><!--end grid-->
    </div><!--end container-->

    <div class="container relative md:mt-24 mt-16">
        
        <!-- includes/Hero/about.blade.php -->
        @include('site::includes.Hero.about')

    </div><!--end container-->

    <div class="container relative md:mt-24 mt-16">
        <div class="grid grid-cols-1 pb-8 text-center">
            <h3 class="mb-6 md:text-3xl text-2xl md:leading-normal leading-normal font-semibold">Tours Packages</h3>

            <p class="text-slate-400 max-w-xl mx-auto">Planning for a trip? We will organize your trip with the best places and within best budget!</p>
        </div><!--end grid-->

        <div class="grid lg:grid-cols-4 md:grid-cols-2 grid-cols-1 mt-6 gap-6">

            <!-- includes/Hero/tours-packages3.blade.php -->
            @include('site::includes.Hero.tours-packages3')

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

@push('scripts')
<script>
    easy_background("#home",
        {
            slide: ["{{ site_asset('images/bg/4.jpg') }}", "{{ site_asset('images/bg/5.jpg') }}", "{{ site_asset('images/bg/6.jpg') }}"],
            delay: [4000, 4000, 4000],
            
        }
    );
</script>
@endpush