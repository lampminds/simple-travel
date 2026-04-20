<x-mail::layout>
{{-- Header --}}
<x-slot:header>
<x-mail::header :url="config('app.url')">
@php
    $logoPath = public_path('images/logo.png');
    $logoSrc = file_exists($logoPath)
        ? 'data:image/png;base64,' . base64_encode((string) file_get_contents($logoPath))
        : url('/images/logo.png');
@endphp
<img src="{{ $logoSrc }}" alt="{{ config('app.name') }}" style="height: 44px; width: auto; max-width: 100%;">
</x-mail::header>
</x-slot:header>

{{-- Body --}}
{!! $slot !!}

{{-- Subcopy --}}
@isset($subcopy)
<x-slot:subcopy>
<x-mail::subcopy>
{!! $subcopy !!}
</x-mail::subcopy>
</x-slot:subcopy>
@endisset

{{-- Footer --}}
<x-slot:footer>
<x-mail::footer>
© {{ date('Y') }} {{ config('app.name') }}. {{ __('invitations.mail.rights_reserved') }}
</x-mail::footer>
</x-slot:footer>
</x-mail::layout>
