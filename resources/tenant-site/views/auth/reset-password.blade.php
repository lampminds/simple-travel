@extends('site::layouts.auth')

@section('page_title', __('tenant.auth_reset_title'))

@section('auth_heading', __('tenant.auth_reset_title'))

@section('auth_body')
    <p class="text-slate-400 mb-6">{{ __('tenant.auth_reset_intro') }}</p>

    <form method="POST" action="{{ route('password.update') }}" class="text-start">
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">
        <div class="grid grid-cols-1">
            <div class="mb-4">
                <label class="font-semibold" for="email">{{ __('auth.login.email') }}:</label>
                <input id="email" name="email" type="email" value="{{ old('email', $request->email) }}" required autofocus autocomplete="username" class="mt-3 w-full py-2 px-3 h-10 bg-transparent dark:bg-slate-900 dark:text-slate-200 rounded outline-none border @error('email') border-red-500 @else border-gray-100 dark:border-gray-800 @enderror focus:ring-0" placeholder="{{ __('auth.login.placeholder_email') }}">
                @error('email')
                    <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="font-semibold" for="password">{{ __('auth.login.password') }}:</label>
                <input id="password" name="password" type="password" required autocomplete="new-password" class="mt-3 w-full py-2 px-3 h-10 bg-transparent dark:bg-slate-900 dark:text-slate-200 rounded outline-none border @error('password') border-red-500 @else border-gray-100 dark:border-gray-800 @enderror focus:ring-0" placeholder="{{ __('auth.login.placeholder_password') }}">
                @error('password')
                    <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="font-semibold" for="password_confirmation">{{ __('tenant.auth_password_confirmation') }}:</label>
                <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password" class="mt-3 w-full py-2 px-3 h-10 bg-transparent dark:bg-slate-900 dark:text-slate-200 rounded outline-none border border-gray-100 dark:border-gray-800 focus:ring-0" placeholder="{{ __('tenant.auth_password_confirmation') }}">
            </div>

            <div class="mb-4">
                <button type="submit" class="py-2 px-5 inline-block tracking-wide align-middle duration-500 text-base text-center bg-red-500 text-white rounded-md w-full">{{ __('tenant.auth_reset_submit') }}</button>
            </div>

            <div class="text-center">
                <a href="{{ route('login') }}" class="text-slate-900 dark:text-white font-bold inline-block">{{ __('auth.login.submit') }}</a>
            </div>
        </div>
    </form>
@endsection
