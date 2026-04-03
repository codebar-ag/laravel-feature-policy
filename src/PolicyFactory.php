<?php

namespace CodebarAg\LaravelFeaturePolicy;

use CodebarAg\LaravelFeaturePolicy\Exceptions\InvalidFeaturePolicy;
use CodebarAg\LaravelFeaturePolicy\Policies\Policy;

final class PolicyFactory
{
    public static function create(string $className): Policy
    {
        $policy = app($className);

        if (! $policy instanceof Policy) {
            throw InvalidFeaturePolicy::create($policy);
        }

        return $policy;
    }
}
