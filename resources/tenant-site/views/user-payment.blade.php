<!-- resources/views/user-payment.blade.php -->
@extends('site::layouts.main')

@section('title', 'User-Payment Page')

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
                <div class="rounded-md shadow-sm dark:shadow-gray-800 bg-white dark:bg-slate-900">
                    <div class="p-6 border-b border-gray-100 dark:border-gray-700">
                        <h5 class="text-xl font-semibold">Payment Methods</h5>
                        <p class="text-slate-400 mt-2">Primary payment method is used by default</p>
                    </div>

                    <div class="px-6">
                        <ul>
                            <li class="flex justify-between items-center py-6">
                                <div class="flex items-center">
                                    <img src="{{ site_asset('images/payments/visa.jpg') }}" class="rounded shadow-sm dark:shadow-gray-800 w-12" alt="">

                                    <div class="ms-3">
                                        <p class="font-semibold">Visa ending in 4578</p>
                                        <p class="text-slate-400 text-sm">Expires in 12/2025</p>
                                    </div>
                                </div>

                                <div>
                                    <a href="" class="size-8 inline-flex items-center justify-center tracking-wide align-middle duration-500 text-base text-center bg-red-600/5 hover:bg-red-600 text-red-600 hover:text-white rounded-full"><i data-feather="trash-2" class="size-4"></i></a>
                                </div>
                            </li>

                            <li class="flex justify-between items-center py-6 border-t border-gray-100 dark:border-gray-700">
                                <div class="flex items-center">
                                    <img src="{{ site_asset('images/payments/american-express.jpg') }}" class="rounded shadow-sm dark:shadow-gray-800 w-12" alt="">

                                    <div class="ms-3">
                                        <p class="font-semibold">American Express ending in 4578</p>
                                        <p class="text-slate-400 text-sm">Expires in 12/2025</p>
                                    </div>
                                </div>

                                <div>
                                    <a href="" class="size-8 inline-flex items-center justify-center tracking-wide align-middle duration-500 text-base text-center bg-red-600/5 hover:bg-red-600 text-red-600 hover:text-white rounded-full"><i data-feather="trash-2" class="size-4"></i></a>
                                </div>
                            </li>

                            <li class="flex justify-between items-center py-6 border-t border-gray-100 dark:border-gray-700">
                                <div class="flex items-center">
                                    <img src="{{ site_asset('images/payments/discover.jpg') }}" class="rounded shadow-sm dark:shadow-gray-800 w-12" alt="">

                                    <div class="ms-3">
                                        <p class="font-semibold">Discover ending in 4578</p>
                                        <p class="text-slate-400 text-sm">Expires in 12/2025</p>
                                    </div>
                                </div>

                                <div>
                                    <a href="" class="size-8 inline-flex items-center justify-center tracking-wide align-middle duration-500 text-base text-center bg-red-600/5 hover:bg-red-600 text-red-600 hover:text-white rounded-full"><i data-feather="trash-2" class="size-4"></i></a>
                                </div>
                            </li>

                            <li class="flex justify-between items-center py-6 border-t border-gray-100 dark:border-gray-700">
                                <div class="flex items-center">
                                    <img src="{{ site_asset('images/payments/mastercard.jpg') }}" class="rounded shadow-sm dark:shadow-gray-800 w-12" alt="">

                                    <div class="ms-3">
                                        <p class="font-semibold">Master Card ending in 4578</p>
                                        <p class="text-slate-400 text-sm">Expires in 12/2025</p>
                                    </div>
                                </div>

                                <div>
                                    <a href="" class="size-8 inline-flex items-center justify-center tracking-wide align-middle duration-500 text-base text-center bg-red-600/5 hover:bg-red-600 text-red-600 hover:text-white rounded-full"><i data-feather="trash-2" class="size-4"></i></a>
                                </div>
                            </li>

                            <li class="py-6 border-t border-gray-100 dark:border-gray-700">
                                <a href="javascript:void(0)" onclick="paymentMethod.showModal()" class="py-2 px-5 inline-block font-semibold tracking-wide align-middle duration-500 text-base text-center bg-red-500 text-white rounded-md">Add Payment Method</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div><!--end grid-->
    </div><!--end container-->
</section><!--end section-->
<!-- End Hero -->

<!-- Start Modal -->
<dialog id="paymentMethod" class="shadow-sm dark:shadow-gray-800 bg-slate-900/75 dark:bg-slate-900 text-slate-900 dark:text-white fixed w-full h-screen top-0 left-0 bottom-0 right-0">
    <div class="relative w-full h-auto max-w-md inline-block bg-white dark:bg-slate-900" style="top: 50%; left:50%; transform: translate(-50%, -50%)">
        <div class="flex justify-between items-center px-6 py-4 border-b border-gray-100 dark:border-gray-700">
            <h3 class="font-bold text-lg">Add Payment Method</h3>
            <form method="dialog">
                <button class="size-6 flex justify-center items-center shadow-sm dark:shadow-gray-800 rounded-md btn-ghost"><i data-feather="x" class="size-4"></i></button>
            </form>
        </div>
        <div class="p-6 text-center">
            <form>
                <div class="grid md:grid-cols-12 grid-cols-1 gap-4">
                    <div class="md:col-span-12">
                        <div class="text-start">
                            <label for="name" class="font-semibold">Your Name :</label>
                            <input name="name" id="name" type="text" class="mt-2 w-full py-2 px-3 h-10 bg-transparent dark:bg-slate-900 dark:text-slate-200 rounded outline-none border border-gray-100 dark:border-gray-800 focus:ring-0" required placeholder="Name :">
                        </div>
                    </div>

                    <div class="lg:col-span-6">
                        <div class="text-start">
                            <label for="ex_month" class="form-label font-medium">Month :</label>
                            <select id="ex_month" class="form-select form-input mt-2 w-full py-2 px-3 h-10 bg-transparent dark:bg-slate-900 dark:text-slate-200 rounded outline-none border border-gray-100 dark:border-gray-800 focus:ring-0">
                                <option>Jan</option>
                                <option>Feb</option>
                                <option>Mar</option>
                                <option>Apr</option>
                                <option>May</option>
                                <option>June</option>
                                <option>July</option>
                                <option>Aug</option>
                                <option>Sep</option>
                                <option>Oct</option>
                                <option>Nov</option>
                                <option>Dec</option>
                            </select>
                        </div>
                    </div>

                    <div class="lg:col-span-6">
                        <div class="text-start">
                            <label for="ex_year" class="form-label font-medium">Year :</label>
                            <select id="ex_year" class="form-select form-input mt-2 w-full py-2 px-3 h-10 bg-transparent dark:bg-slate-900 dark:text-slate-200 rounded outline-none border border-gray-100 dark:border-gray-800 focus:ring-0">
                                <option>2022</option>
                                <option>2023</option>
                                <option>2024</option>
                                <option>2025</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="lg:col-span-6">
                        <div class="text-start">
                            <label for="name" class="font-semibold">Card no. :</label>
                            <input name="number" id="card_number" type="number" class="mt-2 w-full py-2 px-3 h-10 bg-transparent dark:bg-slate-900 dark:text-slate-200 rounded outline-none border border-gray-100 dark:border-gray-800 focus:ring-0" required placeholder="number :">
                        </div>
                    </div>

                    <div class="lg:col-span-6">
                        <div class="text-start">
                            <label for="name" class="font-semibold">CVV :</label>
                            <input name="number" id="cvv_number" type="number" class="mt-2 w-full py-2 px-3 h-10 bg-transparent dark:bg-slate-900 dark:text-slate-200 rounded outline-none border border-gray-100 dark:border-gray-800 focus:ring-0" required placeholder="number :">
                        </div>
                    </div>

                    <div class="md:col-span-12">
                        <div class="text-start">
                            <label for="card_names" class="form-label font-medium">Cards :</label>
                            <select id="card_names" class="form-select form-input mt-2 w-full py-2 px-3 h-10 bg-transparent dark:bg-slate-900 dark:text-slate-200 rounded outline-none border border-gray-100 dark:border-gray-800 focus:ring-0">
                                <option>Visa</option>
                                <option>Ame. Express</option>
                                <option>Master</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="mt-4">
                    <button type="submit" id="submit" name="send" class="py-2 px-5 font-semibold tracking-wide align-middle duration-500 text-base text-center bg-red-500 text-white rounded-md justify-center flex items-center">Add Card</button>
                </div>
            </form>
        </div>
    </div>
</dialog>
<!-- End Modal -->

@endsection