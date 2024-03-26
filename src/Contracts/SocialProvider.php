<?php

namespace SaguiAi\MixpostAdapter\Contracts;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use SaguiAi\MixpostAdapter\Http\Resources\AccountResource;
use SaguiAi\MixpostAdapter\Support\SocialProviderResponse;
use Closure;

interface SocialProvider
{
    public function __construct(Request $request, string $clientId, string $clientSecret, string $redirectUrl, array $values = []);

    public function identifier(): string;

    public function getAuthUrl(): string;

    public function requestAccessToken(array $params): array;

    public function useAccessToken(array $token = []): static;

    public function buildResponse($response, Closure $okResult = null): SocialProviderResponse;

    public function getAccount(): SocialProviderResponse;

    public function publishPost(string $text, Collection $media, array $params = []): SocialProviderResponse;

    public function deletePost($id): SocialProviderResponse;

    public static function externalPostUrl(AccountResource $accountResource): string;
}
