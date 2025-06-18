<?php

/**
 * GeolocationMiddleware for Laravel adapter (PHP 8.3+).
 *
 * Handles geolocation and language detection for Laravel requests.
 *
 * @category Geolocation
 * @package  Rumenx\Geolocation\Adapters\Laravel
 * @author   Rumen Damyanov <contact@rumenx.com>
 * @license  https://opensource.org/licenses/MIT MIT License
 * @link     https://github.com/RumenDamyanov/php-geolocation
 */

namespace Rumenx\Geolocation\Adapters\Laravel;

use Closure;
use Illuminate\Http\Request;
use Rumenx\Geolocation\Geolocation;

/**
 * Class GeolocationMiddleware
 *
 * Middleware to set locale and share geo info in Laravel.
 */
class GeolocationMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Request $request The HTTP request
     * @param  Closure $next    The next middleware
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next): mixed
    {
        $geo = new Geolocation($_SERVER, config('geolocation.country_to_language'));
        $country = $geo->getCountryCode();
        $lang = $geo->getLanguageForCountry();
        $shouldSet = $geo->shouldSetLanguage();

        if ($shouldSet && $lang) {
            // Set app locale if not set by cookie
            app()->setLocale($lang);
        }

        // Optionally, share geo info with views
        view()->share('geo', $geo->getGeoInfo());

        return $next($request);
    }
}
