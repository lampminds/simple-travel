{{--
    Robot illustration error block.
    Expects: $errorKey (e.g. '403'), $image (public path under images/error/, e.g. '403-access denied.png')
--}}
@php
    $prefix = 'errors.'.$errorKey;
    $titleText = $title ?? __($prefix.'.title');
    $descriptionText = $description ?? __($prefix.'.description');
@endphp
<main class="err-robot">
    <div class="err-robot__stage">
        <p class="err-robot__code">{{ __($prefix.'.code') }}</p>
        <h1 class="err-robot__title">{{ $titleText }}</h1>
        <p class="err-robot__desc">{{ $descriptionText }}</p>
    </div>
    <div class="err-robot__visual">
        <img
            src="{{ asset('images/error/'.$image) }}"
            width="800"
            height="600"
            alt=""
            decoding="async"
        >
        <div class="err-robot__cta-wrap">
            <a class="err-robot__cta" href="{{ url('/') }}">{{ __($prefix.'.home') }}</a>
        </div>
    </div>
</main>
