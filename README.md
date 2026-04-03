# Laravel Feature Policy

Build and apply [`Permissions-Policy`](https://developer.mozilla.org/en-US/docs/Web/HTTP/Reference/Headers/Permissions-Policy) (formerly Feature-Policy) headers in Laravel applications.

## Installation

```bash
composer require codebar-ag/laravel-feature-policy
```

Publish configuration (optional):

```bash
php artisan vendor:publish --tag=laravel-feature-policy-config
```

Register the middleware on your `web` stack (or another group), for example in `bootstrap/app.php`:

```php
use CodebarAg\LaravelFeaturePolicy\AddFeaturePolicyHeaders;

$middleware->web(append: [
    AddFeaturePolicyHeaders::class,
]);
```

Set `feature-policy.policy` in config to a class that extends `CodebarAg\LaravelFeaturePolicy\Policies\Policy` and implements `configure()`.

## License

MIT
