<?php

namespace App\Services\Geocoding;

use App\Models\LmpCity;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Forward geocoding via OpenStreetMap Nominatim (free, usage policy applies).
 *
 * Uses structured parameters (street, city, state, country) plus optional countrycodes
 * and viewbox boost — avoids fragile free-form strings such as "City — State, Country".
 *
 * @see https://operations.osmfoundation.org/policies/nominatim/
 * @see https://nominatim.org/release-docs/latest/api/Search/
 */
class NominatimGeocoder
{
    public function __construct(
        protected ?ServiceLocationAddressNormalizer $addressNormalizer = null,
    ) {
        $this->addressNormalizer ??= new ServiceLocationAddressNormalizer;
    }

    /**
     * @return array{lat: float, lon: float}|null
     */
    public function firstResultForServiceLocation(string $streetAddress, LmpCity $city): ?array
    {
        if (! config('services.nominatim.enabled')) {
            return null;
        }

        $userAgent = (string) config('services.nominatim.user_agent');
        if ($userAgent === '') {
            return null;
        }

        $streetAddress = trim($streetAddress);
        if ($streetAddress === '') {
            return null;
        }

        $city->loadMissing(['state.country']);

        $state = $city->state;
        $country = $state?->country;
        $countryName = $country !== null ? trim((string) $country->name) : '';
        $stateName = $state !== null ? trim((string) $state->name) : '';
        $cityName = trim((string) $city->name);

        $countryCode = null;
        if ($country !== null && $country->iso_2 !== null && $country->iso_2 !== '') {
            $countryCode = strtolower((string) $country->iso_2);
        }

        $baseQuery = [
            'format' => 'json',
            'limit' => 5,
            'addressdetails' => 0,
        ];

        if ($email = config('services.nominatim.email')) {
            $baseQuery['email'] = (string) $email;
        }

        if ($countryCode !== null) {
            $baseQuery['countrycodes'] = $countryCode;
        }

        $viewbox = $this->viewboxAroundCity($city);
        if ($viewbox !== null) {
            $baseQuery['viewbox'] = $viewbox;
        }

        $headers = [
            'User-Agent' => $userAgent,
            'Accept' => 'application/json',
            'Accept-Language' => str_replace('_', '-', app()->getLocale()).',en;q=0.8',
        ];

        $streetVariants = $this->addressNormalizer->searchVariants($streetAddress, $city);
        if ($streetVariants === []) {
            $streetVariants = [$streetAddress];
        }

        foreach ($streetVariants as $streetLine) {
            $parsed = $this->searchForStreetLine($streetLine, $cityName, $stateName, $countryName, $baseQuery, $headers);
            if ($parsed !== null) {
                return $parsed;
            }
        }

        $postalCode = $this->addressNormalizer->extractBrazilianPostalCode($streetAddress);
        if ($postalCode !== null && $countryName !== '') {
            $qPostal = implode(', ', array_filter([$postalCode, $cityName, $countryName], fn (string $s): bool => $s !== ''));
            $parsed = $this->executeSearch(array_merge($baseQuery, ['q' => $qPostal]), $headers);
            if ($parsed !== null) {
                return $parsed;
            }
        }

        return null;
    }

    /**
     * @param  array<string, mixed>  $baseQuery
     * @param  array<string, string>  $headers
     * @return array{lat: float, lon: float}|null
     */
    protected function searchForStreetLine(
        string $streetLine,
        string $cityName,
        string $stateName,
        string $countryName,
        array $baseQuery,
        array $headers,
    ): ?array {
        $structured = array_merge($baseQuery, [
            'street' => $streetLine,
            'city' => $cityName,
        ]);
        if ($stateName !== '') {
            $structured['state'] = $stateName;
        }
        if ($countryName !== '') {
            $structured['country'] = $countryName;
        }

        $parsed = $this->executeSearch($structured, $headers);
        if ($parsed !== null) {
            return $parsed;
        }

        $parts = array_filter([$streetLine, $cityName, $stateName, $countryName], fn (string $s): bool => $s !== '');
        $parsed = $this->executeSearch(array_merge($baseQuery, ['q' => implode(', ', $parts)]), $headers);
        if ($parsed !== null) {
            return $parsed;
        }

        if ($countryName === '') {
            return null;
        }

        return $this->executeSearch(
            array_merge($baseQuery, ['q' => $streetLine.', '.$countryName]),
            $headers
        );
    }

    /**
     * @param  array<string, mixed>  $query
     * @param  array<string, string>  $headers
     * @return array{lat: float, lon: float}|null
     */
    protected function executeSearch(array $query, array $headers): ?array
    {
        try {
            $response = Http::withHeaders($headers)
                ->timeout((int) config('services.nominatim.timeout', 12))
                ->get('https://nominatim.openstreetmap.org/search', $query);
        } catch (\Throwable $e) {
            Log::warning('nominatim_geocode_failed', ['message' => $e->getMessage()]);

            return null;
        }

        if (! $response->successful()) {
            if (config('app.debug')) {
                Log::debug('nominatim_geocode_http', [
                    'status' => $response->status(),
                    'body_sample' => mb_substr($response->body(), 0, 300),
                ]);
            }

            return null;
        }

        $rows = $response->json();
        if (! is_array($rows) || $rows === []) {
            return null;
        }

        $first = $rows[0];
        if (! is_array($first)) {
            return null;
        }

        $lat = $first['lat'] ?? null;
        $lon = $first['lon'] ?? null;
        if ($lat === null || $lon === null) {
            return null;
        }

        return [
            'lat' => (float) $lat,
            'lon' => (float) $lon,
        ];
    }

    /**
     * Nominatim viewbox: min_lon, max_lat, max_lon, min_lat (boost, not a hard filter unless bounded=1).
     */
    protected function viewboxAroundCity(LmpCity $city): ?string
    {
        $lat = $city->latitude;
        $lon = $city->longitude;
        if ($lat === null || $lon === null || $lat === '' || $lon === '') {
            return null;
        }

        $latF = (float) $lat;
        $lonF = (float) $lon;
        $d = 0.35;
        $minLon = $lonF - $d;
        $maxLat = $latF + $d;
        $maxLon = $lonF + $d;
        $minLat = $latF - $d;

        return sprintf('%F,%F,%F,%F', $minLon, $maxLat, $maxLon, $minLat);
    }
}
