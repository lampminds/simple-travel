<?php

namespace App\Support;

use DOMDocument;
use DOMElement;

/**
 * Keeps a logo SVG as markup an img tag can show, without scripts or external HTML.
 */
final class SvgSanitizer
{
    /**
     * @return string|null Sanitized document, or null when the file is not a usable SVG.
     */
    public static function sanitize(string $svg): ?string
    {
        $svg = preg_replace('/^\xEF\xBB\xBF/', '', $svg) ?? $svg;
        if (stripos($svg, '<!DOCTYPE') !== false || stripos($svg, '<!ENTITY') !== false) {
            return null;
        }

        if (! str_contains(strtolower(ltrim($svg)), '<svg')) {
            return null;
        }

        $previous = libxml_use_internal_errors(true);
        $dom = new DOMDocument;
        $loaded = $dom->loadXML($svg, LIBXML_NONET);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        $root = $dom->documentElement;
        if (! $loaded || ! $root instanceof DOMElement || strtolower($root->localName) !== 'svg') {
            return null;
        }

        self::stripUnsafeNodes($dom);

        $clean = $dom->saveXML($root);

        return is_string($clean) && str_contains(strtolower($clean), '<svg') ? $clean : null;
    }

    private static function stripUnsafeNodes(DOMDocument $dom): void
    {
        $blocked = ['script', 'foreignobject', 'iframe', 'embed', 'object', 'handler'];
        $remove = [];

        foreach ($dom->getElementsByTagName('*') as $element) {
            if (in_array(strtolower($element->localName), $blocked, true)) {
                $remove[] = $element;
            }
        }

        foreach ($remove as $element) {
            $element->parentNode?->removeChild($element);
        }

        foreach ($dom->getElementsByTagName('*') as $element) {
            $drop = [];
            foreach ($element->attributes as $attribute) {
                $name = strtolower($attribute->name);
                $value = strtolower(trim($attribute->value));
                $unsafeUrl = str_contains($value, 'javascript:') || str_starts_with($value, 'data:text/html');

                if (str_starts_with($name, 'on') || ($unsafeUrl && self::isUrlAttribute($name))) {
                    $drop[] = $attribute->name;
                }
            }

            foreach ($drop as $name) {
                $element->removeAttribute($name);
            }
        }
    }

    private static function isUrlAttribute(string $name): bool
    {
        return in_array($name, ['href', 'xlink:href', 'src'], true);
    }
}
