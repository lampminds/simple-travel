<?php

namespace App\Support;

/**
 * Parse optional coordinate strings and build map deep-links for verification.
 */
final class CoordinateMapUrls
{
    public static function parse(?string $value): ?float
    {
        if ($value === null) {
            return null;
        }

        $value = trim($value);
        if ($value === '') {
            return null;
        }

        $value = str_replace(',', '.', $value);
        if (! is_numeric($value)) {
            return null;
        }

        return (float) $value;
    }

    /**
     * @return array{lat: float, lon: float}|null
     */
    public static function pair(?string $latitude, ?string $longitude): ?array
    {
        $lat = self::parse($latitude);
        $lon = self::parse($longitude);

        if ($lat === null || $lon === null) {
            return null;
        }

        if ($lat < -90 || $lat > 90 || $lon < -180 || $lon > 180) {
            return null;
        }

        return ['lat' => $lat, 'lon' => $lon];
    }

    public static function openStreetMapUrl(float $lat, float $lon): string
    {
        $latS = self::format($lat);
        $lonS = self::format($lon);

        return "https://www.openstreetmap.org/?mlat={$latS}&mlon={$lonS}#map=18/{$latS}/{$lonS}";
    }

    public static function googleMapsUrl(float $lat, float $lon): string
    {
        $latS = self::format($lat);
        $lonS = self::format($lon);

        return "https://www.google.com/maps?q={$latS},{$lonS}";
    }

    private static function format(float $value): string
    {
        return rtrim(rtrim(sprintf('%.7F', $value), '0'), '.');
    }
}
