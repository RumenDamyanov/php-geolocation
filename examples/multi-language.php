<?php

/**
 * Multi-language site example
 * Shows how to implement automatic language detection and fallbacks
 */

require_once __DIR__ . '/../vendor/autoload.php';

use Rumenx\Geolocation\Geolocation;

// Available languages on your site
$availableLanguages = ['en', 'fr', 'de', 'es', 'ja'];

// Country to language mapping with multiple official languages
$countryToLanguage = [
    'US' => ['en'],
    'CA' => ['en', 'fr'],
    'GB' => ['en'],
    'FR' => ['fr'],
    'DE' => ['de'],
    'ES' => ['es'],
    'JP' => ['ja'],
    'CH' => ['de', 'fr'], // Switzerland
    'BE' => ['fr', 'de'], // Belgium
];

// Initialize geolocation
$geo = new Geolocation($_SERVER, $countryToLanguage, 'site_lang');

// For local development, simulate different scenarios
if ($geo->isLocalDevelopment()) {
    echo "🔧 Local development - simulating different countries:\n\n";

    $testCountries = ['US', 'CA', 'FR', 'DE', 'CH', 'JP'];

    foreach ($testCountries as $country) {
        $testGeo = Geolocation::simulate($country, $countryToLanguage);
        $detectedLang = $testGeo->getLanguageForCountry($country, $availableLanguages);

        echo "🌍 {$country}: {$detectedLang}\n";
    }

    echo "\n" . str_repeat('=', 50) . "\n\n";

    // Use German simulation for the rest of the demo
    $geo = Geolocation::simulate('DE', $countryToLanguage);
}

// Get visitor's country and language preference
$countryCode = $geo->getCountryCode();
$detectedLanguage = $geo->getLanguageForCountry($countryCode, $availableLanguages);

echo "🎯 Language Detection Results:\n";
echo "   Country: {$countryCode}\n";
echo "   Detected Language: {$detectedLanguage}\n";

// Check if we should set the language cookie
if ($geo->shouldSetLanguage()) {
    echo "   Action: Setting language cookie\n";
    // In a real application:
    // setcookie('site_lang', $detectedLanguage, time() + (86400 * 30), '/');
} else {
    echo "   Action: Language cookie already set\n";
}

// Language-specific content (example)
$content = [
    'en' => [
        'welcome' => 'Welcome to our website!',
        'description' => 'Experience our amazing products and services.',
    ],
    'fr' => [
        'welcome' => 'Bienvenue sur notre site web !',
        'description' => 'Découvrez nos produits et services extraordinaires.',
    ],
    'de' => [
        'welcome' => 'Willkommen auf unserer Website!',
        'description' => 'Erleben Sie unsere erstaunlichen Produkte und Dienstleistungen.',
    ],
    'es' => [
        'welcome' => '¡Bienvenido a nuestro sitio web!',
        'description' => 'Experimenta nuestros increíbles productos y servicios.',
    ],
    'ja' => [
        'welcome' => '私たちのウェブサイトへようこそ！',
        'description' => '素晴らしい製品とサービスをご体験ください。',
    ],
];

// Default to English if language not available
$selectedContent = $content[$detectedLanguage] ?? $content['en'];

echo "\n📄 Localized Content:\n";
echo "   Title: {$selectedContent['welcome']}\n";
echo "   Description: {$selectedContent['description']}\n";

echo "\n✅ Multi-language setup complete!\n";
