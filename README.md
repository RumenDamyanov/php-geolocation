# php-geolocation

[![Test](https://github.com/RumenDamyanov/php-geolocation/actions/workflows/test.yml/badge.svg?branch=master)](https://github.com/RumenDamyanov/php-geolocation/actions/workflows/test.yml)
[![Analyze](https://github.com/RumenDamyanov/php-geolocation/actions/workflows/analyze.yml/badge.svg?branch=master)](https://github.com/RumenDamyanov/php-geolocation/actions/workflows/analyze.yml)
[![Style](https://github.com/RumenDamyanov/php-geolocation/actions/workflows/style.yml/badge.svg?branch=master)](https://github.com/RumenDamyanov/php-geolocation/actions/workflows/style.yml)
[![codecov](https://codecov.io/gh/RumenDamyanov/php-geolocation/branch/master/graph/badge.svg)](https://codecov.io/gh/RumenDamyanov/php-geolocation)

A framework-agnostic PHP package for Cloudflare geolocation detection, with adapters for Laravel and Symfony. Provides helpers to access geolocation, language, and client information (OS, browser, device, resolution) from Cloudflare headers and other sources, and allows easy integration for language selection and other geolocation-based logic.

## Features

- Detects Cloudflare geolocation headers (country, IP, etc.)
- Helper methods to access geolocation, language, and client info (OS, browser, device, resolution)
- Configurable country-to-language mapping (supports multiple official languages per country)
- Language negotiation: matches browser and available site languages for multi-language countries
- Configurable language cookie name
- Configurable fields for returned visitor info
- Laravel middleware and config publishing
- Symfony bundle, event listener, YAML/PHP config, and service registration
- Fully tested with Pest
- PSR-12 compliant, static analysis and style checks

## Installation

```bash
composer require rumenx/php-geolocation
```

## Usage

### Plain PHP (Framework-agnostic)

```php
use Rumenx\Geolocation\Geolocation;

$countryToLanguage = [
    'CA' => ['en', 'fr'], // Canada: English (default), French
    'DE' => ['de'],
    // ...
];

$geo = new Geolocation(
    $_SERVER,                // HTTP server array
    $countryToLanguage,      // Country-to-language mapping
    'my_lang_cookie'         // (optional) custom cookie name, default: 'lang'
);

// Get best language for visitor from Canada, given available site languages
$lang = $geo->getLanguageForCountry(null, ['en', 'fr', 'de']);
// Logic:
// 1. If browser preferred language matches a country language and is available, use it
// 2. Else, check all browser languages for a match
// 3. Else, use the first country language as fallback

// Get all info (default)
$info = $geo->getGeoInfo();

// Get only specific fields
$info = $geo->getGeoInfo(['country_code', 'ip']);

// Check if language should be set (based on custom cookie name)
if ($geo->shouldSetLanguage()) {
    // ...
}
```

### Laravel

1. Register the middleware or use the service provider:
   - Add `Rumenx\Geolocation\Adapters\Laravel\GeolocationMiddleware` to your middleware stack.
   - Or, register the service provider (auto-discovered via composer.json).
2. Publish the config:
   ```bash
   php artisan vendor:publish --tag=geolocation-config
   ```
3. Configure `config/geolocation.php` as needed (country-to-language mapping, cookie name, etc).

**Example usage in a controller:**

```php
use Rumenx\Geolocation\Geolocation;

public function index(Geolocation $geo)
{
    $lang = $geo->getLanguageForCountry(null, ['en', 'fr', 'de']);
    $info = $geo->getGeoInfo();
    // ...
}
```

### Symfony

1. Register the bundle in `config/bundles.php`:
   ```php
   return [
       // ...
       Rumenx\Geolocation\Adapters\Symfony\GeolocationBundle::class => ['all' => true],
   ];
   ```
2. Configure via YAML or PHP (see `src/Adapters/Symfony/config/geolocation.yaml`):
   ```yaml
   geolocation:
     country_to_language:
       DE: [de]
       AT: [de]
       FR: [fr]
       CA: [en, fr]
     default_language: en
     language_cookie: lang
   ```
3. Register services and event listener in your `services.yaml`:
   ```yaml
   services:
     Rumenx\Geolocation\Geolocation:
       arguments:
         $server: '@request_stack'
         $countryToLanguage: '%geolocation.country_to_language%'
         $languageCookieName: '%geolocation.language_cookie%'
       public: true

     Rumenx\Geolocation\Adapters\Symfony\GeolocationListener:
       arguments:
         $countryToLanguage: '%geolocation.country_to_language%'
       tags:
         - { name: kernel.event_listener, event: kernel.request, method: onKernelRequest }
   ```

**Example usage in a controller:**

```php
use Rumenx\Geolocation\Geolocation;

public function index(Geolocation $geo)
{
    $lang = $geo->getLanguageForCountry(null, ['en', 'fr', 'de']);
    $info = $geo->getGeoInfo();
    // ...
}
```

## Configuration

The package is highly configurable. You can set the following options (see `src/config/geolocation.php` or your framework's config):

- `country_to_language` (array): Map country codes (ISO 3166-1 alpha-2) to language codes or arrays. The first language is the default for the country. Example:
  ```php
  'country_to_language' => [
      'DE' => ['de'],
      'AT' => ['de'],
      'FR' => ['fr'],
      'CA' => ['en', 'fr'],
  ],
  ```
- `default_language` (string): Fallback language if no match is found. Default: `'en'`.
- `language_cookie` (string): Name of the language cookie to check/set. Default: `'lang'`.

You can override these in your Laravel or Symfony config files as needed.

## License

[MIT](LICENSE.md)
