<?php

use CodebarAg\LaravelFeaturePolicy\Directive;
use CodebarAg\LaravelFeaturePolicy\FeatureGroups\DefaultFeatureGroup;
use CodebarAg\LaravelFeaturePolicy\Formatter\PolicyFormatter;
use CodebarAg\LaravelFeaturePolicy\Value;

it('formats directives as string', function () {
    config(['feature-policy.reporting.enabled' => false]);

    $directive = DefaultFeatureGroup::directive(Directive::GEOLOCATION);
    $directive->addRule(Value::SELF);

    $formatter = new PolicyFormatter([$directive]);

    expect((string) $formatter)->toContain('geolocation');
})->group('unit', 'feature-policy');
