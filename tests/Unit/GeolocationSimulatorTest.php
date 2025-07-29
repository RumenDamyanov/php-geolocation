<?php

use Rumenx\Geolocation\Geolocation;
use Rumenx\Geolocation\GeolocationSimulator;

describe('GeolocationSimulator', function () {
    it('can generate fake Cloudflare headers', function () {
        $headers = GeolocationSimulator::fakeCloudflareHeaders('DE');

        expect($headers)->toHaveKey('HTTP_CF_IPCOUNTRY', 'DE');
        expect($headers)->toHaveKey('HTTP_CF_CONNECTING_IP');
        expect($headers)->toHaveKey('HTTP_CF_RAY');
        expect($headers)->toHaveKey('HTTP_ACCEPT_LANGUAGE');
        expect($headers['HTTP_ACCEPT_LANGUAGE'])->toContain('de');
    });

    it('can create geolocation instance with simulated data', function () {
        $geo = GeolocationSimulator::create('CA', [
            'CA' => ['en', 'fr']
        ]);

        expect($geo->getCountryCode())->toBe('CA');
        expect($geo->getIp())->not->toBeNull();
        expect($geo->getPreferredLanguage())->toContain('en');
    });

    it('provides available countries', function () {
        $countries = GeolocationSimulator::getAvailableCountries();

        expect($countries)->toContain('US');
        expect($countries)->toContain('CA');
        expect($countries)->toContain('DE');
        expect($countries)->toBeArray();
    });

    it('can generate random country', function () {
        $country = GeolocationSimulator::randomCountry();
        $availableCountries = GeolocationSimulator::getAvailableCountries();

        expect($availableCountries)->toContain($country);
    });

    it('can add custom country data', function () {
        GeolocationSimulator::addCountryData('XX', [
            'country' => 'XX',
            'ip_ranges' => ['192.168.99.'],
            'languages' => ['xx-XX', 'xx'],
            'timezone' => 'UTC'
        ]);

        $headers = GeolocationSimulator::fakeCloudflareHeaders('XX');
        expect($headers['HTTP_CF_IPCOUNTRY'])->toBe('XX');
        expect($headers['HTTP_ACCEPT_LANGUAGE'])->toContain('xx');
    });

    it('generates fake user agents', function () {
        $userAgent = GeolocationSimulator::generateFakeUserAgent();

        expect($userAgent)->toBeString();
        expect($userAgent)->toContain('Mozilla');
    });

    it('can simulate complete server array', function () {
        $server = GeolocationSimulator::simulateServer('FR', [
            'server_name' => 'test.local'
        ]);

        expect($server)->toHaveKey('HTTP_CF_IPCOUNTRY', 'FR');
        expect($server)->toHaveKey('SERVER_NAME', 'test.local');
        expect($server['HTTP_ACCEPT_LANGUAGE'])->toContain('fr');
    });
});

describe('Geolocation Local Development', function () {
    it('can detect local development environment', function () {
        // Test with localhost
        $geo = new Geolocation([
            'HTTP_HOST' => 'localhost:8080',
            'REMOTE_ADDR' => '127.0.0.1'
        ]);
        expect($geo->isLocalDevelopment())->toBeTrue();

        // Test with local IP
        $geo = new Geolocation([
            'REMOTE_ADDR' => '192.168.1.100'
        ]);
        expect($geo->isLocalDevelopment())->toBeTrue();

        // Test without Cloudflare headers
        $geo = new Geolocation([
            'REMOTE_ADDR' => '203.0.113.1'
        ]);
        expect($geo->isLocalDevelopment())->toBeTrue();

        // Test with Cloudflare headers (production)
        $geo = new Geolocation([
            'HTTP_CF_IPCOUNTRY' => 'US',
            'HTTP_CF_CONNECTING_IP' => '203.0.113.1',
            'REMOTE_ADDR' => '203.0.113.1'
        ]);
        expect($geo->isLocalDevelopment())->toBeFalse();
    });

    it('can create simulated instance via static method', function () {
        $geo = Geolocation::simulate('JP', [
            'JP' => ['ja', 'en']
        ]);

        expect($geo->getCountryCode())->toBe('JP');
        expect($geo->getIp())->not->toBeNull();
        expect($geo->getPreferredLanguage())->toContain('ja');
    });

    it('respects custom options in simulation', function () {
        $geo = Geolocation::simulate('DE', [], 'custom_lang', [
            'user_agent' => 'Custom Test Agent',
            'server_name' => 'dev.example.com'
        ]);

        expect($geo->getCountryCode())->toBe('DE');
        expect($geo->getUserAgent())->toBe('Custom Test Agent');
    });
});
