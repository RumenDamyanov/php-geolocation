<?php

/**
 * Simple content localization example
 * Shows how to redirect users based on their country
 */

require_once __DIR__ . '/../vendor/autoload.php';

use Rumenx\Geolocation\Geolocation;

// Country to domain mapping
$countryDomains = [
    'US' => 'https://example.com',
    'CA' => 'https://example.ca',
    'GB' => 'https://example.co.uk',
    'DE' => 'https://example.de',
    'FR' => 'https://example.fr',
    'JP' => 'https://example.jp',
    'AU' => 'https://example.com.au',
];

// Default domain for unlisted countries
$defaultDomain = 'https://example.com';

// Initialize geolocation
$geo = new Geolocation();

// For local development, simulate a country
if ($geo->isLocalDevelopment()) {
    echo "🔧 Local development detected - simulating geolocation\n";
    $geo = Geolocation::simulate('DE'); // Simulate German visitor
}

$countryCode = $geo->getCountryCode();
$language = $geo->getLanguageForCountry($countryCode);

echo "🌍 Visitor Details:\n";
echo "   Country: {$countryCode}\n";
echo "   Language: {$language}\n";

// Get appropriate domain
$targetDomain = $countryDomains[$countryCode] ?? $defaultDomain;

echo "🎯 Redirecting to: {$targetDomain}\n";

// In a real application, you would redirect:
// header("Location: {$targetDomain}");
// exit;

echo "✅ Content localization complete!\n";
