<?php

declare(strict_types=1);

namespace CodebarAg\LaravelFeaturePolicy\Policies;

use CodebarAg\LaravelFeaturePolicy\Directive;
use CodebarAg\LaravelFeaturePolicy\FeatureGroups\DefaultFeatureGroup;
use CodebarAg\LaravelFeaturePolicy\FeatureGroups\DirectiveContract;
use CodebarAg\LaravelFeaturePolicy\Formatter\PolicyFormatter;
use CodebarAg\LaravelFeaturePolicy\Value;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class Policy implements PolicyContract
{
    /** @var array<string, DirectiveContract> */
    protected array $directives = [];

    /** @var array<string, list<string>> */
    private array $rules = [];

    abstract public function configure(): void;

    public function addDirective(string $directive, array|string $values, ?string $type = null): self
    {
        $groupClass = $type ?? DefaultFeatureGroup::class;
        $currentDirective = Directive::make($directive, type: $groupClass);
        collect($this->rules[$directive] ??= [])
            ->each(fn (string $rule) => $currentDirective->addRule($rule));

        $valueItems = is_array($values) ? $values : [$values];
        collect($valueItems)
            ->map(fn (mixed $valueItem) => array_filter(explode(' ', (string) $valueItem)))
            ->flatten()
            ->map(fn (string $rule) => $this->isSpecialDirectiveValue($rule) ? $rule : "\"{$rule}\"")
            ->each(fn (string $rule) => $currentDirective->addRule($rule))
            ->each(fn (string $rule) => $this->rules[$directive][] = $rule);

        $this->directives[$directive] = $currentDirective;

        return $this;
    }

    public function shouldBeApplied(Request $request, Response $response): bool
    {
        return config('feature-policy.enabled');
    }

    public function applyTo(Response $response): void
    {
        if (! $this->directives) {
            $this->configure();
        }

        $headerName = 'Permissions-Policy';

        if ($response->headers->has($headerName)) {
            return;
        }

        $response->headers->set($headerName, (string) $this);

        if (! config('feature-policy.reporting.enabled')) {
            return;
        }

        $response->headers->set('Reporting-Endpoints', 'violation-reports="'.config('feature-policy.reporting.url').'"');

        if (! config('feature-policy.reporting.report_only')) {
            return;
        }

        $headerName = 'Permissions-Policy-Report-Only';

        if ($response->headers->has($headerName)) {
            return;
        }

        $response->headers->set($headerName, (string) $this);
    }

    public function __toString(): string
    {
        return (string) new PolicyFormatter($this->directives);
    }

    protected function isSpecialDirectiveValue(string $value): bool
    {
        $specialDirectiveValues = [
            Value::NONE,
            Value::SELF,
            Value::ALL,
        ];

        return in_array($value, $specialDirectiveValues, true);
    }
}
