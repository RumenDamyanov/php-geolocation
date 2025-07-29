<?php

/**
 * Geolocation core class for Cloudflare and general geolocation detection (PHP 8.3+).
 *
 * Provides methods to extract geolocation, language, and basic client info (OS, browser, device) from HTTP headers.
 *
 * @category Geolocation
 * @package  Rumenx\Geolocation
 * @author   Rumen Damyanov <contact@rumenx.com>
 * @license  https://opensource.org/licenses/MIT MIT License
 * @link     https://github.com/RumenDamyanov/php-geolocation
 */

namespace Rumenx\Geolocation;

/**
 * Class Geolocation
 *
 * Main geolocation and client info helper for Cloudflare and general use.
 */
class Geolocation
{
    /**
     * HTTP server array (e.g. $_SERVER)
     *
     * @var array<string, string>
     */
    protected array $server;

    /**
     * Country code to language mapping
     *
     * @var array<string, string|array<int, string>>
     */
    protected array $countryToLanguage;

    /**
     * Name of the language cookie
     *
     * @var string
     */
    protected string $languageCookieName;

    /**
     * Geolocation constructor.
     *
     * @param array<string, string>                    $server             HTTP server array (default: $_SERVER)
     * @param array<string, string|array<int, string>> $countryToLanguage  Country code to language mapping
     * @param string                                   $languageCookieName Name of the language cookie (default: 'lang')
     */
    public function __construct(
        array $server = [],
        array $countryToLanguage = [],
        string $languageCookieName = 'lang'
    ) {
        $this->server = empty($server) ? $_SERVER : $server;
        $this->countryToLanguage = $countryToLanguage;
        $this->languageCookieName = $languageCookieName;
    }

    /**
     * Get the country code from Cloudflare header.
     *
     * @return string|null Country code or null
     */
    public function getCountryCode(): ?string
    {
        return $this->server['HTTP_CF_IPCOUNTRY'] ?? null;
    }

    /**
     * Get the visitor IP address (Cloudflare or REMOTE_ADDR).
     *
     * @return string|null IP address or null
     */
    public function getIp(): ?string
    {
        return $this->server['HTTP_CF_CONNECTING_IP']
            ?? $this->server['REMOTE_ADDR']
            ?? null;
    }

    /**
     * Get the preferred language from Accept-Language header.
     *
     * @return string|null Preferred language or null
     */
    public function getPreferredLanguage(): ?string
    {
        $header = $this->server['HTTP_ACCEPT_LANGUAGE'] ?? null;
        if (!$header) {
            return null;
        }
        $langs = explode(',', $header);
        return $langs[0] ?? null;
    }

    /**
     * Get all languages from Accept-Language header.
     *
     * @return array<int, string> List of languages
     */
    public function getAllLanguages(): array
    {
        $header = $this->server['HTTP_ACCEPT_LANGUAGE'] ?? '';
        return array_map(
            fn($l) => trim(explode(';', $l)[0]),
            explode(',', $header)
        );
    }

    /**
     * Get the browser user agent string.
     *
     * @return string|null User agent or null
     */
    public function getUserAgent(): ?string
    {
        return $this->server['HTTP_USER_AGENT'] ?? null;
    }

    /**
     * Parse the user agent to get browser name and version.
     *
     * @return array{browser: string|null, version: string|null} Browser info
     */
    public function getBrowser(): array
    {
        $ua = $this->getUserAgent();
        if (!$ua) {
            return ['browser' => null, 'version' => null];
        }
        // Simple regexes for common browsers
        if (
            preg_match(
                '/(Edge|Edg|OPR|Chrome|Safari|Firefox|MSIE|Trident)\/([\d.]+)/i',
                $ua,
                $m
            )
        ) {
            $browser = $m[1] === 'OPR' ? 'Opera' : (
                $m[1] === 'Edg' ? 'Edge' : (
                    $m[1] === 'Trident' ? 'IE' : $m[1]
                )
            );
            return ['browser' => $browser, 'version' => $m[2]];
        }
        return ['browser' => null, 'version' => null];
    }

    /**
     * Parse the user agent to get OS name.
     *
     * @return string|null OS name or null
     */
    public function getOs(): ?string
    {
        $ua = $this->getUserAgent();
        if (!$ua) {
            return null;
        }
        if (preg_match('/Windows NT/i', $ua)) {
            return 'Windows';
        }
        if (preg_match('/Android/i', $ua)) {
            return 'Android';
        }
        if (preg_match('/iPhone|iPad|iPod/i', $ua)) {
            return 'iOS';
        }
        if (preg_match('/Macintosh|Mac OS X/i', $ua)) {
            return 'Mac OS';
        }
        if (preg_match('/Linux/i', $ua)) {
            return 'Linux';
        }
        return null;
    }

    /**
     * Try to detect device type from user agent.
     *
     * @return string|null Device type: mobile, tablet, desktop, or null
     */
    public function getDeviceType(): ?string
    {
        $ua = $this->getUserAgent();
        if (!$ua) {
            return null;
        }
        if (preg_match('/Mobile|Android|iPhone|iPod/i', $ua)) {
            return 'mobile';
        }
        if (preg_match('/iPad|Tablet/i', $ua)) {
            return 'tablet';
        }
        return 'desktop';
    }

    /**
     * Try to get screen resolution from custom headers (if set by frontend JS).
     *
     * @return array{width: int|null, height: int|null} Screen resolution
     */
    public function getResolution(): array
    {
        $width = $this->server['HTTP_X_SCREEN_WIDTH'] ?? null;
        $height = $this->server['HTTP_X_SCREEN_HEIGHT'] ?? null;
        return [
            'width' => $width ? (int)$width : null,
            'height' => $height ? (int)$height : null,
        ];
    }

    /**
     * Get all geoinfo and client info as an array.
     *
     * @param  array<int, string>|null $fields List of fields to return (default: all)
     *
     * @return array<string, mixed> Associative array of info
     */
    public function getGeoInfo(?array $fields = null): array
    {
        $all = [
            'country_code' => $this->getCountryCode(),
            'ip' => $this->getIp(),
            'preferred_language' => $this->getPreferredLanguage(),
            'all_languages' => $this->getAllLanguages(),
            'os' => $this->getOs(),
            'browser' => $this->getBrowser(),
            'device' => $this->getDeviceType(),
            'resolution' => $this->getResolution(),
        ];
        if ($fields === null) {
            return $all;
        }
        $result = [];
        foreach ($fields as $field) {
            if (array_key_exists($field, $all)) {
                $result[$field] = $all[$field];
            }
        }
        return $result;
    }

    /**
     * Get language for a given country code (or current visitor),
     * considering browser languages and available site languages.
     *
     * @param  string|null             $countryCode            Country code
     * @param  array<int, string>|null $availableSiteLanguages List of available site languages (eg ['en', 'fr', 'de'])
     *
     * @return string|null Language code or null
     */
    public function getLanguageForCountry(
        ?string $countryCode = null,
        ?array $availableSiteLanguages = null
    ): ?string {
        $countryCode = $countryCode ?? $this->getCountryCode();
        if (!$countryCode) {
            return null;
        }
        $langs = $this->countryToLanguage[strtoupper($countryCode)] ?? null;
        if (!$langs) {
            return null;
        }
        // Normalize to array
        $langs = is_array($langs) ? $langs : [$langs];
        if ($availableSiteLanguages) {
            // 1. Check preferred language
            $preferred = $this->getPreferredLanguage();
            if ($preferred) {
                $preferredShort = substr($preferred, 0, 2);
                if (
                    in_array($preferredShort, $langs, true)
                    && in_array($preferredShort, $availableSiteLanguages, true)
                ) {
                    return $preferredShort;
                }
            }
            // 2. Check all browser languages
            foreach ($this->getAllLanguages() as $browserLang) {
                $langCode = substr($browserLang, 0, 2);
                if (
                    in_array($langCode, $langs, true)
                    && in_array($langCode, $availableSiteLanguages, true)
                ) {
                    return $langCode;
                }
            }
            // 3. Fallback: first country language that is available
            foreach ($langs as $lang) {
                if (in_array($lang, $availableSiteLanguages, true)) {
                    return $lang;
                }
            }
            return null;
        }
        // If no availableSiteLanguages provided, fallback to first country language
        if (isset($langs[0])) {
            return $langs[0];
        }
        return null;
    }

    /**
     * Should set language (e.g. if no language cookie is set).
     *
     * @return bool True if language should be set
     */
    public function shouldSetLanguage(): bool
    {
        // Only set language if no language cookie is set
        $cookie = $this->server['HTTP_COOKIE'] ?? '';
        return empty($cookie) || !preg_match('/' . preg_quote($this->languageCookieName, '/') . '=/i', $cookie);
    }

    /**
     * Check if we're in a local development environment.
     *
     * @return bool True if local development detected
     */
    public function isLocalDevelopment(): bool
    {
        $ip = $this->getIp();
        $host = $this->server['HTTP_HOST'] ?? $this->server['SERVER_NAME'] ?? '';

        // Check for localhost, local IPs, or missing Cloudflare headers
        return $ip === null ||
               $ip === '127.0.0.1' ||
               $ip === '::1' ||
               str_starts_with($ip, '192.168.') ||
               str_starts_with($ip, '10.') ||
               str_starts_with($ip, '172.16.') ||
               str_contains($host, 'localhost') ||
               str_contains($host, '.local') ||
               !isset($this->server['HTTP_CF_IPCOUNTRY']);
    }

    /**
     * Create a Geolocation instance with simulated data for local development.
     *
     * @param string                                   $countryCode        Country code to simulate
     * @param array<string, string|array<int, string>> $countryToLanguage  Country to language mapping
     * @param string                                   $languageCookieName Language cookie name
     * @param array<string, mixed>                     $options            Additional simulation options
     *
     * @return Geolocation Geolocation instance with simulated data
     */
    public static function simulate(
        string $countryCode = 'US',
        array $countryToLanguage = [],
        string $languageCookieName = 'lang',
        array $options = []
    ): Geolocation {
        return GeolocationSimulator::create($countryCode, $countryToLanguage, $languageCookieName, $options);
    }
}
// @phpcs:enable Generic.Files.LineLength
