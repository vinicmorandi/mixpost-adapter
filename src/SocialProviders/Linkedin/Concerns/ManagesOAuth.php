<?php

namespace SaguiAi\MixpostAdapter\SocialProviders\Linkedin\Concerns;

use Illuminate\Support\Carbon;
use SaguiAi\MixpostAdapter\Support\SocialProviderResponse;

trait ManagesOAuth
{
    public string $oAuthVersion = 'v2';
    public string $oAuthUrl = 'https://www.linkedin.com/oauth';

    /** Scopes are defined in LinkedinProvider **/

    public function getAuthUrl(): string
    {
        $params = [
            'client_id' => $this->clientId,
            'redirect_uri' => $this->redirectUrl,
            'scope' => urlencode(implode(' ', $this->scope)),
            'state' => $this->values['state'],
            'response_type' => 'code',
        ];

        $url = "$this->oAuthUrl/$this->oAuthVersion/authorization";

        $url = $this->buildUrlFromBase($url, $params);

        return str_replace('%2B', '%20', $url);
    }

    public function requestAccessToken(array $params = []): array
    {
        $params = [
            'grant_type' => 'authorization_code',
            'code' => $params['code'],
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'redirect_uri' => $this->redirectUrl,
        ];

        $response = $this->getHttpClient()::withHeaders($this->httpHeaders())
            ->asForm()
            ->post("$this->oAuthUrl/$this->oAuthVersion/accessToken", $params)->json();

        if (isset($response['serviceErrorCode'])) {
            return [
                'error' => $response['message']
            ];
        }

        if (isset($response['error'])) {
            return [
                'error' => $response['error_description']
            ];
        }

        return [
            'access_token' => $response['access_token'],
            'expires_in' => Carbon::now('UTC')->addSeconds($response['expires_in'])->timestamp,
            'refresh_token' => $response['refresh_token'] ?? '',
        ];
    }

    public function refreshToken(string $refreshToken = null): SocialProviderResponse
    {
        $params = [
            'grant_type' => 'refresh_token',
            'refresh_token' => $refreshToken ?: $this->getAccessToken()['refresh_token'],
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
        ];

        $response = $this->getHttpClient()::withHeaders($this->httpHeaders())
            ->asForm()
            ->post("$this->oAuthUrl/$this->oAuthVersion/accessToken", $params);

        return $this->buildResponse($response, function () use ($response) {
            $data = $response->json();

            return [
                'access_token' => $data['access_token'],
                'expires_in' => Carbon::now('UTC')->addSeconds($data['expires_in'])->timestamp,
                'refresh_token' => $data['refresh_token'],
            ];
        });
    }
}
