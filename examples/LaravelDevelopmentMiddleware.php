<?php

/**
 * Example Laravel middleware for simulating Cloudflare geolocation in development.
 *
 * This middleware should only be used in development environments.
 * Add it to your app/Http/Middleware/ directory and register it in your Kernel.php
 * for development routes or globally with environment checks.
 */

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Rumenx\Geolocation\GeolocationSimulator;

class GeolocationDevelopmentMiddleware
{
    /**
     * Handle an incoming request and inject simulated Cloudflare headers in development.
     *
     * @param  Request $request The HTTP request
     * @param  Closure $next    The next middleware
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next): mixed
    {
        // Only simulate in development environment
        if (app()->environment('local', 'development')) {
            // Get country from request parameter, session, or use default
            $simulateCountry = $request->get('simulate_country')
                ?? session('simulate_country')
                ?? config('app.dev_simulate_country', 'US');

            // Generate fake Cloudflare headers
            $fakeHeaders = GeolocationSimulator::fakeCloudflareHeaders($simulateCountry, [
                'server_name' => $request->getHost(),
                'https' => $request->isSecure() ? 'on' : ''
            ]);

            // Merge fake headers into the request
            foreach ($fakeHeaders as $key => $value) {
                $request->server->set($key, $value);
            }

            // Store the simulated country in session for consistency
            session(['simulate_country' => $simulateCountry]);
        }

        return $next($request);
    }
}

/*
Usage in routes/web.php:

Route::middleware(['geolocation.dev'])->group(function () {
    Route::get('/', [HomeController::class, 'index']);
    Route::get('/test-country/{country}', function ($country) {
        session(['simulate_country' => $country]);
        return redirect('/');
    });
});

Usage in a controller:

use Rumenx\Geolocation\Geolocation;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $geo = new Geolocation(
            $request->server->all(),
            config('geolocation.country_to_language', [])
        );

        // This will now work in development with simulated data
        $country = $geo->getCountryCode();
        $lang = $geo->getLanguageForCountry(null, ['en', 'fr', 'de']);

        return view('home', compact('geo', 'country', 'lang'));
    }
}

Configuration in config/app.php:

'dev_simulate_country' => env('DEV_SIMULATE_COUNTRY', 'US'),

Environment variable in .env:

DEV_SIMULATE_COUNTRY=CA
*/
