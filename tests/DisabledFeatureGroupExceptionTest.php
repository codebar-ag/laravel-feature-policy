<?php

use CodebarAg\LaravelFeaturePolicy\Exceptions\DisabledFeatureGroupException;

it('carries directive name', function () {
    $e = new DisabledFeatureGroupException('gamepad');

    expect($e->getMessage())->toContain('gamepad');
})->group('unit', 'feature-policy');
