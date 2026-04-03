<?php

declare(strict_types=1);

use CodebarAg\LaravelFeaturePolicy\LaravelFeaturePolicyServiceProvider;

it('publishes config via vendor:publish tag', function () {
    $target = config_path('feature-policy.php');

    if (is_file($target)) {
        unlink($target);
    }

    $this->artisan('vendor:publish', [
        '--provider' => LaravelFeaturePolicyServiceProvider::class,
        '--tag' => 'laravel-feature-policy-config',
        '--force' => true,
    ])->assertOk();

    expect(is_file($target))->toBeTrue()
        ->and(file_get_contents($target))->toContain('FPH_ENABLED');
})
    ->group('unit', 'feature-policy');
