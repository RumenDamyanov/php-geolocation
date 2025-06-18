<?php

/**
 * GeolocationListener for Symfony adapter (PHP 8.3+).
 *
 * Listens to kernel request events and sets locale and geo info.
 *
 * @category Geolocation
 * @package  Rumenx\Geolocation\Adapters\Symfony
 * @author   Rumen Damyanov <contact@rumenx.com>
 * @license  https://opensource.org/licenses/MIT MIT License
 * @link     https://github.com/RumenDamyanov/php-geolocation
 * @version  GIT: <git_id>
 */

namespace Rumenx\Geolocation\Adapters\Symfony;

use Rumenx\Geolocation\Geolocation;
use Symfony\Component\HttpKernel\Event\RequestEvent;

/**
 * Class GeolocationListener
 *
 * Handles geolocation and language detection for Symfony requests.
 *
 * @category Geolocation
 * @package  Rumenx\Geolocation\Adapters\Symfony
 * @author   Rumen Damyanov <contact@rumenx.com>
 * @license  https://opensource.org/licenses/MIT MIT License
 * @link     https://github.com/RumenDamyanov/php-geolocation
 */
class GeolocationListener
{
    /**
     * Country code to language mapping.
     *
     * @var array<string, string|array<int, string>>
     */
    private array $countryToLanguage;

    /**
     * Constructor.
     *
     * @param array<string, string|array<int, string>> $countryToLanguage Country code to language mapping
     */
    public function __construct(array $countryToLanguage = [])
    {
        $this->countryToLanguage = $countryToLanguage;
    }

    /**
     * Handles the kernel request event.
     *
     * @param RequestEvent $event The request event
     *
     * @return void
     */
    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        $geo = new Geolocation($request->server->all(), $this->countryToLanguage);
        $lang = $geo->getLanguageForCountry();
        if ($geo->shouldSetLanguage() && $lang) {
            $request->setLocale($lang);
        }
        // Optionally, set as request attribute
        $request->attributes->set('geo', $geo->getGeoInfo());
    }
}
