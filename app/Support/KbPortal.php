<?php

namespace App\Support;

/**
 * Ticket portal (knowledge base) credentials from KB_URL and KB_API_KEY.
 */
final class KbPortal
{
    public static function isConfigured(): bool
    {
        return filled(config('services.kb.url')) && filled(config('services.kb.key'));
    }
}
