<?php

declare(strict_types=1);

namespace CodebarAg\LaravelFeaturePolicy\Formatter;

use CodebarAg\LaravelFeaturePolicy\FeatureGroups\DirectiveContract;
use Illuminate\Support\Collection;

final class PolicyFormatter implements FormatContract
{
    /** @var Collection<string, DirectiveContract> */
    private readonly Collection $directives;

    /**
     * @param  array<string, DirectiveContract>  $directives
     */
    public function __construct(array $directives)
    {
        $this->directives = collect($directives);
    }

    public function __toString(): string
    {
        $policy = $this->directives
            ->map(function (DirectiveContract $directive) {
                $formattedRules = implode(' ', $directive->rules());

                if (count($directive->rules()) === 1) {
                    return "{$directive->name()}={$formattedRules}";
                }

                return "{$directive->name()}=({$formattedRules})";
            })
            ->implode(',');

        if (config('feature-policy.reporting.enabled')) {
            $policy .= '; report-to=violation-reports';
        }

        return $policy;
    }
}
