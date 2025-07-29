<?php

/**
 * Example Symfony event listener for simulating Cloudflare geolocation in development.
 *
 * This listener should only be active in development environments.
 * Register it in your services.yaml with appropriate environment conditions.
 */

namespace App\EventListener;

use Rumenx\Geolocation\GeolocationSimulator;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class GeolocationDevelopmentListener
{
    private string $environment;
    private string $defaultCountry;

    public function __construct(string $environment, string $defaultCountry = 'US')
    {
        $this->environment = $environment;
        $this->defaultCountry = $defaultCountry;
    }

    /**
     * Handle the kernel request event and inject simulated headers in development.
     *
     * @param RequestEvent $event The request event
     *
     * @return void
     */
    public function onKernelRequest(RequestEvent $event): void
    {
        // Only simulate in development environment
        if ($this->environment !== 'dev') {
            return;
        }

        $request = $event->getRequest();

        // Get country from query parameter, session, or use default
        $simulateCountry = $request->query->get('simulate_country')
            ?? $request->getSession()?->get('simulate_country')
            ?? $this->defaultCountry;

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
        $session = $request->getSession();
        if ($session) {
            $session->set('simulate_country', $simulateCountry);
        }
    }
}

/*
Configuration in config/services.yaml:

services:
    App\EventListener\GeolocationDevelopmentListener:
        arguments:
            $environment: '%kernel.environment%'
            $defaultCountry: '%env(DEV_SIMULATE_COUNTRY)%'
        tags:
            - { name: kernel.event_listener, event: kernel.request, method: onKernelRequest, priority: 1000 }

Environment configuration in .env:

DEV_SIMULATE_COUNTRY=DE

Usage in a controller:

use Rumenx\Geolocation\Geolocation;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    public function index(Request $request): Response
    {
        $geo = new Geolocation(
            $request->server->all(),
            $this->getParameter('geolocation.country_to_language')
        );

        // This will now work in development with simulated data
        $country = $geo->getCountryCode();
        $lang = $geo->getLanguageForCountry(null, ['en', 'fr', 'de']);

        return $this->render('home.html.twig', [
            'geo' => $geo->getGeoInfo(),
            'country' => $country,
            'language' => $lang
        ]);
    }

    public function simulateCountry(string $country, Request $request): Response
    {
        $request->getSession()?->set('simulate_country', $country);
        return $this->redirectToRoute('home');
    }
}

Routes in config/routes.yaml (development only):

when@dev:
    simulate_country:
        path: /dev/simulate/{country}
        controller: App\Controller\HomeController::simulateCountry
        requirements:
            country: '[A-Z]{2}'
*/
