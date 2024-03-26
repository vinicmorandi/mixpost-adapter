<?php

namespace SaguiAi\MixpostAdapter\Services;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

class RateLimitService
{
    public function __construct(
        private readonly string $key,
        private readonly int    $limit,
        private readonly int    $timeframeInMinutes,
    )
    {
    }

    public function isExceeded(): bool
    {
        $requests = Cache::get($this->getKey(), []);

        // Filter requests that happened in the `timeframeInMinutes`
        $minutesAgo = Carbon::now()->subMinutes($this->timeframeInMinutes);
        $requests = array_filter($requests, function ($timestamp) use ($minutesAgo) {
            return $timestamp > $minutesAgo;
        });

        return count($requests) >= $this->limit;
    }

    public function isAboutToBeExceeded(int $additionalRequests = 10): bool
    {
        $requests = Cache::get($this->getKey(), []);

        // Filter requests that happened in the `timeframeInMinutes`
        $minutesAgo = Carbon::now()->subMinutes($this->timeframeInMinutes);
        $requests = array_filter($requests, function ($timestamp) use ($minutesAgo) {
            return $timestamp > $minutesAgo;
        });

        return (count($requests) + $additionalRequests) >= $this->limit;
    }

    public function getRemaining(): int
    {
        $requests = Cache::get($this->getKey(), []);

        // Filter requests that happened in the `timeframeInMinutes`
        $minutesAgo = Carbon::now()->subMinutes($this->timeframeInMinutes);
        $requests = array_filter($requests, function ($timestamp) use ($minutesAgo) {
            return $timestamp > $minutesAgo;
        });

        $remainingRequests = $this->limit - count($requests);

        return max($remainingRequests, 0);
    }

    public function record(): void
    {
        $requests = Cache::get($this->getKey(), []);

        $requests[] = Carbon::now();

        Cache::put($this->getKey(), $requests, Carbon::now()->addMinutes($this->timeframeInMinutes + 1));
    }

    private function getKey(): string
    {
        return "mixpost-$this->key-requests";
    }
}
