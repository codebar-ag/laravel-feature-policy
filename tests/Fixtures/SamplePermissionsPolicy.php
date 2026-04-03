<?php

declare(strict_types=1);

namespace CodebarAg\LaravelFeaturePolicy\Tests\Fixtures;

use CodebarAg\LaravelFeaturePolicy\Directive;
use CodebarAg\LaravelFeaturePolicy\Policies\Policy;
use CodebarAg\LaravelFeaturePolicy\Value;

final class SamplePermissionsPolicy extends Policy
{
    public function configure(): void
    {
        $this->addDirective(Directive::GEOLOCATION, Value::SELF)
            ->addDirective(Directive::FULLSCREEN, Value::SELF);
    }
}
