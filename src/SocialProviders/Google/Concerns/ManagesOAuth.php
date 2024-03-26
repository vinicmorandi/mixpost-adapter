<?php

namespace SaguiAi\MixpostAdapter\SocialProviders\Google\Concerns;

use Illuminate\Support\Carbon;
use SaguiAi\MixpostAdapter\Support\SocialProviderResponse;

trait ManagesOAuth
{
    protected array $scope = [
        'https://www.googleapis.com/auth/youtube'
    ];

    public function getAuthUrl(): string
    {
        $params = [
            'client_id' => $this->clientId,
            'redirect_uri' => $this->redirectUrl,
            'scope' => implode(' ', $this->scope),
            'state' => $this->values['state'],
            'include_granted_scopes' => 'true',
            'response_type' => 'code',
            'access_type' => 'offline',
            'prompt' => 'consent',
        ];

        $url = 'https://accounts.google.com/o/oauth2/v2/auth';

        return $this->buildUrlFromBase($url, $params);
    }

    public function requestAccessToken(array $params = []): array
    {
        $params = [
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'redirect_uri' => $this->redirectUrl,
            'grant_type' => 'authorization_code',
            'code' => $params['code'],
        ];

        $response = $this->getHttpClient()::post('https://accounts.google.com/o/oauth2/token', $params)->json();

        if (isset($response['error'])) {
            return [
                'error' => $response['error_description']
            ];
        }

        return [
            'access_token' => $response['access_token'],
            'refresh_token' => $response['refresh_token'],
            'expires_in' => Carbon::now('UTC')->addSeconds($response['expires_in'])->timestamp,
        ];
    }

    public function refreshToken(string $refreshToken = null): SocialProviderResponse
    {
        $params = [
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'redirect_uri' => $this->redirectUrl,
            'grant_type' => 'refresh_token',
            'refresh_token' => $refreshToken ?: $this->getAccessToken()['refresh_token']
        ];

        $response = $this->getHttpClient()::post('https://oauth2.googleapis.com/token', $params);

        return $this->buildResponse($response, function () use ($response) {
            $data = $response->json();

            return [
                'access_token' => $data['access_token'],
                'expires_in' => Carbon::now('UTC')->addSeconds($data['expires_in'])->timestamp,
            ];
        });
    }

    public function revokeToken(string $token = null): SocialProviderResponse
    {
        $response = $this->getHttpClient()::post('https://oauth2.googleapis.com/revoke', [
            'token' => $token ?: $this->getAccessToken()['access_token']
        ]);

        return $this->buildResponse($response);
    }
}
