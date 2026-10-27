@extends('site::layouts.auth')

@section('page_title', __('auth.login.title'))

@section('auth_heading', __('auth.login.heading'))

@section('auth_body')
    @if (session('status'))
        <p class="text-slate-400 mb-4">{{ session('status') }}</p>
    @endif
    @if (session('error'))
        <p class="text-red-500 mb-4">{{ session('error') }}</p>
    @endif

    <form method="POST" action="{{ route('login') }}" class="text-start">
        @csrf
        @if (filled(request('invitation_token')))
            <input type="hidden" name="invitation_token" value="{{ request('invitation_token') }}">
        @endif
        <div class="grid grid-cols-1">
            <div class="mb-4">
                <label class="font-semibold" for="email">{{ __('auth.login.email') }}:</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="username" class="mt-3 w-full py-2 px-3 h-10 bg-transparent dark:bg-slate-900 dark:text-slate-200 rounded outline-none border @error('email') border-red-500 @else border-gray-100 dark:border-gray-800 @enderror focus:ring-0" placeholder="{{ __('auth.login.placeholder_email') }}">
                @error('email')
                    <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="font-semibold" for="password">{{ __('auth.login.password') }}:</label>
                <input id="password" name="password" type="password" required autocomplete="current-password" class="mt-3 w-full py-2 px-3 h-10 bg-transparent dark:bg-slate-900 dark:text-slate-200 rounded outline-none border @error('password') border-red-500 @else border-gray-100 dark:border-gray-800 @enderror focus:ring-0" placeholder="{{ __('auth.login.placeholder_password') }}">
                @error('password')
                    <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-between mb-4">
                <div class="flex items-center mb-0">
                    <input class="form-checkbox size-4 appearance-none rounded border border-gray-200 dark:border-gray-800 accent-red-600 checked:appearance-auto dark:accent-red-600 focus:border-red-300 focus:ring-0 focus:ring-offset-0 focus:ring-red-200 focus:ring-opacity-50 me-2" type="checkbox" name="remember" id="remember" @checked(old('remember'))>
                    <label class="form-checkbox-label text-slate-400" for="remember">{{ __('auth.login.remember_me') }}</label>
                </div>
                <p class="text-slate-400 mb-0"><a href="{{ route('password.request') }}" class="text-slate-400">{{ __('auth.login.forgot_password') }}</a></p>
            </div>

            <div class="mb-4">
                <button type="submit" class="py-2 px-5 inline-block tracking-wide align-middle duration-500 text-base text-center bg-red-500 text-white rounded-md w-full">{{ __('auth.login.submit') }}</button>
            </div>
        </div>
    </form>
@endsection
