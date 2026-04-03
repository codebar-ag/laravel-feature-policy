<?php

use CodebarAg\LaravelFeaturePolicy\AddFeaturePolicyHeaders;
use CodebarAg\LaravelFeaturePolicy\Directive;
use CodebarAg\LaravelFeaturePolicy\Policies\Policy;
use CodebarAg\LaravelFeaturePolicy\Value;
use CodebarAg\LaravelFeaturePolicy\Tests\Fixtures\SamplePermissionsPolicy;
use Illuminate\Support\Facades\Route;

beforeEach(function () {
    config([
        'feature-policy.enabled' => true,
        'feature-policy.policy' => SamplePermissionsPolicy::class,
        'feature-policy.reporting.enabled' => false,
        'feature-policy.reporting.report_only' => false,
        'feature-policy.directives.proposal' => false,
    ]);

    Route::middleware(['web', AddFeaturePolicyHeaders::class])
        ->get('/__fp_headers_route', fn () => 'ok');
});

it('sets default Permissions-Policy from app policy', function () {
    $this->get('/__fp_headers_route')
        ->assertSuccessful()
        ->assertHeader('Permissions-Policy', 'geolocation=self,fullscreen=self');
})
    ->group('unit', 'feature-policy');

it('does not set headers when feature policy is disabled', function () {
    config(['feature-policy.enabled' => false]);

    $this->get('/__fp_headers_route')
        ->assertHeaderMissing('Permissions-Policy');
})
    ->group('unit', 'feature-policy');

it('accepts multiple values for the same directive', function () {
    $policy = new class extends Policy
    {
        public function configure(): void
        {
            $this->addDirective(Directive::CAMERA, 'src-1')
                ->addDirective(Directive::CAMERA, 'src-2')
                ->addDirective(Directive::FULLSCREEN, 'src-3')
                ->addDirective(Directive::FULLSCREEN, 'src-4');
        }
    };

    config(['feature-policy.policy' => $policy::class]);

    $this->get('/__fp_headers_route')
        ->assertHeader('Permissions-Policy', 'camera=("src-1" "src-2"),fullscreen=("src-3" "src-4")');
})
    ->group('unit', 'feature-policy');

it('can add multiple values for the same directive from an array', function () {
    $policy = new class extends Policy
    {
        public function configure(): void
        {
            $this->addDirective(Directive::CAMERA, ['src-1', 'src-2']);
        }
    };

    config(['feature-policy.policy' => $policy::class]);

    $this->get('/__fp_headers_route')
        ->assertHeader('Permissions-Policy', 'camera=("src-1" "src-2")');
})
    ->group('unit', 'feature-policy');

it('does not quote special directive values', function () {
    $policy = new class extends Policy
    {
        public function configure(): void
        {
            $this->addDirective(Directive::CAMERA, [Value::SELF]);
        }
    };

    config(['feature-policy.policy' => $policy::class]);

    $this->get('/__fp_headers_route')
        ->assertHeader('Permissions-Policy', 'camera=self');
})
    ->group('unit', 'feature-policy');

it('parses space separated string values', function () {
    $policy = new class extends Policy
    {
        public function configure(): void
        {
            $this->addDirective(Directive::CAMERA, 'src-1 '.Value::SELF.' src-2');
        }
    };

    config(['feature-policy.policy' => $policy::class]);

    $this->get('/__fp_headers_route')
        ->assertHeader('Permissions-Policy', 'camera=("src-1" self "src-2")');
})
    ->group('unit', 'feature-policy');

it('does not add duplicate rule values', function () {
    $policy = new class extends Policy
    {
        public function configure(): void
        {
            $this->addDirective(Directive::CAMERA, [Value::SELF, Value::SELF]);
        }
    };

    config(['feature-policy.policy' => $policy::class]);

    $this->get('/__fp_headers_route')
        ->assertHeader('Permissions-Policy', 'camera=self');
})
    ->group('unit', 'feature-policy');

it('renders none value', function () {
    $policy = new class extends Policy
    {
        public function configure(): void
        {
            $this->addDirective(Directive::CAMERA, [Value::NONE]);
        }
    };

    config(['feature-policy.policy' => $policy::class]);

    $this->get('/__fp_headers_route')
        ->assertHeader('Permissions-Policy', 'camera=()');
})
    ->group('unit', 'feature-policy');

it('renders all value', function () {
    $policy = new class extends Policy
    {
        public function configure(): void
        {
            $this->addDirective(Directive::CAMERA, [Value::ALL]);
        }
    };

    config(['feature-policy.policy' => $policy::class]);

    $this->get('/__fp_headers_route')
        ->assertHeader('Permissions-Policy', 'camera=*');
})
    ->group('unit', 'feature-policy');

it('uses custom policy from route middleware parameter', function () {
    $customPolicy = new class extends Policy
    {
        public function configure(): void
        {
            $this->addDirective(Directive::FULLSCREEN, 'custom-policy');
        }
    };

    Route::get('/__fp_custom_policy_route', fn () => 'ok')
        ->middleware(AddFeaturePolicyHeaders::class.':'.$customPolicy::class);

    config(['feature-policy.policy' => SamplePermissionsPolicy::class]);

    $this->get('/__fp_custom_policy_route')
        ->assertSuccessful()
        ->assertHeader('Permissions-Policy', 'fullscreen="custom-policy"');
})
    ->group('unit', 'feature-policy');

it('adds reporting metadata when reporting is enabled', function () {
    config([
        'feature-policy.reporting.enabled' => true,
        'feature-policy.reporting.report_only' => false,
        'feature-policy.reporting.url' => 'https://example.test/report',
    ]);

    $policy = new class extends Policy
    {
        public function configure(): void
        {
            $this->addDirective(Directive::CAMERA, [Value::ALL]);
        }
    };

    config(['feature-policy.policy' => $policy::class]);

    $response = $this->get('/__fp_headers_route')->assertSuccessful();

    $response->assertHeader('Reporting-Endpoints', 'violation-reports="https://example.test/report"');
    $response->assertHeader('Permissions-Policy', 'camera=*; report-to=violation-reports');
    $response->assertHeaderMissing('Permissions-Policy-Report-Only');
})
    ->group('unit', 'feature-policy');

it('adds report-only header when configured', function () {
    config([
        'feature-policy.reporting.enabled' => true,
        'feature-policy.reporting.report_only' => true,
        'feature-policy.reporting.url' => 'https://example.test/report',
    ]);

    $policy = new class extends Policy
    {
        public function configure(): void
        {
            $this->addDirective(Directive::CAMERA, [Value::ALL]);
        }
    };

    config(['feature-policy.policy' => $policy::class]);

    $response = $this->get('/__fp_headers_route')->assertSuccessful();

    $response->assertHeader('Reporting-Endpoints', 'violation-reports="https://example.test/report"');
    $response->assertHeader('Permissions-Policy', 'camera=*; report-to=violation-reports');
    $response->assertHeader('Permissions-Policy-Report-Only', 'camera=*; report-to=violation-reports');
})
    ->group('unit', 'feature-policy');

it('does not set Permissions-Policy when config policy class is empty', function () {
    config(['feature-policy.policy' => '']);

    $this->get('/__fp_headers_route')
        ->assertSuccessful()
        ->assertHeaderMissing('Permissions-Policy');
})
    ->group('unit', 'feature-policy');

it('still applies custom middleware policy when config policy is empty', function () {
    config(['feature-policy.policy' => '']);

    $customPolicy = new class extends Policy
    {
        public function configure(): void
        {
            $this->addDirective(Directive::MICROPHONE, Value::SELF);
        }
    };

    Route::get('/__fp_empty_config_custom_route', fn () => 'ok')
        ->middleware(AddFeaturePolicyHeaders::class.':'.$customPolicy::class);

    $this->get('/__fp_empty_config_custom_route')
        ->assertSuccessful()
        ->assertHeader('Permissions-Policy', 'microphone=self');
})
    ->group('unit', 'feature-policy');

it('does not set Permissions-Policy when config policy is null', function () {
    config(['feature-policy.policy' => null]);

    $this->get('/__fp_headers_route')
        ->assertSuccessful()
        ->assertHeaderMissing('Permissions-Policy');
})
    ->group('unit', 'feature-policy');
