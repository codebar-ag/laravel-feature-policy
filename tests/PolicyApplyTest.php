<?php

use CodebarAg\LaravelFeaturePolicy\Directive;
use CodebarAg\LaravelFeaturePolicy\Exceptions\UnknownPermissionGroupException;
use CodebarAg\LaravelFeaturePolicy\Policies\Policy;
use CodebarAg\LaravelFeaturePolicy\Value;
use Illuminate\Http\Response;

it('formats directives as string', function () {
    $policy = new class extends Policy
    {
        public function configure(): void
        {
            $this->addDirective(Directive::CAMERA, Value::NONE);
        }
    };
    $policy->configure();

    expect((string) $policy)->toBe('camera=()');
})
    ->group('unit', 'feature-policy');

it('outputs empty string without directives', function () {
    $policy = new class extends Policy
    {
        public function configure(): void {}
    };
    $policy->configure();

    expect((string) $policy)->toBeEmpty();
})
    ->group('unit', 'feature-policy');

it('sets Permissions-Policy header via applyTo', function () {
    config(['feature-policy.reporting.enabled' => false]);

    $policy = new class extends Policy
    {
        public function configure(): void
        {
            $this->addDirective(Directive::CAMERA, Value::SELF);
        }
    };

    $response = new Response;

    expect($response->headers->has('Permissions-Policy'))->toBeFalse();

    $policy->applyTo($response);

    expect($response->headers->has('Permissions-Policy'))->toBeTrue()
        ->and($response->headers->get('Permissions-Policy'))->toBe('camera=self');
})
    ->group('unit', 'feature-policy');

it('addDirective mutates string representation', function () {
    $policy = new class extends Policy
    {
        public function configure(): void {}
    };

    expect((string) $policy)->toBeEmpty();

    $policy->addDirective(Directive::CAMERA, Value::SELF);

    expect((string) $policy)->toBe('camera=self');
})
    ->group('unit', 'feature-policy');

it('does not overwrite an existing Permissions-Policy header', function () {
    config(['feature-policy.reporting.enabled' => false]);

    $policy = new class extends Policy
    {
        public function configure(): void
        {
            $this->addDirective(Directive::CAMERA, Value::SELF);
        }
    };
    $policy->configure();

    $response = new Response;
    $response->headers->set('Permissions-Policy', 'existing=value');

    $policy->applyTo($response);

    expect($response->headers->get('Permissions-Policy'))->toBe('existing=value');
})
    ->group('unit', 'feature-policy');

it('does not overwrite an existing Permissions-Policy-Report-Only header in report-only mode', function () {
    config([
        'feature-policy.reporting.enabled' => true,
        'feature-policy.reporting.report_only' => true,
        'feature-policy.reporting.url' => 'https://example.test/report',
    ]);

    $policy = new class extends Policy
    {
        public function configure(): void
        {
            $this->addDirective(Directive::CAMERA, Value::ALL);
        }
    };
    $policy->configure();

    $response = new Response;
    $existingReportOnly = 'camera=(); report-to=other';
    $response->headers->set('Permissions-Policy-Report-Only', $existingReportOnly);

    $policy->applyTo($response);

    expect($response->headers->get('Permissions-Policy-Report-Only'))->toBe($existingReportOnly);
})
    ->group('unit', 'feature-policy');

it('throws when feature group type is unknown', function () {
    $policy = new class extends Policy
    {
        public function configure(): void
        {
            $invalidGroupType = new class {};
            $this->addDirective(Directive::CAMERA, Value::SELF, $invalidGroupType::class);
        }
    };

    $policy->configure();
})
    ->throws(UnknownPermissionGroupException::class)
    ->group('unit', 'feature-policy');
