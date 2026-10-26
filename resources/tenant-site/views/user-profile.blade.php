<!-- resources/views/user-profile.blade.php -->
@extends('site::layouts.main')

@section('title', 'User-Profile Page')

@section('content')

@include('site::includes.navbar3')

<!-- Start Hero -->
<section class="relative lg:pb-24 pb-16 md:mt-[84px] mt-[70px]">
    <div class="container md:!px-3 !px-0 relative">
        <div class="relative overflow-hidden md:rounded-md shadow-sm dark:shadow-gray-800 h-52 bg-center bg-no-repeat bg-cover" style="background-image: url('{{ site_asset('images/bg/cta.jpg') }}');"></div>
    </div><!--end container-->

    <div class="container relative md:mt-24 mt-16">
        <div class="md:flex">
            <div class="lg:w-1/4 md:w-1/3 md:px-3">
                <div class="relative md:-mt-48 -mt-32">
                    <div class="p-6 rounded-md shadow-sm dark:shadow-gray-800 bg-white dark:bg-slate-900">
                        <div class="profile-pic text-center mb-5">
                            <input id="pro-img" name="profile-image" type="file" class="hidden" onchange="loadFile(event)" />
                            <div>
                                <div class="relative h-28 w-28 mx-auto">
                                    <img src="{{ site_asset('images/client/16.jpg') }}" class="rounded-full shadow-sm dark:shadow-gray-800 ring-4 ring-slate-50 dark:ring-slate-800" id="profile-image" alt="">
                                    <label class="absolute inset-0 cursor-pointer" for="pro-img"></label>
                                </div>

                                <div class="mt-4">
                                    <h5 class="text-lg font-semibold">Jesus Zamora</h5>
                                    <p class="text-slate-400">jesus@hotmail.com</p>
                                </div>
                            </div>
                        </div>

                        <div class="border-t border-gray-100 dark:border-gray-700">
                            <ul class="list-none sidebar-nav mb-0 pb-0" id="navmenu-nav">
                                
                                <!-- includes/Pages/nav.blade.php -->
                                @include('site::includes.Pages.nav')

                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="lg:w-3/4 md:w-2/3 md:px-3 mt-6 md:mt-0">
                <div>
                    <h5 class="text-lg font-semibold mb-6">Introduction</h5>
                
                    <p class="text-slate-400 mt-4">Nam libero tempore, cum soluta nobis est eligendi optio cumque nihil impedit quo minus id quod maxime placeat facere possimus, omnis voluptas assumenda est, omnis dolor repellendus how to pursue pleasure rationally encounter but because those who do not know how to pursue consequences that are extremely</p>
                    <p class="text-slate-400 mt-2">Lorem ipsum dolor sit amet consectetur adipisicing elit. Quidem earum dicta saepe fugit, qui commodi possimus vero pariatur asperiores. Sed impedit reprehenderit deserunt culpa incidunt iste laborum placeat molestiae illo?</p>
                </div>

                <div class="mt-6">
                    <h5 class="text-lg font-semibold mb-6">My Tours</h5>

                    <div class="grid lg:grid-cols-3 md:grid-cols-2 grid-cols-1 mt-6 gap-6">

                        <!-- includes/Pages/tours.blade.php -->
                        @include('site::includes.Pages.tours')

                    </div><!--end grid-->
                </div>
            </div>
        </div><!--end grid-->
    </div><!--end container-->
</section><!--end section-->
<!-- End Hero -->

@endsection