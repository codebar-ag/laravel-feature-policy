<?php

declare(strict_types=1);

namespace CodebarAg\LaravelFeaturePolicy;

use Closure;
use CodebarAg\LaravelFeaturePolicy\Policies\Policy;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\Response;

final class AddFeaturePolicyHeaders
{
    public function handle(Request $request, Closure $next, ?string $customPolicyClass = null): Response
    {
        $response = $next($request);

        $this->getPolicies($customPolicyClass)
            ->filter(fn (Policy $policy) => $policy->shouldBeApplied($request, $response))
            ->each(fn (Policy $policy) => $policy->applyTo($response));

        return $response;
    }

    /** @return Collection<int, Policy> */
    protected function getPolicies(?string $customPolicyClass = null): Collection
    {
        $policies = collect();

        if ($customPolicyClass) {
            $policies->push(PolicyFactory::create($customPolicyClass));

            return $policies;
        }

        $policyClass = config('feature-policy.policy');

        if (is_string($policyClass) && $policyClass !== '') {
            $policies->push(PolicyFactory::create($policyClass));
        }

        return $policies;
    }
}
