<?php

declare(strict_types=1);

namespace CodebarAg\LaravelFeaturePolicy\Policies;

use Illuminate\Http\Request;
use Stringable;
use Symfony\Component\HttpFoundation\Response;

interface PolicyContract extends Stringable
{
    /**
     * @param  array<string>|string  $values
     */
    public function addDirective(string $directive, array|string $values, ?string $type = null): self;

    public function shouldBeApplied(Request $request, Response $response): bool;

    public function applyTo(Response $response): void;
}
