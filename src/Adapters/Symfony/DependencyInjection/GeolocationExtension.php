<?php

/**
 * GeolocationExtension for Symfony adapter.
 *
 * Loads and manages geolocation bundle configuration.
 *
 * @category Geolocation
 * @package  Rumenx\Geolocation\Adapters\Symfony
 * @author   Rumen Damyanov <contact@rumenx.com>
 * @license  https://opensource.org/licenses/MIT MIT License
 * @link     https://github.com/RumenDamyanov/php-geolocation
 * @version  PHP 8.1+
 */

namespace Rumenx\Geolocation\Adapters\Symfony\DependencyInjection;

/**
 * Class GeolocationExtension
 *
 * Handles loading of geolocation configuration for Symfony.
 *
 * @category Geolocation
 * @package  Rumenx\Geolocation\Adapters\Symfony
 * @author   Rumen Damyanov <contact@rumenx.com>
 * @license  https://opensource.org/licenses/MIT MIT License
 * @link     https://github.com/RumenDamyanov/php-geolocation
 */
class GeolocationExtension
{
    /**
     * Loads configuration.
     *
     * @param  array<int, array<string, mixed>> $configs   The configuration arrays
     * @param  ContainerBuilder                 $container The container
     * @return void
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        // Optionally load config and set parameters/services
    }
}
