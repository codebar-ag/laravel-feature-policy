<img src="https://banners.beyondco.de/Laravel%20Feature%20Policy.png?theme=light&packageManager=composer+require&packageName=codebar-ag%2Flaravel-feature-policy&pattern=circuitBoard&style=style_2&description=Permissions-Policy+header+builder+and+middleware+for+Laravel.&md=1&showWatermark=0&fontSize=150px&images=shield&widths=500&heights=500">

[![Latest Version on Packagist](https://img.shields.io/packagist/v/codebar-ag/laravel-feature-policy.svg?style=flat-square)](https://packagist.org/packages/codebar-ag/laravel-feature-policy)
[![Total Downloads](https://img.shields.io/packagist/dt/codebar-ag/laravel-feature-policy.svg?style=flat-square)](https://packagist.org/packages/codebar-ag/laravel-feature-policy)
[![GitHub Tests](https://github.com/codebar-ag/laravel-feature-policy/actions/workflows/tests.yml/badge.svg?branch=main)](https://github.com/codebar-ag/laravel-feature-policy/actions/workflows/tests.yml)
[![GitHub Code Style](https://github.com/codebar-ag/laravel-feature-policy/actions/workflows/fix-php-code-style-issues.yml/badge.svg?branch=main)](https://github.com/codebar-ag/laravel-feature-policy/actions/workflows/fix-php-code-style-issues.yml)
[![PHPStan](https://github.com/codebar-ag/laravel-feature-policy/actions/workflows/phpstan.yml/badge.svg)](https://github.com/codebar-ag/laravel-feature-policy/actions/workflows/phpstan.yml)
[![Dependency Review](https://github.com/codebar-ag/laravel-feature-policy/actions/workflows/dependency-review.yml/badge.svg)](https://github.com/codebar-ag/laravel-feature-policy/actions/workflows/dependency-review.yml)
[![Coverage](https://github.com/codebar-ag/laravel-feature-policy/actions/workflows/coverage.yml/badge.svg?branch=main)](https://github.com/codebar-ag/laravel-feature-policy/actions/workflows/coverage.yml)

Build and apply [`Permissions-Policy`](https://developer.mozilla.org/en-US/docs/Web/HTTP/Reference/Headers/Permissions-Policy) (formerly Feature-Policy) headers in Laravel applications.

## Requirements

- PHP 8.3, 8.4, or 8.5
- Laravel 13

## Installation

```bash
composer require codebar-ag/laravel-feature-policy
```

Publish configuration (optional):

```bash
php artisan vendor:publish --tag=laravel-feature-policy-config
```

## Configuration

The published config file is `config/feature-policy.php`. You can also rely on environment variables:

| Env | Config key | Default | Purpose |
| --- | --- | --- | --- |
| `FPH_ENABLED` | `enabled` | `true` | Master switch; when false, middleware does not apply policy headers. |
| — | `policy` | `null` | Fully qualified class name of your policy (must extend `CodebarAg\LaravelFeaturePolicy\Policies\Policy`). |
| `FPH_PROPOSAL_ENABLED` | `directives.proposal` | `false` | Enable [proposed](https://github.com/w3c/webappsec-permissions-policy/blob/main/features.md) directive group. |
| `FPH_EXPERIMENTAL_ENABLED` | `directives.experimental` | `false` | Enable experimental directive handling. |
| `FPH_REPORTING_ENABLED` | `reporting.enabled` | `false` | Add `Reporting-Endpoints` and related reporting metadata. |
| `FPH_REPORT_ONLY` | `reporting.report_only` | `false` | When reporting is on, also emit `Permissions-Policy-Report-Only`. |
| `FPH_REPORTING_URL` | `reporting.url` | *(see config)* | Endpoint URL for violation reports. |

Implement a policy class with a `configure()` method that calls `addDirective()` (see package tests and `Policies\Policy`).

## Middleware

Register the middleware on your `web` stack (or another group), for example in `bootstrap/app.php`:

```php
use CodebarAg\LaravelFeaturePolicy\AddFeaturePolicyHeaders;

$middleware->web(append: [
    AddFeaturePolicyHeaders::class,
]);
```

You may pass a specific policy class as a middleware parameter:

```php
Route::get('/admin', AdminController::class)
    ->middleware(AddFeaturePolicyHeaders::class.':'.AdminPermissionsPolicy::class);
```

## Quality checks

Run Laravel Pint in test mode:

```bash
composer lint
```

Run static analysis (PHPStan + Larastan):

```bash
composer analyse
```

Run the test suite:

```bash
composer test
```

Run tests with code coverage and a 100% minimum (requires the PCOV or Xdebug PHP extension):

```bash
composer test-coverage
```

Run lint, analysis, and tests together:

```bash
composer quality
```

## Credits

This package was initially inspired by [mazedlx/laravel-feature-policy](https://github.com/mazedlx/laravel-feature-policy) (MIT). The implementation has since been significantly adapted for Laravel 13 and is maintained independently by [codebar-ag](https://github.com/codebar-ag).

## License

MIT
