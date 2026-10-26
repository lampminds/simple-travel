<!-- resources/views/login.blade.php -->
@extends('site::layouts.no-header')

@section('title', 'Login Page')

@section('content')

<section class="md:h-screen py-36 flex items-center relative overflow-hidden zoom-image">
    <div class="absolute inset-0 image-wrap z-1 bg-no-repeat bg-center bg-cover" style="background-image: url('{{ site_asset('images/bg/6.jpg') }}');"></div>
    <div class="absolute inset-0 bg-gradient-to-b from-transparent to-slate-900 z-2" id="particles-snow"></div>
    <div class="container relative z-3">
        <div class="flex justify-center">
            <div class="max-w-[400px] w-full m-auto p-6 bg-white dark:bg-slate-900 shadow-md dark:shadow-gray-700 rounded-md">
                <a href="{{ url('/') }}"><img src="{{ site_asset('images/logo-icon.png') }}" class="mx-auto" alt=""></a>
                <h5 class="my-6 text-xl font-semibold">Login</h5>
                <form class="text-start">
                    <div class="grid grid-cols-1">
                        <div class="mb-4">
                            <label class="font-semibold" for="LoginEmail">Email Address:</label>
                            <input id="LoginEmail" type="email" class="mt-3 w-full py-2 px-3 h-10 bg-transparent dark:bg-slate-900 dark:text-slate-200 rounded outline-none border border-gray-100 dark:border-gray-800 focus:ring-0" placeholder="name@example.com">
                        </div>

                        <div class="mb-4">
                            <label class="font-semibold" for="LoginPassword">Password:</label>
                            <input id="LoginPassword" type="password" class="mt-3 w-full py-2 px-3 h-10 bg-transparent dark:bg-slate-900 dark:text-slate-200 rounded outline-none border border-gray-100 dark:border-gray-800 focus:ring-0" placeholder="Password:">
                        </div>

                        <div class="flex justify-between mb-4">
                            <div class="flex items-center mb-0">
                                <input class="form-checkbox size-4 appearance-none rounded border border-gray-200 dark:border-gray-800 accent-red-600 checked:appearance-auto dark:accent-red-600 focus:border-red-300 focus:ring-0 focus:ring-offset-0 focus:ring-red-200 focus:ring-opacity-50 me-2" type="checkbox" value="" id="RememberMe">
                                <label class="form-checkbox-label text-slate-400" for="RememberMe">Remember me</label>
                            </div>
                            <p class="text-slate-400 mb-0"><a href="{{ url('/forgot-password') }}" class="text-slate-400">Forgot password ?</a></p>
                        </div>

                        <div class="mb-4">
                            <input type="submit" class="py-2 px-5 inline-block tracking-wide align-middle duration-500 text-base text-center bg-red-500 text-white rounded-md w-full" value="Login / Sign in">
                        </div>

                        <div class="text-center">
                            <span class="text-slate-400 me-2">Don't have an account ?</span> <a href="{{ url('/signup') }}" class="text-slate-900 dark:text-white font-bold inline-block">Sign Up</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section><!--end section -->

@endsection