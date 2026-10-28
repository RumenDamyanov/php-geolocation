# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [1.1.0] - 2026-10-28

### Security

- Accept only a two-letter Cloudflare country code, so a spoofed `CF-IPCountry` value cannot carry markup.
- Use `CF-Connecting-IP` only when it is a valid IP address, and otherwise fall back to `REMOTE_ADDR`.
- Document that PHP2Shell ([CVE-2026-109618](https://www.cve.org/CVERecord?id=CVE-2026-109618), [CVE-2026-109619](https://www.cve.org/CVERecord?id=CVE-2026-109619)) is in the PHP engine: this package cannot patch it. Run PHP **8.3.36+**, **8.4.27+**, or **8.5.12+** ([PHP Foundation advisory](https://thephp.foundation/blog/2026/10/21/todays-php-security-release-and-how-were-strengthening-php-security/), [CISA alert](https://www.cisa.gov/news-events/alerts/2026/10/21/critical-vulnerabilities-php-require-immediate-patching)). Cloudflare country and IP headers are trustworthy only when the origin accepts traffic from Cloudflare.

### Changed

- Require PHP **^8.3**. The documentation already required 8.3, but Composer still allowed 8.2. PHP 8.2 reaches upstream end of life on 31 December 2026; stay on 1.0.x if you must remain on 8.2 (and use **8.2.35+** until then).
- CI tests PHP 8.3, 8.4, and 8.5. Coverage is collected on 8.4 only.
- Treat the full `172.16.0.0/12` private range, and other non-public addresses, as local development.
- Strip `Accept-Language` quality values and return an empty language list when the header is missing.
- Detect Edge when its user agent also contains Chrome.

### Fixed

- Remove the duplicate `homepage` and `support` keys in `composer.json`.

## [1.0.0] - 2025-01-XX

### Added
- Initial stable release
- Core geolocation functionality
- Laravel and Symfony framework adapters
- Complete documentation and examples
- Full test coverage
- CI/CD pipeline
- Security policy and contributing guidelines
