@extends('layouts.base', ['title' => $title ?? $account->nick])

@section('content')
    <div class="container py-5">
        <h1 class="h3 mb-2">{{ $title ?? $account->nick }}</h1>
        @if (filled($subtitle ?? null))
            <p class="text-muted mb-3">{{ $subtitle }}</p>
        @endif
        @if (($productCategories ?? collect())->isNotEmpty())
            <p class="small text-uppercase text-muted mb-2">{{ __('tenant.product_categories_heading') }}</p>
            <ul class="list-inline mb-3">
                @foreach ($productCategories as $category)
                    <li class="list-inline-item badge bg-light text-dark">{{ $category->name !== '' ? $category->name : $category->code }}</li>
                @endforeach
            </ul>
        @endif
        @if (filled($about ?? null))
            <h2 class="h5 mt-4">{{ __('tenant.about_heading') }}</h2>
            <div class="mb-0">{!! \App\Models\AccountPublicProfile::renderAbout($about) !!}</div>
        @endif
        @if (! empty($videoEmbed['url']))
            <p class="mt-3 mb-0">
                <a href="{{ $videoEmbed['url'] }}" target="_blank" rel="noopener noreferrer">{{ $videoEmbed['url'] }}</a>
            </p>
        @endif
    </div>
@endsection
