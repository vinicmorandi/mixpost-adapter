<?php

namespace SaguiAi\MixpostAdapter\SocialProviders\TikTok\Concerns;

use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use SaguiAi\MixpostAdapter\Support\SocialProviderResponse;

trait ManagesOAuth
{
    protected function scope(): array
    {
        $scope = [
            'user.info.basic',
            'user.info.profile',
            'user.info.stats',
            'video.list',
            'video.upload',
        ];

        if (self::isDirectShareType()) {
            $scope[] = 'video.publish';
        }

        return $scope;
    }

    public function getAuthUrl(): string
    {
        $params = [
            'client_key' => $this->clientId,
            'redirect_uri' => $this->redirectUrl,
            'scope' => implode(',', $this->scope()),
            'state' => $this->values['state'],
            'response_type' => 'code',
        ];

        $url = "https://www.tiktok.com/$this->apiVersion/auth/authorize";

        return $this->buildUrlFromBase($url, $params);
    }

    public function requestAccessToken(array $params = []): array
    {
        $params = [
            'client_key' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'redirect_uri' => $this->redirectUrl,
            'grant_type' => 'authorization_code',
            'code' => $params['code'],
        ];

        $response = $this->getHttpClient()::asForm()->post("$this->apiUrl/$this->apiVersion/oauth/token/", $params)->json();

        if (Arr::has($response, 'error')) {
            return [
                'error' => $response['error_description']
            ];
        }

        return [
            'open_id' => $response['open_id'],
            'scope' => $response['scope'],
            'access_token' => $response['access_token'],
            'expires_in' => Carbon::now('UTC')->addSeconds($response['expires_in'])->timestamp,
            'refresh_token' => $response['refresh_token'],
            'refresh_token_expires_in' => Carbon::now('UTC')->addSeconds($response['refresh_expires_in'])->timestamp,
        ];
    }

    public function refreshToken(string $refreshToken = null): SocialProviderResponse
    {
        $params = [
            'client_key' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'grant_type' => 'refresh_token',
            'refresh_token' => $refreshToken ?: $this->getAccessToken()['refresh_token']
        ];

        $response = $this->getHttpClient()::asForm()->post("$this->apiUrl/$this->apiVersion/oauth/token/", $params);

        return $this->buildResponse($response, function () use ($response) {
            $data = $response->json();

            return [
                'open_id' => $data['open_id'],
                'access_token' => $data['access_token'],
                'refresh_token' => $data['refresh_token'],
                'expires_in' => Carbon::now('UTC')->addSeconds($data['expires_in'])->timestamp,
                'refresh_token_expires_in' => Carbon::now('UTC')->addSeconds($data['refresh_expires_in'])->timestamp,
            ];
        });
    }

    public function revokeToken(): SocialProviderResponse
    {
        $params = [
            'open_id' => $this->getAccessToken()['open_id'],
            'access_token' => $this->getAccessToken()['access_token']
        ];

        $response = $this->getHttpClient()::asForm()->post("$this->apiUrl/$this->apiVersion/oauth/revoke/", $params);

        return $this->buildResponse($response);
    }
}
