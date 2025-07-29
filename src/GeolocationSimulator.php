<?php

/**
 * GeolocationSimulator for local development and testing (PHP 8.3+).
 *
 * Provides methods to simulate Cloudflare geolocation headers for local development
 * when actual Cloudflare infrastructure is not available.
 *
 * @category Geolocation
 * @package  Rumenx\Geolocation
 * @author   Rumen Damyanov <contact@rumenx.com>
 * @license  https://opensource.org/licenses/MIT MIT License
 * @link     https://github.com/RumenDamyanov/php-geolocation
 */

namespace Rumenx\Geolocation;

/**
 * Class GeolocationSimulator
 *
 * Simulates Cloudflare geolocation headers for local development.
 *
 * @category Geolocation
 * @package  Rumenx\Geolocation
 * @author   Rumen Damyanov <contact@rumenx.com>
 * @license  https://opensource.org/licenses/MIT MIT License
 * @link     https://github.com/RumenDamyanov/php-geolocation
 */
class GeolocationSimulator
{
    /**
     * Common countries with their typical data for simulation.
     *
     * @var array<string, array<string, mixed>>
     */
    private static array $countryData = [
        'US' => [
            'country' => 'US',
            'ip_ranges' => ['192.168.1.', '10.0.0.', '172.16.0.'],
            'languages' => ['en-US', 'en', 'es'],
            'timezone' => 'America/New_York'
        ],
        'CA' => [
            'country' => 'CA',
            'ip_ranges' => ['192.168.2.', '10.0.1.', '172.16.1.'],
            'languages' => ['en-CA', 'en', 'fr-CA', 'fr'],
            'timezone' => 'America/Toronto'
        ],
        'GB' => [
            'country' => 'GB',
            'ip_ranges' => ['192.168.3.', '10.0.2.', '172.16.2.'],
            'languages' => ['en-GB', 'en'],
            'timezone' => 'Europe/London'
        ],
        'DE' => [
            'country' => 'DE',
            'ip_ranges' => ['192.168.4.', '10.0.3.', '172.16.3.'],
            'languages' => ['de-DE', 'de', 'en'],
            'timezone' => 'Europe/Berlin'
        ],
        'FR' => [
            'country' => 'FR',
            'ip_ranges' => ['192.168.5.', '10.0.4.', '172.16.4.'],
            'languages' => ['fr-FR', 'fr', 'en'],
            'timezone' => 'Europe/Paris'
        ],
        'JP' => [
            'country' => 'JP',
            'ip_ranges' => ['192.168.6.', '10.0.5.', '172.16.5.'],
            'languages' => ['ja-JP', 'ja', 'en'],
            'timezone' => 'Asia/Tokyo'
        ],
        'AU' => [
            'country' => 'AU',
            'ip_ranges' => ['192.168.7.', '10.0.6.', '172.16.6.'],
            'languages' => ['en-AU', 'en'],
            'timezone' => 'Australia/Sydney'
        ],
        'BR' => [
            'country' => 'BR',
            'ip_ranges' => ['192.168.8.', '10.0.7.', '172.16.7.'],
            'languages' => ['pt-BR', 'pt', 'en'],
            'timezone' => 'America/Sao_Paulo'
        ]
    ];

    /**
     * Generate fake Cloudflare headers for a specific country.
     *
     * @param string $countryCode ISO 3166-1 alpha-2 country code
     * @param array<string, mixed> $options Additional options for customization
     *
     * @return array<string, string> Simulated HTTP headers
     */
    public static function fakeCloudflareHeaders(string $countryCode = 'US', array $options = []): array
    {
        $countryCode = strtoupper($countryCode);
        $data = self::$countryData[$countryCode] ?? self::$countryData['US'];

        // Generate fake IP
        $ipRange = $options['ip_range'] ?? ($data['ip_ranges'][array_rand($data['ip_ranges'])]);
        $fakeIp = $ipRange . rand(1, 254);

        // Select language
        $languages = $options['languages'] ?? $data['languages'];
        $primaryLang = $languages[0];
        $acceptLanguage = implode(',', array_map(function ($lang, $index) {
            $weight = 1.0 - ($index * 0.1);
            return $index === 0 ? $lang : "{$lang};q={$weight}";
        }, $languages, array_keys($languages)));

        return [
            'HTTP_CF_IPCOUNTRY' => $countryCode,
            'HTTP_CF_CONNECTING_IP' => $fakeIp,
            'HTTP_CF_RAY' => strtolower(bin2hex(random_bytes(8))) . '-' . strtoupper($countryCode),
            'HTTP_CF_VISITOR' => '{"scheme":"https"}',
            'HTTP_ACCEPT_LANGUAGE' => $acceptLanguage,
            'REMOTE_ADDR' => $fakeIp,
            'HTTP_USER_AGENT' => $options['user_agent'] ?? self::generateFakeUserAgent(),
            'HTTP_X_FORWARDED_FOR' => $fakeIp,
            'SERVER_NAME' => $options['server_name'] ?? 'localhost',
            'SERVER_PORT' => $options['server_port'] ?? '80',
            'HTTPS' => $options['https'] ?? '',
        ];
    }

    /**
     * Generate a random fake user agent string.
     *
     * @return string Fake user agent
     */
    public static function generateFakeUserAgent(): string
    {
        $userAgents = [
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36' .
                ' (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36' .
                ' (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
            'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36' .
                ' (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:89.0) Gecko/20100101 Firefox/89.0',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.15; rv:89.0) Gecko/20100101 Firefox/89.0',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15' .
                ' (KHTML, like Gecko) Version/14.1.1 Safari/605.1.15',
            'Mozilla/5.0 (iPhone; CPU iPhone OS 14_6 like Mac OS X) AppleWebKit/605.1.15' .
                ' (KHTML, like Gecko) Version/14.1.1 Mobile/15E148 Safari/604.1',
            'Mozilla/5.0 (iPad; CPU OS 14_6 like Mac OS X) AppleWebKit/605.1.15' .
                ' (KHTML, like Gecko) Version/14.1.1 Mobile/15E148 Safari/604.1'
        ];

        return $userAgents[array_rand($userAgents)];
    }

    /**
     * Get a random country code for simulation.
     *
     * @return string Random country code
     */
    public static function randomCountry(): string
    {
        return array_rand(self::$countryData);
    }

    /**
     * Get available countries for simulation.
     *
     * @return array<string> List of available country codes
     */
    public static function getAvailableCountries(): array
    {
        return array_keys(self::$countryData);
    }

    /**
     * Create a simulated $_SERVER array with Cloudflare headers.
     *
     * @param string $countryCode Country code to simulate
     * @param array<string, mixed> $options Additional options
     * @param array<string, string> $baseServer Base $_SERVER array to merge with
     *
     * @return array<string, string> Complete $_SERVER array with simulated data
     */
    public static function simulateServer(
        string $countryCode = 'US',
        array $options = [],
        array $baseServer = []
    ): array {
        $baseServer = $baseServer ?: $_SERVER;
        $fakeHeaders = self::fakeCloudflareHeaders($countryCode, $options);

        return array_merge($baseServer, $fakeHeaders);
    }

    /**
     * Create a Geolocation instance with simulated data.
     *
     * @param string $countryCode Country code to simulate
     * @param array<string, string|array<int, string>> $countryToLanguage Country to language mapping
     * @param string $languageCookieName Language cookie name
     * @param array<string, mixed> $options Additional simulation options
     *
     * @return Geolocation Geolocation instance with simulated data
     * @phpstan-return Geolocation
     */
    public static function create(
        string $countryCode = 'US',
        array $countryToLanguage = [],
        string $languageCookieName = 'lang',
        array $options = []
    ): Geolocation {
        $simulatedServer = self::simulateServer($countryCode, $options);

        return new Geolocation($simulatedServer, $countryToLanguage, $languageCookieName);
    }

    /**
     * Add custom country data for simulation.
     *
     * @param string $countryCode Country code
     * @param array<string, mixed> $data Country data
     *
     * @return void
     */
    public static function addCountryData(string $countryCode, array $data): void
    {
        self::$countryData[strtoupper($countryCode)] = $data;
    }
}
