<?php

use CodebarAg\LaravelFeaturePolicy\Exceptions\UnknownPermissionGroupException;

it('wraps unknown group', function () {
    $e = new UnknownPermissionGroupException('BadGroup');

    expect($e->getMessage())->toContain('BadGroup');
})->group('unit', 'feature-policy');
