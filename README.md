# Set Permissions-Policy headers in a Laravel app

This package is strongly inspired by [Spaties](https://spatie.be) [laravel-csp](https://github.com/spatie/laravel-csp) package. Thanks to [Freek van der Herten](https://github.com/freekmurze) and [Thomas Verhelst](https://github.com/TVke) for creating such an awesome package and doing all the heavy lifting!

With Permissions-Policy you can control which web platform permissions to allow and disallow within your web applications. Permissions-Policy is a Security Header (like Content-Security-Policy) that is brand new. The list of things you can restrict isn't final yet, I'll add them in time when the specification evolves.

## Installation

You should install this package via composer:

```bash
$ composer require codebar-ag/laravel-feature-policy
```

Next, publish the config file:

```bash
$ php artisan vendor:publish --provider="CodebarAg\FeaturePolicy\FeaturePolicyServiceProvider" --tag="config"
```

The contents of the `config/feature-policy.php` file look like this:

```php
<?php

return [
    /*
     * A policy will determine which Permissions-Policy headers will be set.
     * A valid policy extends `CodebarAg\FeaturePolicy\Policies\Policy`
     */
    'policy' => CodebarAg\FeaturePolicy\Policies\Basic::class,

    /*
     * Feature-policy headers will only be added if this is set to true
     */
    'enabled' => env('FPH_ENABLED', true),
];
```

## Middleware

You can add Feature-Policy headers to all responses by registering `CodebarAg\FeaturePolicy\AddFeaturePolicyHeaders::class` in the HTTP kernel:

```php
// app/Http/Kernel.php

...

protected $middlewareGroups = [
    'web' => [
        ...
        \CodebarAg\FeaturePolicy\AddFeaturePolicyHeaders::class,
    ]
];
```

Alternatively you can add the middleware to a single route and route group:

```php
// in a routes file
Route::get('/home', 'HomeController')->middleware(CodebarAg\FeaturePolicy\AddFeaturePolicyHeaders::class);
```

You could even pass a policy as a parameter and override the policy specified in the config file:

```php
// in a routes file
Route::get('/home', 'HomeController')->middleware(CodebarAg\FeaturePolicy\AddFeaturePolicyHeaders::class . ':' . MyFeaturePolicy::class);
```

## Usage

This package allows you to define Permissions-Policy policies. A Feature-Policy policy determines which Permissions-Policy directives will be set in the headers of the response.

An example of a Permissions-Policy directive is `microphone`:

`Permissions-Policy: microphone=(self "https://spatie.be")`

In the above example by specifying `microphone` and allowing it for `self` makes the permission disabled for all origins except our own and https://spatie.be.

The full list of directives isn't final yet, but here are some of the things you have access to:

- accelerometer
- ambient-light-sensor
- autoplay
- camera
- encrypted-media
- fullscreen
- geolocation
- gyroscope
- magnetometer
- microphone
- midi
- payment
- picture-in-picture
- speaker
- usb
- vr

You can find the feature definitions at https://github.com/WICG/feature-policy/blob/master/features.md

You can add multiple policy options as an array or as a single string with space-separated options:

```php
// in a policy
...
    ->addDirective(Directive::CAMERA, [
        Value::SELF,
        'spatie.be',
    ])
    ->addDirective(Directive::GYROSCOPE, 'self spatie.be')
...
```

## Creating Policies

The `policy` key of the `feature-policy` config file is set to `CodebarAg\FeaturePolicy\Policies\Basic::class` by default, which allows your site to use a few of the available features. The class looks like this:

```php
<?php

namespace CodebarAg\FeaturePolicy\Policies;

use CodebarAg\FeaturePolicy\Value;
use CodebarAg\FeaturePolicy\Directive;

class Basic extends Policy
{
    public function configure()
    {
        $this->addDirective(Directive::GEOLOCATION, Value::SELF)
            ->addDirective(Directive::FULLSCREEN, Value::SELF);
    }
}
```

Let's say you're happy with allowing `geolocation` and `fullscreen` but also wanted to add `www.awesomesite.com` to gain access to this feature, then you can easily extend the class:

```php
<?php

namespace App\Services\FeaturePolicy\Policies;

use CodebarAg\FeaturePolicy\Directive;
use CodebarAg\FeaturePolicy\Policies\Basic;

class MyFeaturePolicy extends Basic
{
    public function configure()
    {
        parent::configure();

        $this->addDirective(Directive::GEOLOCATION, 'www.awesomesite.com')
            ->addDirective(Directive::FULLSCREEN, 'www.awesomesite.com');
    }
}
```

Don't forget to change the `policy` key in the `feature-policy` config file to the class name fo your policy (e.g. `App\Services\Policies\MyFeaturePolicy`).

## Testing

You can run all tests with:

```bash
$ composer test
```

## Changelog

Please see [CHANGELOG](https://github.com/codebar-ag/laravel-feature-policy/blob/master/CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](https://github.com/codebar-ag/laravel-feature-policy/blob/master/CONTRIBUTING.md) for details.

### Security

If you discover any security related issues please email helpdesk@codebar.ch instead of using the issue tracker.

## Credits

- [Christian Leo-Pernold](https://github.com/mazedlx/laravel-feature-policy)
- [Freek van der Herten](https://github.com/freekmurze)
- [Thomas Verhelst](https://github.com/TVke)
- [All Contributors](https://github.com/codebar-ag/laravel-feature-policy/contributors)

## Support

If you like this package please feel free to star it.

## License

The MIT License (MIT). Please see [LICENSE](https://github.com/codebar-ag/laravel-feature-policy/blob/master/LICENSE.md) for more information.
