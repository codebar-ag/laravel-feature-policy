<?php

use CodebarAg\LaravelFeaturePolicy\Value;

it('exposes policy value constants', function () {
    expect(Value::ALL)->toBe('*')
        ->and(Value::SELF)->toBe('self')
        ->and(Value::NONE)->toBe('()');
})->group('unit', 'feature-policy');
