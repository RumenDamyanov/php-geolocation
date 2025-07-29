<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Rumenx\Geolocation\Geolocation;

echo "🌍 PHP Geolocation Package - Local Development Simulation Demo\n";
echo "===============================================================\n\n";

// Check if we're in local development
$geo = new Geolocation();
if ($geo->isLocalDevelopment()) {
    echo "✅ Detected local development environment\n\n";

    // Simulate different countries
    $countries = ['US', 'CA', 'GB', 'DE', 'FR', 'JP'];

    foreach ($countries as $country) {
        echo "🎭 Simulating geolocation for: {$country}\n";

        $geo = Geolocation::simulate($country);
        $info = $geo->getGeoInfo();

        echo "   Country: {$info['country_code']}\n";
        echo "   Language: {$info['preferred_language']}\n";
        echo "   Browser: {$info['browser']['browser']} {$info['browser']['version']}\n";
        echo "   OS: {$info['os']}\n";
        echo "   Device: {$info['device']}\n";
        echo "\n";
    }

} else {
    echo "🌐 Running in production - using real Cloudflare headers\n";
    $info = $geo->getGeoInfo();
    print_r($info);
}

echo "Demo completed! 🚀\n";
