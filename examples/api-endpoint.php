<?php

/**
 * API endpoint example
 * Shows how to use geolocation in a REST API to customize responses
 */

require_once __DIR__ . '/../vendor/autoload.php';

use Rumenx\Geolocation\Geolocation;

// Simulate an API request
header('Content-Type: application/json');

// Initialize geolocation
$geo = new Geolocation();

// For local development, simulate different countries
if ($geo->isLocalDevelopment()) {
    $simulatedCountries = ['US', 'GB', 'DE', 'JP', 'AU'];
    $randomCountry = $simulatedCountries[array_rand($simulatedCountries)];
    $geo = Geolocation::simulate($randomCountry);
}

// Get visitor information
$geoInfo = $geo->getGeoInfo();

// Country-specific API responses
$countryData = [
    'US' => [
        'currency' => 'USD',
        'timezone' => 'America/New_York',
        'features' => ['premium_support', 'advanced_analytics'],
        'locale' => 'en_US',
    ],
    'GB' => [
        'currency' => 'GBP',
        'timezone' => 'Europe/London',
        'features' => ['gdpr_compliance', 'premium_support'],
        'locale' => 'en_GB',
    ],
    'DE' => [
        'currency' => 'EUR',
        'timezone' => 'Europe/Berlin',
        'features' => ['gdpr_compliance', 'data_sovereignty'],
        'locale' => 'de_DE',
    ],
    'JP' => [
        'currency' => 'JPY',
        'timezone' => 'Asia/Tokyo',
        'features' => ['local_payment_methods', 'mobile_optimized'],
        'locale' => 'ja_JP',
    ],
];

// Default data for unlisted countries
$defaultData = [
    'currency' => 'USD',
    'timezone' => 'UTC',
    'features' => ['basic_support'],
    'locale' => 'en_US',
];

$countryCode = $geoInfo['country_code'];
$responseData = $countryData[$countryCode] ?? $defaultData;

// Build API response
$apiResponse = [
    'visitor' => [
        'country' => $countryCode,
        'language' => $geoInfo['preferred_language'],
        'device' => $geoInfo['device'],
        'browser' => $geoInfo['browser']['browser'] ?? 'unknown',
    ],
    'localization' => $responseData,
    'timestamp' => date('c'),
    'debug' => [
        'is_simulation' => $geo->isLocalDevelopment(),
        'user_agent' => $geoInfo['user_agent'] ?? 'unknown',
    ],
];

// Output JSON response
echo json_encode($apiResponse, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
