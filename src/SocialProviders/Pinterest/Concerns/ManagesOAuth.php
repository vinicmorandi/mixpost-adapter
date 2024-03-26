<?php

namespace SaguiAi\MixpostAdapter\SocialProviders\Pinterest\Concerns;

use Illuminate\Support\Carbon;
use SaguiAi\MixpostAdapter\Support\SocialProviderResponse;

trait ManagesOAuth
{
    protected array $scope = [
        'user_accounts:read',
        'boards:read',
        'boards:read_secret',
        'boards:write',
        'boards:write_secret',
        'pins:read',
        'pins:read_secret',
        'pins:write'
    ];

    public function getAuthUrl(): string
    {
        $params = [
            'client_id' => $this->clientId,
            'redirect_uri' => $this->redirectUrl,
            'scope' => implode(',', $this->scope),
            'state' => $this->values['state'],
            'response_type' => 'code',
        ];

        $url = "https://www.pinterest.com/oauth";

        return $this->buildUrlFromBase($url, $params);
    }

    public function requestAccessToken(array $params = []): array
    {
        $params = [
            'grant_type' => 'authorization_code',
            'code' => $params['code'],
            'redirect_uri' => $this->redirectUrl,
        ];

        $response = $this->getHttpClient()::withHeaders($this->authHeader())
            ->asForm()
            ->post("{$this->getApiUrl()}/$this->apiVersion/oauth/token", $params)->json();

        if (isset($response['error'])) {
            return [
                'error' => $response['error_description']
            ];
        }

        return [
            'access_token' => $response['access_token'],
            'expires_in' => Carbon::now('UTC')->addSeconds($response['expires_in'])->timestamp,
            'refresh_token' => $response['refresh_token'],
            'refresh_token_expires_in' => Carbon::now('UTC')->addSeconds($response['refresh_token_expires_in'])->timestamp,
        ];
    }

    public function refreshToken(string $refreshToken = null): SocialProviderResponse
    {
        $params = [
            'grant_type' => 'refresh_token',
            'refresh_token' => $refreshToken ?: $this->getAccessToken()['refresh_token'],
            'scope' => implode(',', $this->scope),
        ];

        $response = $this->getHttpClient()::withHeaders($this->authHeader())
            ->asForm()
            ->post("{$this->getApiUrl()}/$this->apiVersion/oauth/token", $params);

        return $this->buildResponse($response, function () use ($response) {
            $data = $response->json();

            return [
                'access_token' => $data['access_token'],
                'expires_in' => Carbon::now('UTC')->addSeconds($data['expires_in'])->timestamp,
            ];
        });
    }

    protected function authHeader(): array
    {
        return [
            'Authorization' => 'Basic ' . base64_encode($this->clientId . ':' . $this->clientSecret)
        ];
    }
}
