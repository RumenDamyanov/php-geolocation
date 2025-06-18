<?php

describe('Geolocation', function () {
    it('detects country code from cloudflare header', function () {
        $geo = new \Rumenx\Geolocation\Geolocation([
            'HTTP_CF_IPCOUNTRY' => 'DE',
        ]);
        expect($geo->getCountryCode())->toBe('DE');
    });

    it('returns null if country code header is missing', function () {
        $geo = new \Rumenx\Geolocation\Geolocation([]);
        expect($geo->getCountryCode())->toBeNull();
    });

    it('detects preferred language from accept-language header', function () {
        $geo = new \Rumenx\Geolocation\Geolocation([
            'HTTP_ACCEPT_LANGUAGE' => 'fr-FR,fr;q=0.9,en;q=0.8',
        ]);
        expect($geo->getPreferredLanguage())->toBe('fr-FR');
        expect($geo->getAllLanguages())->toBe(['fr-FR', 'fr', 'en']);
    });

    it('returns null if accept-language header is missing', function () {
        $geo = new \Rumenx\Geolocation\Geolocation([]);
        expect($geo->getPreferredLanguage())->toBeNull();
        expect($geo->getAllLanguages())->toBe(['']);
    });

    it('maps country to language (case-insensitive)', function () {
        $geo = new \Rumenx\Geolocation\Geolocation([
            'HTTP_CF_IPCOUNTRY' => 'at',
        ], [
            'DE' => 'de',
            'AT' => 'de',
            'FR' => 'fr',
        ]);
        expect($geo->getLanguageForCountry())->toBe('de');
    });

    it('returns null for unmapped country', function () {
        $geo = new \Rumenx\Geolocation\Geolocation([
            'HTTP_CF_IPCOUNTRY' => 'US',
        ], [
            'DE' => 'de',
        ]);
        expect($geo->getLanguageForCountry())->toBeNull();
    });

    it('shouldSetLanguage returns true if no lang cookie', function () {
        $geo = new \Rumenx\Geolocation\Geolocation([
            'HTTP_COOKIE' => '',
        ]);
        expect($geo->shouldSetLanguage())->toBeTrue();
    });

    it('shouldSetLanguage returns false if lang cookie is set', function () {
        $geo = new \Rumenx\Geolocation\Geolocation([
            'HTTP_COOKIE' => 'foo=bar; lang=de; baz=qux',
        ]);
        expect($geo->shouldSetLanguage())->toBeFalse();
    });

    it('shouldSetLanguage uses custom cookie name', function () {
        $geo = new \Rumenx\Geolocation\Geolocation([
            'HTTP_COOKIE' => 'foo=bar; mylang=de; baz=qux',
        ], [], 'mylang');
        expect($geo->shouldSetLanguage())->toBeFalse();
    });

    it('shouldSetLanguage returns true if custom cookie is not set', function () {
        $geo = new \Rumenx\Geolocation\Geolocation([
            'HTTP_COOKIE' => 'foo=bar; lang=de; baz=qux',
        ], [], 'mylang');
        expect($geo->shouldSetLanguage())->toBeTrue();
    });

    it('getGeoInfo returns all info', function () {
        $geo = new \Rumenx\Geolocation\Geolocation([
            'HTTP_CF_IPCOUNTRY' => 'DE',
            'HTTP_CF_CONNECTING_IP' => '1.2.3.4',
            'HTTP_ACCEPT_LANGUAGE' => 'de,en',
        ]);
        $info = $geo->getGeoInfo();
        expect($info['country_code'])->toBe('DE');
        expect($info['ip'])->toBe('1.2.3.4');
        expect($info['preferred_language'])->toBe('de');
        expect($info['all_languages'])->toBe(['de', 'en']);
    });

    it('getGeoInfo returns only requested fields', function () {
        $geo = new \Rumenx\Geolocation\Geolocation([
            'HTTP_CF_IPCOUNTRY' => 'DE',
            'HTTP_CF_CONNECTING_IP' => '1.2.3.4',
            'HTTP_ACCEPT_LANGUAGE' => 'de,en',
            'HTTP_USER_AGENT' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/114.0.0.0',
            'HTTP_X_SCREEN_WIDTH' => '1920',
            'HTTP_X_SCREEN_HEIGHT' => '1080',
        ]);
        $info = $geo->getGeoInfo(['country_code', 'ip']);
        expect($info)->toBe([
            'country_code' => 'DE',
            'ip' => '1.2.3.4',
        ]);
    });

    it('getGeoInfo returns all fields by default', function () {
        $geo = new \Rumenx\Geolocation\Geolocation([
            'HTTP_CF_IPCOUNTRY' => 'DE',
            'HTTP_CF_CONNECTING_IP' => '1.2.3.4',
            'HTTP_ACCEPT_LANGUAGE' => 'de,en',
            'HTTP_USER_AGENT' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/114.0.0.0',
            'HTTP_X_SCREEN_WIDTH' => '1920',
            'HTTP_X_SCREEN_HEIGHT' => '1080',
        ]);
        $info = $geo->getGeoInfo();
        expect($info['country_code'])->toBe('DE');
        expect($info['ip'])->toBe('1.2.3.4');
        expect($info['preferred_language'])->toBe('de');
        expect($info['all_languages'])->toBe(['de', 'en']);
        expect($info['os'])->toBe('Windows');
        expect($info['browser']['browser'])->toBe('Chrome');
        expect($info['device'])->toBe('desktop');
        expect($info['resolution'])->toBe(['width' => 1920, 'height' => 1080]);
    });

    it('works with empty server array', function () {
        $geo = new \Rumenx\Geolocation\Geolocation([]);
        expect($geo->getCountryCode())->toBeNull();
        expect($geo->getIp())->toBeNull();
        expect($geo->getPreferredLanguage())->toBeNull();
    });

    it('selects browser language for multi-language country if available', function () {
        $geo = new \Rumenx\Geolocation\Geolocation([
            'HTTP_CF_IPCOUNTRY' => 'CA',
            'HTTP_ACCEPT_LANGUAGE' => 'fr-CA,fr;q=0.9,en;q=0.8',
        ], [
            'CA' => ['en', 'fr'],
        ]);
        $lang = $geo->getLanguageForCountry(null, ['en', 'fr']);
        expect($lang)->toBe('fr');
    });

    it('selects fallback language for multi-language country if browser language not available', function () {
        $geo = new \Rumenx\Geolocation\Geolocation([
            'HTTP_CF_IPCOUNTRY' => 'CA',
            'HTTP_ACCEPT_LANGUAGE' => 'de-DE,de;q=0.9',
        ], [
            'CA' => ['en', 'fr'],
        ]);
        $lang = $geo->getLanguageForCountry(null, ['en', 'fr']);
        expect($lang)->toBe('en');
    });

    it('returns null if no country language matches available site languages', function () {
        $geo = new \Rumenx\Geolocation\Geolocation([
            'HTTP_CF_IPCOUNTRY' => 'CA',
            'HTTP_ACCEPT_LANGUAGE' => 'de-DE,de;q=0.9',
        ], [
            'CA' => ['en', 'fr'],
        ]);
        $lang = $geo->getLanguageForCountry(null, ['de']);
        expect($lang)->toBeNull();
    });

    it('getLanguageForCountry returns null if country code is missing', function () {
        $geo = new \Rumenx\Geolocation\Geolocation([]);
        expect($geo->getLanguageForCountry())->toBeNull();
    });

    it('getLanguageForCountry returns null if country maps to empty array', function () {
        $geo = new \Rumenx\Geolocation\Geolocation([
            'HTTP_CF_IPCOUNTRY' => 'ZZ',
        ], [
            'ZZ' => [],
        ]);
        expect($geo->getLanguageForCountry())->toBeNull();
    });

    it('getLanguageForCountry returns null if no available site languages match and langs is empty', function () {
        $geo = new \Rumenx\Geolocation\Geolocation([
            'HTTP_CF_IPCOUNTRY' => 'ZZ',
        ], [
            'ZZ' => [],
        ]);
        expect($geo->getLanguageForCountry('ZZ', []))->toBeNull();
    });

    it('getLanguageForCountry returns null if langs is empty and no availableSiteLanguages', function () {
        $geo = new \Rumenx\Geolocation\Geolocation([
            'HTTP_CF_IPCOUNTRY' => 'ZZ',
        ], [
            'ZZ' => [],
        ]);
        expect($geo->getLanguageForCountry('ZZ'))->toBeNull();
    });

    it('detects all supported OS', function () {
        $oses = [
            ['Mozilla/5.0 (Windows NT 10.0; Win64; x64)', 'Windows'],
            ['Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7)', 'Mac OS'],
            ['Mozilla/5.0 (X11; Linux x86_64)', 'Linux'],
            ['Mozilla/5.0 (Linux; Android 10)', 'Android'],
            ['Mozilla/5.0 (iPhone; CPU iPhone OS 13_5_1 like Mac OS X)', 'iOS'],
            ['Mozilla/5.0 (Unknown)', null],
        ];
        foreach ($oses as [$ua, $expected]) {
            $geo = new \Rumenx\Geolocation\Geolocation(['HTTP_USER_AGENT' => $ua]);
            expect($geo->getOs())->toBe($expected);
        }
    });

    it('detects all supported device types', function () {
        $devices = [
            ['Mozilla/5.0 (iPhone; CPU iPhone OS 13_5_1 like Mac OS X)', 'mobile'],
            ['Mozilla/5.0 (Linux; Android 10)', 'mobile'],
            ['Mozilla/5.0 (iPad; CPU OS 13_5_1 like Mac OS X)', 'tablet'],
            ['Mozilla/5.0 (Windows NT 10.0; Win64; x64)', 'desktop'],
            [null, null],
        ];
        foreach ($devices as [$ua, $expected]) {
            $geo = new \Rumenx\Geolocation\Geolocation($ua ? ['HTTP_USER_AGENT' => $ua] : []);
            expect($geo->getDeviceType())->toBe($expected);
        }
    });

    it('detects all supported browsers', function () {
        $browsers = [
            ['Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/114.0.0.0', 'Chrome'],
            ['Mozilla/5.0 (Windows NT 10.0; Win64; x64) Firefox/89.0', 'Firefox'],
            ['Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) Safari/605.1.15', 'Safari'],
            ['Mozilla/5.0 (Windows NT 10.0; Win64; x64) Edg/90.0.818.56', 'Edge'],
            ['Mozilla/5.0 (Windows NT 10.0; Win64; x64) OPR/76.0.4017.177', 'Opera'],
            ['Mozilla/5.0 (Windows NT 10.0; Win64; x64) MSIE/10.0', 'MSIE'],
            ['Mozilla/5.0 (Windows NT 10.0; Win64; x64) Trident/7.0', 'IE'],
            ['Mozilla/5.0 (Unknown)', null],
            [null, null],
        ];
        foreach ($browsers as [$ua, $expected]) {
            $geo = new \Rumenx\Geolocation\Geolocation($ua ? ['HTTP_USER_AGENT' => $ua] : []);
            $browser = $geo->getBrowser();
            expect($browser['browser'])->toBe($expected);
        }
    });

    it('detects screen resolution from headers', function () {
        $geo = new \Rumenx\Geolocation\Geolocation([
            'HTTP_X_SCREEN_WIDTH' => '1920',
            'HTTP_X_SCREEN_HEIGHT' => '1080',
        ]);
        expect($geo->getResolution())->toBe(['width' => 1920, 'height' => 1080]);
    });

    it('returns nulls for missing screen resolution headers', function () {
        $geo = new \Rumenx\Geolocation\Geolocation([]);
        expect($geo->getResolution())->toBe(['width' => null, 'height' => null]);
    });

    it('getLanguageForCountry returns browser language if it matches langs and availableSiteLanguages', function () {
        $geo = new \Rumenx\Geolocation\Geolocation([
            'HTTP_CF_IPCOUNTRY' => 'DE',
            'HTTP_ACCEPT_LANGUAGE' => 'fr-FR,de;q=0.9,en;q=0.8',
        ], [
            'DE' => ['de', 'fr'],
        ]);
        // Preferred is fr, but not in availableSiteLanguages, browserLang de is
        expect($geo->getLanguageForCountry('DE', ['de', 'en']))->toBe('de');
    });

    it('getLanguageForCountry returns first country language if no availableSiteLanguages', function () {
        $geo = new \Rumenx\Geolocation\Geolocation([
            'HTTP_CF_IPCOUNTRY' => 'FR',
        ], [
            'FR' => ['fr', 'en'],
        ]);
        expect($geo->getLanguageForCountry('FR'))->toBe('fr');
    });

    it('getLanguageForCountry returns first country language if mapping is a string and no availableSiteLanguages', function () {
        $geo = new \Rumenx\Geolocation\Geolocation([
            'HTTP_CF_IPCOUNTRY' => 'BG',
        ], [
            'BG' => 'bg',
        ]);
        expect($geo->getLanguageForCountry('BG'))->toBe('bg');
    });

    it('getLanguageForCountry returns null if country maps to empty array and availableSiteLanguages is set', function () {
        $geo = new \Rumenx\Geolocation\Geolocation([
            'HTTP_CF_IPCOUNTRY' => 'ZZ',
        ], [
            'ZZ' => [],
        ]);
        expect($geo->getLanguageForCountry('ZZ', ['en', 'fr']))->toBeNull();
    });

    it('getLanguageForCountry returns null if mapping is empty string', function () {
        $geo = new \Rumenx\Geolocation\Geolocation([
            'HTTP_CF_IPCOUNTRY' => 'ZZ',
        ], [
            'ZZ' => '',
        ]);
        expect($geo->getLanguageForCountry('ZZ'))->toBeNull();
    });

    it('getLanguageForCountry returns null for country with empty array mapping and no availableSiteLanguages', function () {
        $geo = new \Rumenx\Geolocation\Geolocation([
            'HTTP_CF_IPCOUNTRY' => 'XY',
        ], [
            'XY' => [],
        ]);
        expect($geo->getLanguageForCountry('XY'))->toBeNull();
    });

    it('getLanguageForCountry hits final return null for empty array mapping and no availableSiteLanguages', function () {
        $geo = new \Rumenx\Geolocation\Geolocation([
            'HTTP_CF_IPCOUNTRY' => 'ZZ',
        ], [
            'ZZ' => [],
        ]);
        expect($geo->getLanguageForCountry('ZZ'))->toBeNull();
    });

    it('getLanguageForCountry hits final return null for lower-case country code and empty array mapping', function () {
        $geo = new \Rumenx\Geolocation\Geolocation([
            'HTTP_CF_IPCOUNTRY' => 'zz',
        ], [
            'ZZ' => [],
        ]);
        expect($geo->getLanguageForCountry('zz'))->toBeNull();
    });

    it('getLanguageForCountry hits final return null for mapping with no 0th index', function () {
        $geo = new \Rumenx\Geolocation\Geolocation([
            'HTTP_CF_IPCOUNTRY' => 'ZZ',
        ], [
            'ZZ' => [1 => 'de'], // No 0th index
        ]);
        expect($geo->getLanguageForCountry('ZZ'))->toBeNull();
    });
});
