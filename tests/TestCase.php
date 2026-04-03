<?php

declare(strict_types=1);

namespace CodebarAg\LaravelFeaturePolicy\Tests;

use CodebarAg\LaravelFeaturePolicy\LaravelFeaturePolicyServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    /**
     * @param  \Illuminate\Foundation\Application  $app
     * @return list<class-string>
     */
    protected function getPackageProviders($app): array
    {
        return [
            LaravelFeaturePolicyServiceProvider::class,
        ];
    }
}
