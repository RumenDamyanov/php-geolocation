# Security Policy

## Supported Versions

We release patches for security vulnerabilities. Which versions are eligible for receiving such patches depends on the CVSS v3.0 Rating:

| Version | PHP  | Supported          |
| ------- | ---- | ------------------ |
| 1.1.x   | 8.3+ | :white_check_mark: |
| 1.0.x   | 8.2+ | :white_check_mark: until PHP 8.2 EOL (31 Dec 2026) |

Runtime PHP must be a patched build. PHP2Shell ([CVE-2026-109618](https://www.cve.org/CVERecord?id=CVE-2026-109618), [CVE-2026-109619](https://www.cve.org/CVERecord?id=CVE-2026-109619)) is in the PHP engine, not in this library. Upgrade the interpreter to **8.5.12+**, **8.4.27+**, **8.3.36+**, or **8.2.35+**. There is no application-level workaround; see the [PHP Foundation advisory](https://thephp.foundation/blog/2026/10/21/todays-php-security-release-and-how-were-strengthening-php-security/) and [CISA alert](https://www.cisa.gov/news-events/alerts/2026/10/21/critical-vulnerabilities-php-require-immediate-patching).

`CF-IPCountry` and `CF-Connecting-IP` are client-controlled unless the origin only accepts connections from Cloudflare. Do not use them for access control on a directly reachable server.

## Reporting a Vulnerability

If you discover a security vulnerability within this package, please send an email to Rumen Damyanov at [security@rumenx.com](mailto:security@rumenx.com). All security vulnerabilities will be promptly addressed.

Please do not create GitHub issues for security vulnerabilities.

### What to include in your report

- A clear description of the vulnerability
- Steps to reproduce the issue
- Any potential impact of the vulnerability
- If possible, a suggested fix or patch

### Response Timeline

- **Acknowledgment**: We will acknowledge receipt of your vulnerability report within 48 hours.
- **Initial Assessment**: We will provide an initial assessment within 5 business days.
- **Resolution**: We aim to resolve critical vulnerabilities within 30 days.

### Disclosure Policy

We follow responsible disclosure practices:

1. We will work with you to understand and validate the vulnerability
2. We will develop and test a fix
3. We will release the fix and publicly disclose the vulnerability
4. We will credit you for the discovery (unless you prefer to remain anonymous)

Thank you for helping keep this project and its users safe!
