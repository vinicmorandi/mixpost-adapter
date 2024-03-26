<?php

namespace SaguiAi\MixpostAdapter\SocialProviders\Meta\Concerns;

use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use SaguiAi\MixpostAdapter\Enums\SocialProviderResponseStatus;
use SaguiAi\MixpostAdapter\Support\AccountSuffix;
use SaguiAi\MixpostAdapter\Support\SocialProviderResponse;

trait ManagesFacebookPageResources
{
    use FacebookPostPublication;
    use FacebookReelPublication;
    use FacebookStoryPublication;

    public function getAccount(): SocialProviderResponse
    {
        $response = $this->getHttpClient()::get("$this->apiUrl/$this->apiVersion/me", [
            'fields' => 'id,name,username,picture{url},location',
            'access_token' => $this->getAccessToken()['page_access_token']
        ]);

        return $this->buildResponse($response, function () use ($response) {
            $data = $response->json();

            return [
                'id' => $data['id'],
                'name' => $data['name'],
                'username' => $data['username'] ?? '',
                'image' => Arr::get($data, 'picture.data.url'),
                'data' => AccountSuffix::schema(strval(Arr::get($data, 'location.city')))
            ];
        });
    }

    public function getEntities(bool $withAccessToken = false): SocialProviderResponse
    {
        $response = $this->getHttpClient()::withToken($this->getAccessToken()['access_token'])
            ->get("$this->apiUrl/$this->apiVersion/me/accounts", [
                'fields' => 'id,name,username,picture{url},location' . ($withAccessToken ? ',access_token' : ''),
                'limit' => 200
            ]);

        return $this->buildResponse($response, function () use ($response, $withAccessToken) {
            return $response->collect('data')->map(function ($item) use ($withAccessToken) {
                $array = [
                    'id' => $item['id'],
                    'name' => $item['name'],
                    'username' => $item['username'] ?? '',
                    'image' => Arr::get($item, 'picture.data.url'),
                    'data' => AccountSuffix::schema(strval(Arr::get($item, 'location.city'))),
                ];

                if ($withAccessToken) {
                    $array['access_token'] = [
                        'access_token' => $item['access_token']
                    ];
                }

                return $array;
            })->toArray();
        });
    }

    public function publishPost(string $text, Collection $media, array $params = []): SocialProviderResponse
    {
        $type = Arr::get($params, 'type', 'post');

        return match ($type) {
            'post' => $this->publishFacebookStandardPost($text, $media),
            'reel' => $this->publishFacebookReel($text, $media),
            'story' => $this->publishFacebookStory($media),
            default => $this->response(SocialProviderResponseStatus::NO_CONTENT, []),
        };
    }

    public function getPageAudience(): SocialProviderResponse
    {
        $response = $this->getHttpClient()::get("$this->apiUrl/$this->apiVersion/{$this->values['provider_id']}", [
            'fields' => 'fan_count,followers_count',
            'access_token' => $this->getAccessToken()['page_access_token']
        ]);

        return $this->buildResponse($response);
    }

    public function getPageInsights(): SocialProviderResponse
    {
        $data = [
            'access_token' => $this->accessToken(),
            'metric' => 'page_engaged_users,page_post_engagements,page_posts_impressions',
            'period' => 'day',
            'since' => Carbon::today('UTC')->subDays(90)->toDateString(),
            'until' => Carbon::today('UTC')->toDateString(),
        ];

        $response = $this->getHttpClient()::get("$this->apiUrl/$this->apiVersion/{$this->values['provider_id']}/insights", $data);

        return $this->buildResponse($response);
    }

    public function getPosts(): SocialProviderResponse
    {
        $data = [
            'access_token' => $this->accessToken(),
            'limit' => 100,
            'period' => 'day',
            'since' => Carbon::today('UTC')->subDays(90)->toDateString(),
            'until' => Carbon::today('UTC')->toDateString(),
        ];

        $response = $this->getHttpClient()::get("$this->apiUrl/$this->apiVersion/{$this->values['provider_id']}/feed", $data);

        return $this->buildResponse($response);
    }

    public function getStories(): SocialProviderResponse
    {
        $result = $this->getHttpClient()::get("$this->apiUrl/$this->apiVersion/{$this->values['provider_id']}/stories", [
            'access_token' => $this->accessToken()
        ]);

        return $this->buildResponse($result);
    }

    public function deletePost($id): SocialProviderResponse
    {
        return $this->response(SocialProviderResponseStatus::OK, []);
    }
}
