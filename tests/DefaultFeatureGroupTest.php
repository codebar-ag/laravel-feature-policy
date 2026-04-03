<?php

use CodebarAg\LaravelFeaturePolicy\Directive;
use CodebarAg\LaravelFeaturePolicy\FeatureGroups\DefaultFeatureGroup;

it('resolves a default feature directive', function () {
    $d = DefaultFeatureGroup::directive(Directive::GEOLOCATION);

    expect($d->name())->toBe(Directive::GEOLOCATION);
})->group('unit', 'feature-policy');
