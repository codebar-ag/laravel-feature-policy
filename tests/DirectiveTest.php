<?php

use CodebarAg\LaravelFeaturePolicy\Directive;
use CodebarAg\LaravelFeaturePolicy\Exceptions\UnsupportedPermissionException;
use CodebarAg\LaravelFeaturePolicy\Value;

$skippableDirectiveValues = [
    Directive::VR,
    Directive::XR,
    Directive::XR_SPATIAL_TRACKING,
    Directive::FLOC,
];

$directiveCases = [];
foreach ((new ReflectionClass(Directive::class))->getConstants() as $name => $value) {
    if (in_array($value, $skippableDirectiveValues, true)) {
        continue;
    }
    $directiveCases[] = [$name, $value];
}

dataset('feature_directives', $directiveCases);

it('can make a directive from name', function (string $constantName, string $value) {
    $directive = Directive::make($value);

    expect($directive->name())->toBe($value)
        ->and($directive->name())->toBe(constant(Directive::class.'::'.$constantName))
        ->and($directive->rules())->toBeEmpty();
})
    ->with('feature_directives')
    ->group('unit', 'feature-policy');

it('throws for unknown directive string', function () {
    Directive::make('invalid-directive');
})
    ->throws(UnsupportedPermissionException::class)
    ->group('unit', 'feature-policy');

it('can add a rule to a directive', function () {
    $directive = Directive::make(Directive::GEOLOCATION);
    expect($directive->rules())->toBeEmpty();

    $directive->addRule(Value::SELF);

    expect($directive->rules())->not->toBeEmpty()
        ->and($directive->rules())->toHaveCount(1)
        ->and($directive->rules()[0])->toBe(Value::SELF);
})
    ->group('unit', 'feature-policy');

it('uses xr spatial tracking name instead of deprecated vr alias', function () {
    $directive = Directive::make(Directive::XR_SPATIAL_TRACKING);
    expect($directive->name())->toBe(Directive::XR_SPATIAL_TRACKING);
})
    ->group('unit', 'feature-policy');

it('defaults floc to none', function () {
    $directive = Directive::make(Directive::FLOC);
    expect($directive->rules())->not->toBeEmpty()
        ->and($directive->rules())->toHaveCount(1)
        ->and($directive->rules()[0])->toBe(Value::NONE);
})
    ->group('unit', 'feature-policy');
