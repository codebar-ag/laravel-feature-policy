<?php

declare(strict_types=1);

namespace CodebarAg\LaravelFeaturePolicy\FeatureGroups;

interface FeatureGroupContract
{
    public static function directive(string $directive): DirectiveContract;
}
