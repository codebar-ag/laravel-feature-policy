<?php

use CodebarAg\LaravelFeaturePolicy\Exceptions\InvalidFeaturePolicy;
use CodebarAg\LaravelFeaturePolicy\PolicyFactory as FeaturePolicyFactory;
use CodebarAg\LaravelFeaturePolicy\Tests\Fixtures\SamplePermissionsPolicy;

it('basic policy factory create', function () {
    $policy = FeaturePolicyFactory::create(SamplePermissionsPolicy::class);
    expect($policy)->toBeInstanceOf(SamplePermissionsPolicy::class);
})
    ->group('unit', 'policies', 'feature-policy');

it('policy factory rejects classes that do not extend Policy', function () {
    $notPolicy = new class {};
    FeaturePolicyFactory::create($notPolicy::class);
})
    ->throws(InvalidFeaturePolicy::class)
    ->group('unit', 'policies', 'feature-policy');
