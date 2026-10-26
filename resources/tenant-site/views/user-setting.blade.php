<!-- resources/views/user-setting.blade.php -->
@extends('site::layouts.main')

@section('title', 'User-Setting Page')

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
                <div class="p-6 rounded-md shadow-sm dark:shadow-gray-800 bg-white dark:bg-slate-900">
                    <h5 class="text-lg font-semibold mb-4">Personal Detail :</h5>
                    <form>
                        <div class="grid lg:grid-cols-2 grid-cols-1 gap-5">
                            <div>
                                <label class="form-label font-medium">First Name : <span class="text-red-600">*</span></label>
                                <div class="form-icon relative mt-2">
                                    <i data-feather="user" class="w-4 h-4 absolute top-3 start-4"></i>
                                    <input type="text" class="ps-12 w-full py-2 px-3 h-10 bg-transparent dark:bg-slate-900 dark:text-slate-200 rounded outline-none border border-gray-100 dark:border-gray-800 focus:ring-0" placeholder="First Name:" id="firstname" name="name" required="">
                                </div>
                            </div>
                            <div>
                                <label class="form-label font-medium">Last Name : <span class="text-red-600">*</span></label>
                                <div class="form-icon relative mt-2">
                                    <i data-feather="user-check" class="w-4 h-4 absolute top-3 start-4"></i>
                                    <input type="text" class="ps-12 w-full py-2 px-3 h-10 bg-transparent dark:bg-slate-900 dark:text-slate-200 rounded outline-none border border-gray-100 dark:border-gray-800 focus:ring-0" placeholder="Last Name:" id="lastname" name="name" required="">
                                </div>
                            </div>
                            <div>
                                <label class="form-label font-medium">Your Email : <span class="text-red-600">*</span></label>
                                <div class="form-icon relative mt-2">
                                    <i data-feather="mail" class="w-4 h-4 absolute top-3 start-4"></i>
                                    <input type="email" class="ps-12 w-full py-2 px-3 h-10 bg-transparent dark:bg-slate-900 dark:text-slate-200 rounded outline-none border border-gray-100 dark:border-gray-800 focus:ring-0" placeholder="Email" name="email" required="">
                                </div>
                            </div>
                            <div>
                                <label class="form-label font-medium">Occupation : </label>
                                <div class="form-icon relative mt-2">
                                    <i data-feather="bookmark" class="w-4 h-4 absolute top-3 start-4"></i>
                                    <input name="name" id="occupation" type="text" class="ps-12 w-full py-2 px-3 h-10 bg-transparent dark:bg-slate-900 dark:text-slate-200 rounded outline-none border border-gray-100 dark:border-gray-800 focus:ring-0" placeholder="Occupation :">
                                </div>
                            </div>
                        </div><!--end grid-->

                        <div class="grid grid-cols-1">
                            <div class="mt-5">
                                <label class="form-label font-medium">Description : </label>
                                <div class="form-icon relative mt-2">
                                    <i data-feather="message-circle" class="w-4 h-4 absolute top-3 start-4"></i>
                                    <textarea name="comments" id="comments" class="ps-11 w-full py-2 px-3 h-28 bg-transparent dark:bg-slate-900 dark:text-slate-200 rounded outline-none border border-gray-100 dark:border-gray-800 focus:ring-0" placeholder="Message :"></textarea>
                                </div>
                            </div>
                        </div><!--end row-->

                        <input type="submit" id="submit" name="send" class="py-2 px-5 inline-block font-semibold tracking-wide align-middle duration-500 text-base text-center bg-red-500 text-white rounded-md mt-5" value="Save Changes">
                    </form><!--end form-->
                </div>

                <div class="p-6 rounded-md shadow-sm dark:shadow-gray-800 bg-white dark:bg-slate-900 mt-6">
                    <div class="grid lg:grid-cols-2 grid-cols-1 gap-5">
                        <div>
                            <h5 class="text-lg font-semibold mb-4">Contact Info :</h5>

                            <form>
                                <div class="grid grid-cols-1 gap-5">
                                    <div>
                                        <label class="form-label font-medium">Phone No. :</label>
                                        <div class="form-icon relative mt-2">
                                            <i data-feather="phone" class="w-4 h-4 absolute top-3 start-4"></i>
                                            <input name="number" id="number" type="number" class="ps-12 w-full py-2 px-3 h-10 bg-transparent dark:bg-slate-900 dark:text-slate-200 rounded outline-none border border-gray-100 dark:border-gray-800 focus:ring-0" placeholder="Phone :">
                                        </div>
                                    </div>

                                    <div>
                                        <label class="form-label font-medium">Website :</label>
                                        <div class="form-icon relative mt-2">
                                            <i data-feather="globe" class="w-4 h-4 absolute top-3 start-4"></i>
                                            <input name="url" id="url" type="url" class="ps-12 w-full py-2 px-3 h-10 bg-transparent dark:bg-slate-900 dark:text-slate-200 rounded outline-none border border-gray-100 dark:border-gray-800 focus:ring-0" placeholder="Url :">
                                        </div>
                                    </div>
                                </div><!--end grid-->

                                <button class="py-2 px-5 inline-block font-semibold tracking-wide align-middle duration-500 text-base text-center bg-red-500 text-white rounded-md mt-5">Add</button>
                            </form>
                        </div><!--end col-->
                        
                        <div> 
                            <h5 class="text-lg font-semibold mb-4">Change password :</h5>
                            <form>
                                <div class="grid grid-cols-1 gap-5">
                                    <div>
                                        <label class="form-label font-medium">Old password :</label>
                                        <div class="form-icon relative mt-2">
                                            <i data-feather="key" class="w-4 h-4 absolute top-3 start-4"></i>
                                            <input type="password" class="ps-12 w-full py-2 px-3 h-10 bg-transparent dark:bg-slate-900 dark:text-slate-200 rounded outline-none border border-gray-100 dark:border-gray-800 focus:ring-0" placeholder="Old password" required="">
                                        </div>
                                    </div>

                                    <div>
                                        <label class="form-label font-medium">New password :</label>
                                        <div class="form-icon relative mt-2">
                                            <i data-feather="key" class="w-4 h-4 absolute top-3 start-4"></i>
                                            <input type="password" class="ps-12 w-full py-2 px-3 h-10 bg-transparent dark:bg-slate-900 dark:text-slate-200 rounded outline-none border border-gray-100 dark:border-gray-800 focus:ring-0" placeholder="New password" required="">
                                        </div>
                                    </div>

                                    <div>
                                        <label class="form-label font-medium">Re-type New password :</label>
                                        <div class="form-icon relative mt-2">
                                            <i data-feather="key" class="w-4 h-4 absolute top-3 start-4"></i>
                                            <input type="password" class="ps-12 w-full py-2 px-3 h-10 bg-transparent dark:bg-slate-900 dark:text-slate-200 rounded outline-none border border-gray-100 dark:border-gray-800 focus:ring-0" placeholder="Re-type New password" required="">
                                        </div>
                                    </div>
                                </div><!--end grid-->

                                <button class="py-2 px-5 inline-block font-semibold tracking-wide align-middle duration-500 text-base text-center bg-red-500 text-white rounded-md mt-5">Save password</button>
                            </form>
                        </div><!--end col-->

                    </div><!--end row-->
                </div>

                <div class="p-6 rounded-md shadow-sm dark:shadow-gray-800 bg-white dark:bg-slate-900 mt-6">
                    <h5 class="text-lg font-semibold mb-5 text-red-600">Delete Account :</h5>

                    <p class="text-slate-400 mb-4">Do you want to delete the account? Please press below "Delete" button</p>

                    <a href="" class="py-2 px-5 inline-block font-semibold tracking-wide align-middle duration-500 text-base text-center bg-red-600 text-white rounded-md">Delete</a>
                </div>
            </div>
        </div><!--end grid-->
    </div><!--end container-->
</section><!--end section-->
<!-- End Hero -->

@endsection