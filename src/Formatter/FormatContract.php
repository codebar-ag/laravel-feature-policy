<?php

declare(strict_types=1);

namespace CodebarAg\LaravelFeaturePolicy\Formatter;

use Stringable;

interface FormatContract extends Stringable
{
    public function __toString(): string;
}
