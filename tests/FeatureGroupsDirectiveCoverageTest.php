<?php

declare(strict_types=1);

use CodebarAg\LaravelFeaturePolicy\Directive;
use CodebarAg\LaravelFeaturePolicy\FeatureGroups\DefaultFeatureGroup;
use CodebarAg\LaravelFeaturePolicy\FeatureGroups\DirectiveContract;
use CodebarAg\LaravelFeaturePolicy\FeatureGroups\ProposedFeatureGroup;
use CodebarAg\LaravelFeaturePolicy\Value;

function assertDirectiveContractFullyExercised(DirectiveContract $directive): void
{
    expect($directive->name())->toBeString();
    expect($directive->name())->not->toBeEmpty();
    expect($directive->specificationName())->toBeString();
    expect($directive->specificationUrl())->toBeString();
    expect($directive->browserSupport())->toBeString();
    expect($directive->browserSupportUrl())->toBeString();
    expect($directive->note())->toBeString();
    expect($directive->isDeprecated())->toBeBool();
    expect($directive->rules())->toBeArray();
    $directive->addRule(Value::SELF);
}

it('exercises every default feature group directive arm', function () {
    foreach ((new ReflectionClass(Directive::class))->getConstants() as $value) {
        if (! is_string($value)) {
            continue;
        }

        $directive = DefaultFeatureGroup::directive($value);
        assertDirectiveContractFullyExercised($directive);
    }
})->group('unit', 'feature-policy');

it('exercises every proposed feature group directive arm', function () {
    config(['feature-policy.directives.proposal' => true]);

    foreach ([
        ProposedFeatureGroup::CLIPBOARD_READ,
        ProposedFeatureGroup::CLIPBOARD_WRITE,
        ProposedFeatureGroup::GAMEPAD,
        ProposedFeatureGroup::SHARED_AUTOFILL,
        ProposedFeatureGroup::SPEAKER_SELECTION,
    ] as $value) {
        assertDirectiveContractFullyExercised(ProposedFeatureGroup::directive($value));
    }
})->group('unit', 'feature-policy');
