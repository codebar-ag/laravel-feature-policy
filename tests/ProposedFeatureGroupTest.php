<?php

use CodebarAg\LaravelFeaturePolicy\AddFeaturePolicyHeaders;
use CodebarAg\LaravelFeaturePolicy\Exceptions\DisabledFeatureGroupException;
use CodebarAg\LaravelFeaturePolicy\FeatureGroups\ProposedFeatureGroup;
use CodebarAg\LaravelFeaturePolicy\Policies\Policy;
use CodebarAg\LaravelFeaturePolicy\Value;
use Illuminate\Support\Facades\Route;

beforeEach(function () {
    config([
        'feature-policy.enabled' => true,
        'feature-policy.directives.proposal' => false,
        'feature-policy.reporting.enabled' => false,
    ]);

    Route::middleware(['web', AddFeaturePolicyHeaders::class])
        ->get('/__fp_proposed_route', fn () => 'ok');
});

it('throws when proposed directive is used while proposal group is disabled', function () {
    $this->withoutExceptionHandling();

    $policy = new class extends Policy
    {
        public function configure(): void
        {
            $this->addDirective(ProposedFeatureGroup::GAMEPAD, [Value::ALL], ProposedFeatureGroup::class);
        }
    };

    config(['feature-policy.policy' => $policy::class]);

    $this->get('/__fp_proposed_route');
})
    ->throws(DisabledFeatureGroupException::class, 'The directive (gamepad) is disabled.')
    ->group('unit', 'feature-policy');

it('renders proposed directive when proposal group is enabled', function () {
    $policy = new class extends Policy
    {
        public function configure(): void
        {
            $this->addDirective(ProposedFeatureGroup::CLIPBOARD_READ, [Value::ALL], ProposedFeatureGroup::class);
        }
    };

    config([
        'feature-policy.policy' => $policy::class,
        'feature-policy.directives.proposal' => true,
    ]);

    $this->get('/__fp_proposed_route')
        ->assertSuccessful()
        ->assertHeader('Permissions-Policy', 'clipboard-read=*');
})
    ->group('unit', 'feature-policy');
