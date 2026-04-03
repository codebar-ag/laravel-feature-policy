<?php

declare(strict_types=1);

namespace CodebarAg\LaravelFeaturePolicy;

use Illuminate\Support\ServiceProvider;

final class LaravelFeaturePolicyServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/feature-policy.php', 'feature-policy');
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/feature-policy.php' => config_path('feature-policy.php'),
            ], 'laravel-feature-policy-config');
        }
    }
}
