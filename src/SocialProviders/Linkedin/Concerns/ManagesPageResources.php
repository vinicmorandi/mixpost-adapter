<?php

namespace SaguiAi\MixpostAdapter\SocialProviders\Linkedin\Concerns;

use Illuminate\Support\Arr;
use SaguiAi\MixpostAdapter\Support\AccountSuffix;
use SaguiAi\MixpostAdapter\Support\SocialProviderResponse;

trait ManagesPageResources
{
    public function getAccount(): SocialProviderResponse
    {
        if ($this->hasRefreshToken() && $this->tokenIsAboutToExpire()) {
            $newAccessToken = $this->refreshToken();

            if ($newAccessToken->hasError()) {
                return $newAccessToken;
            }

            $this->updateToken($newAccessToken->context());
        }

        $response = $this->getHttpClient()::withToken($this->getAccessToken()['access_token'])
            ->withHeaders($this->httpHeaders())
            ->get("$this->apiUrl/$this->apiVersion/organizations/{$this->values['provider_id']}", [
                'projection' => '(id,localizedName,vanityName,logoV2(original~:playableStreams))'
            ]);

        return $this->buildResponse($response, function () use ($response) {
            $data = $response->json();

            return [
                'id' => $data['id'],
                'name' => $data['localizedName'],
                'username' => $data['vanityName'] ?? '',
                'image' => Arr::get($data, 'logoV2.original~.elements.0.identifiers.0.identifier'),
                'data' => AccountSuffix::schema('Page')
            ];
        });
    }

    public function getEntities(): SocialProviderResponse
    {
        if ($this->hasRefreshToken() && $this->tokenIsAboutToExpire()) {
            $newAccessToken = $this->refreshToken();

            if ($newAccessToken->hasError()) {
                return $newAccessToken;
            }

            $this->updateToken($newAccessToken->context());
        }

        $response = $this->getHttpClient()::withToken($this->getAccessToken()['access_token'])
            ->withHeaders($this->httpHeaders())
            ->get("$this->apiUrl/$this->apiVersion/organizationalEntityAcls", [
                'q' => 'roleAssignee',
                'projection' => '(elements*(organizationalTarget~(id,vanityName,localizedName,logoV2(original~:playableStreams))))'
            ]);

        return $this->buildResponse($response, function () use ($response) {
            return $response->collect('elements')->map(function ($item) {
                return [
                    'id' => $item['organizationalTarget~']['id'],
                    'name' => $item['organizationalTarget~']['localizedName'],
                    'username' => $item['organizationalTarget~']['vanityName'] ?? '',
                    'image' => Arr::get($item, 'organizationalTarget~.logoV2.original~.elements.0.identifiers.0.identifier'),
                    'data' => AccountSuffix::schema('Page')
                ];
            })->toArray();
        });
    }

    public function getFollowerCount(): SocialProviderResponse
    {
        if ($this->hasRefreshToken() && $this->tokenIsAboutToExpire()) {
            $newAccessToken = $this->refreshToken();

            if ($newAccessToken->hasError()) {
                return $newAccessToken;
            }

            $this->updateToken($newAccessToken->context());
        }

        $response = $this->getHttpClient()::withToken($this->getAccessToken()['access_token'])
            ->get("$this->apiUrl/$this->apiVersion/networkSizes/urn:li:organization:{$this->values['provider_id']}", [
                'edgeType' => 'CompanyFollowedByMember'
            ]);

        return $this->buildResponse($response, function () use ($response) {
            return [
                'count' => $response->json('firstDegreeSize')
            ];
        });
    }

    public function getShares($count = 99, $start = 0): SocialProviderResponse
    {
        if ($this->hasRefreshToken() && $this->tokenIsAboutToExpire()) {
            $newAccessToken = $this->refreshToken();

            if ($newAccessToken->hasError()) {
                return $newAccessToken;
            }

            $this->updateToken($newAccessToken->context());
        }

        return $this->buildResponse(
            $this->getHttpClient()::withToken($this->getAccessToken()['access_token'])
                ->get("$this->apiUrl/$this->apiVersion/shares", [
                    'q' => 'owners',
                    'owners' => "urn:li:organization:{$this->values['provider_id']}",
                    'sharesPerOwner' => 1000,
                    'count' => $count,
                    'start' => $start
                ])
        );
    }
}
