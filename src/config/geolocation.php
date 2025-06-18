<?php

/**
 * Geolocation configuration file for PHP 8.3+ projects.
 *
 * Provides the default country-to-language mapping and other geolocation settings.
 *
 * @category Geolocation
 * @package  Rumenx\Geolocation
 * @author   Rumen Damyanov <contact@rumenx.com>
 * @license  https://opensource.org/licenses/MIT MIT License
 * @link     https://github.com/RumenDamyanov/php-geolocation
 * @version  GIT: <git_id>
 */

return [
    // Map country codes to language codes or arrays (first is fallback)
    'country_to_language' => [
        'DE' => ['de'], // Germany
        'AT' => ['de'], // Austria
        'FR' => ['fr'], // France
        'CA' => ['en', 'fr'], // Canada: English (default), French
        // Add more as needed
    ],
    // Default language if no match
    'default_language' => 'en',
    // Language cookie name
    'language_cookie' => 'lang',
];
