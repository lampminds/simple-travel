@props([
    'latitude' => null,
    'longitude' => null,
])

@php
    use App\Support\CoordinateMapUrls;

    $pair = CoordinateMapUrls::pair(
        $latitude !== null && $latitude !== '' ? (string) $latitude : null,
        $longitude !== null && $longitude !== '' ? (string) $longitude : null,
    );
@endphp

@if ($pair !== null)
    <div {{ $attributes->merge(['class' => 'small mt-1']) }}>
        <span class="text-muted">{{ __('wizard.step7_coordinates_verify') }}</span>
        <a
            href="{{ CoordinateMapUrls::openStreetMapUrl($pair['lat'], $pair['lon']) }}"
            class="link-primary link-offset-2 link-underline-opacity-25 link-underline-opacity-100-hover"
            target="_blank"
            rel="noopener noreferrer"
        >{{ __('wizard.step7_coordinates_open_osm') }}</a>
        <span class="text-muted mx-1" aria-hidden="true">·</span>
        <a
            href="{{ CoordinateMapUrls::googleMapsUrl($pair['lat'], $pair['lon']) }}"
            class="link-primary link-offset-2 link-underline-opacity-25 link-underline-opacity-100-hover"
            target="_blank"
            rel="noopener noreferrer"
        >{{ __('wizard.step7_coordinates_open_google') }}</a>
        <span class="d-block text-muted mt-1">{{ __('wizard.step7_coordinates_paste_hint') }}</span>
    </div>
@endif
