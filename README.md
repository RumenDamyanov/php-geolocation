# php-geolocation

[![Test](https://github.com/RumenDamyanov/php-geolocation/actions/workflows/test.yml/badge.svg?branch=master)](https://github.com/RumenDamyanov/php-geolocation/actions/workflows/test.yml)
[![Analyze](https://github.com/RumenDamyanov/php-geolocation/actions/workflows/analyze.yml/badge.svg?branch=master)](https://github.com/RumenDamyanov/php-geolocation/actions/workflows/analyze.yml)
[![Style](https://github.com/RumenDamyanov/php-geolocation/actions/workflows/style.yml/badge.svg?branch=master)](https://github.com/RumenDamyanov/php-geolocation/actions/workflows/style.yml)
[![codecov](https://codecov.io/gh/RumenDamyanov/php-geolocation/branch/master/graph/badge.svg)](https://codecov.io/gh/RumenDamyanov/php-geolocation)

A simple, framework-agnostic PHP utility for Cloudflare geolocation detection and client information extraction. Provides helpers to access geolocation, language, and client information (OS, browser, device, resolution) from HTTP headers.

## Features

- 🌍 Detects Cloudflare geolocation headers (country, IP, etc.)
- 🔍 Helper methods to access geolocation, language, and client info (OS, browser, device, resolution)
- 🌐 Configurable country-to-language mapping (supports multiple official languages per country)
- 🤝 Language negotiation: matches browser and available site languages for multi-language countries
- 🍪 Configurable language cookie name
- ⚙️ Configurable fields for returned visitor info
- 🛠️ **Local development simulation** - Fake Cloudflare headers for testing without production setup
- 🎭 **Auto-detection** of local environments (localhost, local IPs, missing Cloudflare headers)
- 🧪 Fully tested with Pest (100% coverage)
- ✅ PSR-12 compliant, static analysis and style checks
- 🚀 Simple utility class - no framework dependencies or complex setup required

## Why This Design?

This package is intentionally designed as a **simple utility library** rather than a complex framework integration. Here's why:

### 🎯 Focused Purpose
- **Single responsibility**: Extract and process geolocation data from HTTP headers
- **Pure functions**: No side effects, no global state, predictable behavior
- **Framework-agnostic**: Works with any PHP application or framework

### 🔧 Easy Integration
- **No service providers needed**: Just instantiate the class when you need it
- **No configuration files**: Pass configuration directly to the constructor
- **No middleware complexity**: Use it exactly where and when you need it
- **Developer control**: You decide how and when to use geolocation data

### 📦 Minimal Dependencies
- **Zero runtime dependencies**: Only requires PHP 8.3+
- **Small footprint**: Single class, focused functionality
- **Fast installation**: No complex dependency trees
- **Version compatibility**: No framework version constraints

This approach makes the package more reliable, easier to understand, and simpler to maintain - following the Unix philosophy of "do one thing and do it well."

## Installation

```bash
composer require rumenx/php-geolocation
```

## Local Development Simulation

When developing locally where Cloudflare is not available, you can simulate its functionality:

### Quick Simulation

```php
use Rumenx\Geolocation\Geolocation;

// Create a simulated instance for a specific country
$geo = Geolocation::simulate('DE', [
    'DE' => ['de'],
    'CA' => ['en', 'fr']
]);

echo $geo->getCountryCode(); // 'DE'
echo $geo->getIp(); // Simulated IP like '192.168.4.123'
```

### Advanced Simulation

```php
use Rumenx\Geolocation\GeolocationSimulator;

// Generate fake Cloudflare headers
$headers = GeolocationSimulator::fakeCloudflareHeaders('JP', [
    'user_agent' => 'Custom User Agent',
    'server_name' => 'dev.example.com'
]);

// Create instance with simulated server data
$geo = new Geolocation($headers, ['JP' => ['ja', 'en']]);
```

### Auto-Detection of Local Environment

```php
$geo = new Geolocation();

if ($geo->isLocalDevelopment()) {
    // Automatically detected: localhost, local IPs, or missing Cloudflare headers
    echo "Running in local development mode";
}
```

### Available Countries for Simulation

```php
// Get list of built-in countries
$countries = GeolocationSimulator::getAvailableCountries();
// ['US', 'CA', 'GB', 'DE', 'FR', 'JP', 'AU', 'BR']

// Get random country for testing
$randomCountry = GeolocationSimulator::randomCountry();
```

### Framework Integration for Development

For Laravel and Symfony, check the `/examples` directory for middleware and event listeners that automatically inject simulated Cloudflare headers in development environments.

## Usage

### Basic Usage

```php
use Rumenx\Geolocation\Geolocation;

// Simple usage with defaults
$geo = new Geolocation();
$country = $geo->getCountryCode();
$ip = $geo->getIp();
$info = $geo->getGeoInfo();

// Advanced usage with custom configuration
$countryToLanguage = [
    'CA' => ['en', 'fr'], // Canada: English (default), French
    'DE' => ['de'],       // Germany: German
    'CH' => ['de', 'fr', 'it'], // Switzerland: German, French, Italian
    // Add more countries as needed...
];

$geo = new Geolocation(
    $_SERVER,                // HTTP server array (optional, defaults to $_SERVER)
    $countryToLanguage,      // Country-to-language mapping (optional)
    'my_lang_cookie'         // Custom cookie name (optional, defaults to 'lang')
);
```

### Language Detection

```php
// Get best language for visitor based on country and browser preferences
$availableSiteLanguages = ['en', 'fr', 'de', 'es'];
$lang = $geo->getLanguageForCountry(null, $availableSiteLanguages);

// Language selection logic:
// 1. If browser preferred language matches a country language and is available, use it
// 2. Else, check all browser languages for a match with available languages
// 3. Else, use the first country language as fallback
// 4. Returns null if no match found

// Check if language should be set (based on cookie)
if ($geo->shouldSetLanguage()) {
    // Set language in your application
    setcookie($geo->languageCookieName, $lang);
}
```

### Client Information

```php
// Get specific information
$country = $geo->getCountryCode();     // 'US', 'CA', 'DE', etc.
$ip = $geo->getIp();                   // '192.168.1.1'
$browser = $geo->getBrowser();         // ['name' => 'Chrome', 'version' => '91.0']
$os = $geo->getOs();                   // 'Windows 10', 'macOS', 'Linux', etc.
$device = $geo->getDeviceType();       // 'desktop', 'mobile', 'tablet'
$resolution = $geo->getResolution();   // ['width' => 1920, 'height' => 1080]

// Get all information at once
$info = $geo->getGeoInfo();
// Returns: [
//     'country_code' => 'US',
//     'ip' => '192.168.1.1',
//     'preferred_language' => 'en-US',
//     'all_languages' => ['en-US', 'en', 'fr'],
//     'user_agent' => 'Mozilla/5.0...',
//     'browser' => ['name' => 'Chrome', 'version' => '91.0'],
//     'os' => 'Windows 10',
//     'device_type' => 'desktop',
//     'resolution' => ['width' => 1920, 'height' => 1080]
// ]

// Get only specific fields
$specificInfo = $geo->getGeoInfo(['country_code', 'ip', 'browser']);
```

### Framework Integration Examples

#### Laravel

```php
// In a controller
class HomeController extends Controller
{
    public function index(Request $request)
    {
        $geo = new Geolocation(
            $request->server->all(),
            config('app.country_to_language', [])
        );

        $country = $geo->getCountryCode();
        $lang = $geo->getLanguageForCountry(null, ['en', 'fr', 'de']);

        if ($geo->shouldSetLanguage() && $lang) {
            app()->setLocale($lang);
        }

        return view('home', [
            'geo' => $geo->getGeoInfo(['country_code', 'ip']),
            'language' => $lang
        ]);
    }
}

// In a middleware (optional)
class GeolocationMiddleware
{
    public function handle($request, Closure $next)
    {
        $geo = new Geolocation($request->server->all());
        $lang = $geo->getLanguageForCountry();

        if ($lang && $geo->shouldSetLanguage()) {
            app()->setLocale($lang);
        }

        return $next($request);
    }
}
```

#### Symfony

```php
// In a controller
class HomeController extends AbstractController
{
    public function index(Request $request): Response
    {
        $geo = new Geolocation(
            $request->server->all(),
            $this->getParameter('country_to_language')
        );

        $country = $geo->getCountryCode();
        $lang = $geo->getLanguageForCountry(null, ['en', 'fr', 'de']);

        if ($geo->shouldSetLanguage() && $lang) {
            $request->setLocale($lang);
        }

        return $this->render('home.html.twig', [
            'geo' => $geo->getGeoInfo(),
            'language' => $lang
        ]);
    }
}

// In an event listener (optional)
class GeolocationListener
{
    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        $geo = new Geolocation($request->server->all());
        $lang = $geo->getLanguageForCountry();

        if ($lang && $geo->shouldSetLanguage()) {
            $request->setLocale($lang);
        }
    }
}
```

#### Plain PHP

```php
// In any PHP application
session_start();

$geo = new Geolocation($_SERVER, [
    'US' => ['en'],
    'CA' => ['en', 'fr'],
    'DE' => ['de'],
    'FR' => ['fr']
]);

$country = $geo->getCountryCode();
$lang = $geo->getLanguageForCountry(null, ['en', 'fr', 'de']);

// Set language preference
if ($geo->shouldSetLanguage() && $lang) {
    $_SESSION['language'] = $lang;
    setcookie('lang', $lang, time() + (86400 * 30)); // 30 days
}

// Use the information
echo "Welcome visitor from: " . ($country ?? 'Unknown');
echo "Preferred language: " . ($lang ?? 'Default');
```

## Configuration

The package is simple and requires minimal configuration. All settings are passed directly to the constructor:

### Constructor Parameters

```php
$geo = new Geolocation($server, $countryToLanguage, $languageCookieName);
```

- **`$server`** (array, optional): HTTP server array, defaults to `$_SERVER`
- **`$countryToLanguage`** (array, optional): Country code to language mapping
- **`$languageCookieName`** (string, optional): Language cookie name, defaults to `'lang'`

### Country-to-Language Mapping

Map country codes (ISO 3166-1 alpha-2) to language codes or arrays. The first language is the default for the country:

```php
$countryToLanguage = [
    'US' => ['en'],                    // United States: English only
    'CA' => ['en', 'fr'],              // Canada: English (default), French
    'CH' => ['de', 'fr', 'it', 'rm'],  // Switzerland: German (default), French, Italian, Romansh
    'BE' => ['nl', 'fr', 'de'],        // Belgium: Dutch (default), French, German
    'IN' => ['hi', 'en'],              // India: Hindi (default), English
    'ZA' => ['en', 'af', 'zu'],        // South Africa: English (default), Afrikaans, Zulu
    // Add more countries as needed...
];
```

### Example Configurations

#### Minimal Setup
```php
// Use defaults for everything
$geo = new Geolocation();
```

#### Basic Country Mapping
```php
$geo = new Geolocation($_SERVER, [
    'DE' => ['de'],
    'FR' => ['fr'],
    'ES' => ['es']
]);
```

#### Custom Cookie Name
```php
$geo = new Geolocation($_SERVER, [], 'user_language');
```

#### Full Configuration
```php
$geo = new Geolocation(
    $_SERVER,  // or $request->server->all() in frameworks
    [
        'US' => ['en'],
        'CA' => ['en', 'fr'],
        'MX' => ['es'],
        'DE' => ['de'],
        'AT' => ['de'],
        'CH' => ['de', 'fr', 'it'],
        'FR' => ['fr'],
        'BE' => ['nl', 'fr'],
        'IT' => ['it'],
        'ES' => ['es'],
        'BR' => ['pt'],
        'PT' => ['pt'],
        'RU' => ['ru'],
        'CN' => ['zh'],
        'JP' => ['ja'],
        'KR' => ['ko']
    ],
    'preferred_language'
);
```

No configuration files, service providers, or complex setup needed!

## Examples

The [`examples/`](examples/) directory contains practical demonstrations of the package capabilities:

### 🎯 Basic Usage

- **[`demo.php`](examples/demo.php)** - Interactive demo showing simulation in local development with multiple countries

### ⚡ Framework Integration

- **[`LaravelDevelopmentMiddleware.php`](examples/LaravelDevelopmentMiddleware.php)** - Laravel middleware for automatic header injection in development
- **[`SymfonyDevelopmentListener.php`](examples/SymfonyDevelopmentListener.php)** - Symfony event listener for request-level simulation

### 🌍 Real-World Applications

- **[`content-localization.php`](examples/content-localization.php)** - Redirect visitors to country-specific domains
- **[`api-endpoint.php`](examples/api-endpoint.php)** - REST API with geolocation-based responses (currency, features, etc.)
- **[`multi-language.php`](examples/multi-language.php)** - Automatic language detection with fallbacks for multi-language sites

### Running Examples

```bash
# Basic simulation demo
php examples/demo.php

# Content localization
php examples/content-localization.php

# API endpoint simulation
php examples/api-endpoint.php

# Multi-language detection
php examples/multi-language.php
```

All examples automatically detect local development and use simulation, so they work perfectly without Cloudflare setup! 🚀

## Contributing

We welcome contributions! Please see our [Contributing Guide](CONTRIBUTING.md) for details on how to contribute to this project.

### Development

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Run tests: `composer test`
5. Run static analysis: `composer analyze`
6. Check code style: `composer style`
7. Submit a pull request

## Security

If you discover a security vulnerability, please see our [Security Policy](SECURITY.md) for information on how to report it responsibly.

## Changelog

All notable changes to this project are documented in the [Changelog](CHANGELOG.md).

## Support

- 📖 [Documentation](README.md)
- 🐛 [Issue Tracker](https://github.com/RumenDamyanov/php-geolocation/issues)
- 💬 [Discussions](https://github.com/RumenDamyanov/php-geolocation/discussions)
- 💖 [Sponsor this project](FUNDING.md)

## License

[MIT](LICENSE.md)
