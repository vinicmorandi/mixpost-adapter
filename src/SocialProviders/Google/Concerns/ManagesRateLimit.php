<?php

namespace SaguiAi\MixpostAdapter\SocialProviders\Google\Concerns;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Carbon;
use SaguiAi\MixpostAdapter\Enums\SocialProviderResponseStatus;
use SaguiAi\MixpostAdapter\Support\SocialProviderResponse;
use Closure;

trait ManagesRateLimit
{
    /**
     * YouTube Data API (v3) - Quota Calculator
     *
     * @see https://developers.google.com/youtube/v3/determine_quota_cost
     */
    public function getQuotaUsage(array $headers): array|null
    {
        return null;
    }

    /**
     * @param $response Response
     */
    public function buildResponse($response, Closure $okResult = null): SocialProviderResponse
    {
        if (in_array($response->status(), [200, 201])) {
            return $this->response(SocialProviderResponseStatus::OK, $okResult ? $okResult() : $response->json());
        }

        if ($response->status() === 401) {
            return $this->response(SocialProviderResponseStatus::UNAUTHORIZED, $response->json());
        }

        if ($response->status() === 403) {
            $now = Carbon::now('UTC');
            $nextMidnight = Carbon::tomorrow('UTC');
            $retryAfter = $now->diffInSeconds($nextMidnight);

            return $this->response(
                SocialProviderResponseStatus::EXCEEDED_RATE_LIMIT,
                $this->rateLimitExceedContext($retryAfter, 'The daily quota has been exceeded.'),
                false,
                $retryAfter,
                true
            );
        }

        return $this->response(SocialProviderResponseStatus::ERROR, $response->json());
    }
}
