<?php

namespace SaguiAi\MixpostAdapter\SocialProviders\TikTok\Concerns;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Arr;
use SaguiAi\MixpostAdapter\Enums\SocialProviderResponseStatus;
use SaguiAi\MixpostAdapter\Services\RateLimitService;
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

        $rateLimitAboutToBeExceeded = $usage['about_to_be_exceeded'];
        $retryAfter = $usage['retry_after'];
        $isAppLevel = true;

        if (in_array($response->status(), [200, 201])) {
            return $this->response(
                SocialProviderResponseStatus::OK,
                Arr::wrap($okResult ? $okResult() : $response->json()),
                $rateLimitAboutToBeExceeded,
                $retryAfter,
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
                $this->rateLimitExceedContext(60),
                $rateLimitAboutToBeExceeded,
                $retryAfter,
                $isAppLevel
            );
        }

        if ($response->status() === 416) {
            return $this->response(
                SocialProviderResponseStatus::ERROR,
                [$response->reason()],
                $rateLimitAboutToBeExceeded,
                $retryAfter,
                $isAppLevel
            );
        }

        return $this->response(
            SocialProviderResponseStatus::ERROR,
            [
                'data' => $response->json(),
                'reason' => $response->reason()
            ],
            $rateLimitAboutToBeExceeded,
            $retryAfter,
            $isAppLevel
        );
    }

    /**
     * Rate limit
     *
     * @see https://developers.tiktok.com/doc/tiktok-api-v2-rate-limit/
     */
    public function getRateLimitUsage(Response $response): array
    {
        $endpoint = $response->transferStats->getEffectiveUri()->getPath();

        $limit = match ($endpoint) {
            '/v2/user/info/' => 600,
            '/v2/video/query/' => 600,
            '/v2/video/list/' => 600,
            default => 600,
        };

        $timeframeInMinutes = 1;

        $rateLimitService = new RateLimitService(
            "tiktok-$endpoint",
            $limit,
            1,
        );

        $rateLimitService->record();

        return [
            'limit' => $limit,
            'about_to_be_exceeded' => $rateLimitService->isAboutToBeExceeded(20),
            'retry_after' => $timeframeInMinutes * 60
        ];
    }
}
