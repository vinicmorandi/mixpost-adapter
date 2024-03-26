<?php

namespace SaguiAi\MixpostAdapter\SocialProviders\Linkedin\Concerns;

use Illuminate\Support\Carbon;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Arr;
use SaguiAi\MixpostAdapter\Enums\SocialProviderResponseStatus;
use SaguiAi\MixpostAdapter\Support\SocialProviderResponse;
use Closure;

trait ManagesRateLimit
{
    /**
     * @param $response Response
     */
    public function buildResponse($response, Closure $okResult = null): SocialProviderResponse
    {
        $usage = $this->getRateLimitUsage($response);

        if ($response->status() === 429 || $usage['exceeded']) {
            return $this->response(
                SocialProviderResponseStatus::EXCEEDED_RATE_LIMIT,
                $this->rateLimitExceedContext($usage['retry_after']),
                false,
                $usage['retry_after'],
                true
            );
        }

        if ($response->status() === 401) {
            return $this->response(
                SocialProviderResponseStatus::UNAUTHORIZED,
                ['access_token_expired']
            );
        }

        if (in_array($response->status(), [200, 201])) {
            return $this->response(SocialProviderResponseStatus::OK, $okResult ? $okResult() : $response->json());
        }

        return $this->response(
            SocialProviderResponseStatus::ERROR,
            $response->json()
        );
    }

    /**
     * Rate limit
     *
     * @see https://learn.microsoft.com/en-us/linkedin/shared/api-guide/concepts/rate-limits
     */
    public function getRateLimitUsage(Response $response): array
    {
        $now = Carbon::now()->utc();
        $nextMidnight = Carbon::tomorrow('UTC');
        $retryAfter = $now->diffInSeconds($nextMidnight);

        return [
            'retry_after' => $retryAfter,
            'exceeded' => Arr::get($response->json(), 'elements.0.organizationalTarget!.status') === 429,
        ];
    }
}
