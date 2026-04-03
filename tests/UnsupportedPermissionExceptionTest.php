<?php

use CodebarAg\LaravelFeaturePolicy\Exceptions\UnsupportedPermissionException;

it('wraps unknown directive', function () {
    $e = new UnsupportedPermissionException('unknown-directive');

    expect($e->getMessage())->toContain('unknown-directive');
})->group('unit', 'feature-policy');
