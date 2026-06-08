<?php

/**
 * Application overrides for Livewire (package defaults are merged).
 *
 * @see vendor/livewire/livewire/config/livewire.php
 */
return [

    /*
    |---------------------------------------------------------------------------
    | Payload Guards
    |---------------------------------------------------------------------------
    |
    | Filament RichEditor (TipTap) syncs deeply nested document nodes over
    | Livewire (e.g. data.translations.{id}.text.content.0.content...). The
    | default depth of 10 is too low for formatted helper HTML in admin forms.
    | Keep a finite limit to retain DoS protection on public Livewire endpoints.
    |
    */

    'payload' => [
        'max_nesting_depth' => (int) env('LIVEWIRE_MAX_NESTING_DEPTH', 25),
    ],
];
