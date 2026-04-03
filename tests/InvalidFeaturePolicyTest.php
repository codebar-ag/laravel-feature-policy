<?php

use CodebarAg\LaravelFeaturePolicy\Exceptions\InvalidFeaturePolicy;

it('creates from object', function () {
    $e = InvalidFeaturePolicy::create(new stdClass);

    expect($e)->toBeInstanceOf(InvalidFeaturePolicy::class);
})->group('unit', 'feature-policy');
