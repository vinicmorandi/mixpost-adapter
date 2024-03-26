<?php

namespace SaguiAi\MixpostAdapter\SocialProviders\Pinterest\Concerns;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
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
        $usage = $this->getRateLimitUsage($response->headers());
        $rateLimitAboutToBeExceeded = $usage['remaining'] < 5;
        $isAppLevel = $this->getEnvironment() === 'sandbox';

        if (in_array($response->status(), [200, 201])) {
            return $this->response(
                SocialProviderResponseStatus::OK,
                $okResult ? $okResult() : $response->json(),
                $rateLimitAboutToBeExceeded,
                $usage['retry_after'],
                $isAppLevel
            );
        }

        if ($response->status() === 401) {
            return $this->response(
                SocialProviderResponseStatus::UNAUTHORIZED,
                ['access_token_expired']
            );
        }

        if ($response->status() === 429) {
            return $this->response(
                SocialProviderResponseStatus::EXCEEDED_RATE_LIMIT,
                $this->rateLimitExceedContext($usage['retry_after']),
                $rateLimitAboutToBeExceeded,
                $usage['retry_after'],
                $isAppLevel
            );
        }

        // Pinterest message contains media base64 string, so we don't want store it in database
        // Storing base64 string in database may cause performance issue
        // We will truncate message to 100 characters
        return $this->response(
            SocialProviderResponseStatus::ERROR,
            [
                'code' => $response->json('code'),
                'message' => Str::limit($response->json('message'), 100, '...'),
            ],
            $rateLimitAboutToBeExceeded,
            $usage['retry_after'],
            $isAppLevel
        );
    }

    /**
     * Rate limit
     *
     * @see https://developers.pinterest.com/docs/reference/ratelimits
     */
    public function getRateLimitUsage(array $headers): array
    {
        $headers = array_change_key_case($headers, CASE_LOWER);

        $reset = intval(Arr::get($headers, 'x-ratelimit-reset.0', 0));

        return [
            'limit' => Arr::get($headers, 'x-ratelimit-limit.0', ''),
            'remaining' => intval(Arr::get($headers, 'x-ratelimit-remaining.0', 0)),
            'retry_after' => $reset * 60, // convert minutes to seconds
        ];
    }
}
