<?php

/**
 * GeolocationServiceProvider for Laravel adapter.
 *
 * Registers geolocation services and publishes config in Laravel.
 *
 * @category Geolocation
 * @package  Rumenx\Geolocation\Adapters\Laravel
 * @author   Rumen Damyanov <contact@rumenx.com>
 * @license  https://opensource.org/licenses/MIT MIT License
 * @link     https://github.com/RumenDamyanov/php-geolocation
 */

namespace Rumenx\Geolocation\Adapters\Laravel;

use Illuminate\Support\ServiceProvider;

/**
 * Class GeolocationServiceProvider
 *
 * Laravel service provider for geolocation integration.
 */
class GeolocationServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        // Publish config
        $this->publishes(
            [
            __DIR__ . '/config/geolocation.php' => config_path('geolocation.php'),
            ],
            'geolocation-config'
        );
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/config/geolocation.php',
            'geolocation'
        );
    }
}
