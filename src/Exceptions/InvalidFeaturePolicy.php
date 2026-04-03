<?php

namespace CodebarAg\LaravelFeaturePolicy\Exceptions;

use CodebarAg\LaravelFeaturePolicy\Policies\Policy;
use Exception;

final class InvalidFeaturePolicy extends Exception
{
    public static function create(object $policy): self
    {
        $className = $policy::class;

        return new self("The Feature-Policy class '{$className}' is not valid. A valid policy extends ".Policy::class);
    }
}
