<?php

namespace App\Services\Geocoding;

use App\Models\LmpCity;

/**
 * Prepares a street line pasted from Google Maps (or similar) for Nominatim forward geocoding.
 *
 * Users often paste the full formatted address even though city/state/country are already chosen
 * in the wizard. Nominatim works best with street + number (+ optional neighbourhood).
 */
final class ServiceLocationAddressNormalizer
{
    /**
     * @return list<string> Unique non-empty search strings, most specific first.
     */
    public function searchVariants(string $rawStreet, LmpCity $city): array
    {
        $trimmed = $this->stripPastedLocalitySuffixes(trim($rawStreet), $city);
        $trimmed = $this->stripPostalCodes($trimmed);
        $trimmed = trim(preg_replace('/\s+/u', ' ', $trimmed) ?? $trimmed);

        $variants = array_filter([
            $trimmed,
            $this->firstSegmentBeforeDash($trimmed),
            $this->simplifyStreetTokens($trimmed),
            $this->simplifyStreetTokens($this->firstSegmentBeforeDash($trimmed) ?? ''),
        ]);

        $core = $this->streetCoreWithNumber($trimmed);
        if ($core !== '') {
            $variants[] = $core;
        }

        return array_values(array_unique(array_filter(
            $variants,
            fn (string $v): bool => $v !== '' && mb_strlen($v) >= 3
        )));
    }

    /**
     * Extract Brazilian CEP if present (85860-320 or 85860320).
     */
    public function extractBrazilianPostalCode(string $address): ?string
    {
        if (preg_match('/\b(\d{5})-?(\d{3})\b/u', $address, $m) !== 1) {
            return null;
        }

        return $m[1].'-'.$m[2];
    }

    public function stripPastedLocalitySuffixes(string $street, LmpCity $city): string
    {
        $city->loadMissing(['state.country']);

        $cutMarkers = array_filter([
            trim((string) $city->name),
            $city->state !== null ? trim((string) $city->state->name) : null,
            $city->state?->country !== null ? trim((string) $city->state->country->name) : null,
            'Brasil',
            'Brazil',
        ], fn (?string $s): bool => $s !== null && $s !== '');

        $lower = mb_strtolower($street);
        $earliest = null;

        foreach ($cutMarkers as $marker) {
            $pos = mb_stripos($lower, mb_strtolower($marker));
            if ($pos !== false && $pos > 0 && ($earliest === null || $pos < $earliest)) {
                $earliest = $pos;
            }
        }

        if ($earliest !== null) {
            $street = mb_substr($street, 0, $earliest);
        }

        return trim($street, " \t\n\r\0\x0B,;-");
    }

    public function stripPostalCodes(string $street): string
    {
        $street = preg_replace('/,?\s*\b\d{5}-?\d{3}\b/u', '', $street) ?? $street;

        return trim($street, " \t\n\r\0\x0B,;-");
    }

    protected function firstSegmentBeforeDash(string $street): ?string
    {
        if (! str_contains($street, ' - ')) {
            return null;
        }

        $segment = trim(explode(' - ', $street, 2)[0]);

        return $segment !== '' && $segment !== $street ? $segment : null;
    }

    protected function simplifyStreetTokens(string $street): string
    {
        $s = $street;
        $s = preg_replace('/\b(av\.?|avenida|r\.?|rua|al\.?|alameda|trav\.?|travessa|rod\.?|rodovia)\b/iu', ' ', $s) ?? $s;
        $s = preg_replace('/\b(eng\.?|engenheiro|dr\.?|doutor|prof\.?)\b/iu', ' ', $s) ?? $s;
        $s = preg_replace('/[,.-]+/u', ' ', $s) ?? $s;
        $s = preg_replace('/\s+/u', ' ', trim($s)) ?? $s;

        return $s;
    }

    /**
     * Keeps tokens around the house number (helps when OSM omits abbreviations).
     */
    protected function streetCoreWithNumber(string $street): string
    {
        $simplified = $this->simplifyStreetTokens($street);
        if ($simplified === '') {
            return '';
        }

        if (preg_match('/(\d{1,6})/u', $simplified, $m, PREG_OFFSET_CAPTURE) !== 1) {
            return $simplified;
        }

        $pos = $m[0][1];
        $tokens = preg_split('/\s+/u', $simplified, -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $numberIndex = null;

        foreach ($tokens as $i => $token) {
            if (preg_match('/^\d{1,6}$/u', $token) === 1) {
                $numberIndex = $i;
                break;
            }
        }

        if ($numberIndex === null) {
            return $simplified;
        }

        $from = max(0, $numberIndex - 4);
        $slice = array_slice($tokens, $from, min(count($tokens) - $from, 6));

        return implode(' ', $slice);
    }
}
