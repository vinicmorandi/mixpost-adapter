<?php

namespace SaguiAi\MixpostAdapter\SocialProviders\Linkedin\Concerns;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use SaguiAi\MixpostAdapter\Models\Media;
use SaguiAi\MixpostAdapter\Support\SocialProviderResponse;
use SaguiAi\MixpostAdapter\Util;

trait ManagesResources
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

        if (self::hasSignInOpenIdShareProduct()) {
            $response = $this->getHttpClient()::withToken($this->getAccessToken()['access_token'])
                ->withHeaders($this->httpHeaders())
                ->get("$this->apiUrl/$this->apiVersion/userinfo");

            return $this->buildResponse($response, function () use ($response) {
                $data = $response->json();

                return [
                    'id' => $data['sub'],
                    'name' => $data['name'],
                    'username' => '',
                    'image' => $data['picture'] ?? ''
                ];
            });
        }

        $response = $this->getHttpClient()::withToken($this->getAccessToken()['access_token'])
            ->withHeaders($this->httpHeaders())
            ->get("$this->apiUrl/$this->apiVersion/me", [
                'projection' => '(id,localizedFirstName,localizedLastName,vanityName,profilePicture(displayImage~:playableStreams))'
            ]);

        return $this->buildResponse($response, function () use ($response) {
            $data = $response->json();

            return [
                'id' => $data['id'],
                'name' => "{$data['localizedFirstName']} {$data['localizedLastName']}",
                'username' => $data['vanityName'] ?? '',
                'image' => Arr::get($data, 'profilePicture.displayImage~.elements.0.identifiers.0.identifier')
            ];
        });
    }

    public function publishPost(string $text, Collection $media, array $params = []): SocialProviderResponse
    {
        if ($this->hasRefreshToken() && $this->tokenIsAboutToExpire()) {
            $newAccessToken = $this->refreshToken();

            if ($newAccessToken->hasError()) {
                return $newAccessToken;
            }

            $this->updateToken($newAccessToken->context());
        }

        $specificContent = [
            'shareMediaCategory' => 'NONE'
        ];

        if ($media->count()) {
            $uploadedMedia = [];

            foreach ($media as $mediaItem) {
                $mediaResponse = $this->uploadMedia($mediaItem, $mediaItem->isVideo() ? 'video' : 'image');

                if ($mediaResponse->hasError()) {
                    return $mediaResponse;
                }

                $uploadedMedia[] = [
                    'media' => $mediaResponse->value['asset'],
                    'status' => 'READY'
                ];
            }

            $specificContent = [
                'shareMediaCategory' => $media->first()->isVideo() ? 'VIDEO' : 'IMAGE',
                'media' => $uploadedMedia
            ];
        }

        $response = $this->getHttpClient()::withToken($this->getAccessToken()['access_token'])
            ->withHeaders($this->httpHeaders())
            ->post("$this->apiUrl/$this->apiVersion/ugcPosts", [
                'author' => "urn:li:{$this->author()}:{$this->values['provider_id']}",
                'lifecycleState' => 'PUBLISHED',
                'specificContent' => [
                    'com.linkedin.ugc.ShareContent' => array_merge([
                        'shareCommentary' => [
                            'text' => $text,
                        ]
                    ], $specificContent)
                ],
                'visibility' => [
                    'com.linkedin.ugc.MemberNetworkVisibility' => Str::upper(Arr::get($params, 'visibility', 'PUBLIC'))
                ],
            ]);

        return $this->buildResponse($response, function () use ($response) {
            return [
                'id' => $response->header('X-RestLi-Id')
            ];
        });
    }

    public function uploadMedia(Media $mediaItem, string $type = 'image'): SocialProviderResponse
    {
        $registerResponse = $this->buildResponse(
            $this->getHttpClient()::withToken($this->getAccessToken()['access_token'])
                ->withHeaders($this->httpHeaders())
                ->post("$this->apiUrl/$this->apiVersion/assets?action=registerUpload", [
                    "registerUploadRequest" => [
                        "owner" => "urn:li:{$this->author()}:{$this->values['provider_id']}",
                        "recipes" => [
                            "urn:li:digitalmediaRecipe:feedshare-$type"
                        ],
                        "serviceRelationships" => [
                            [
                                "identifier" => "urn:li:userGeneratedContent",
                                "relationshipType" => "OWNER"
                            ]
                        ]
                    ]
                ])
        );

        if ($registerResponse->hasError()) {
            return $registerResponse;
        }

        $uploadUrl = $registerResponse->value['uploadMechanism']['com.linkedin.digitalmedia.uploading.MediaUploadHttpRequest']['uploadUrl'];
        $stream = $mediaItem->readStream();

        $upload = function ($timeout) use ($uploadUrl, $stream) {
            return $this->getHttpClient()::withToken($this->getAccessToken()['access_token'])
                ->timeout($timeout)
                ->withHeaders($this->httpHeaders())
                ->withBody($stream['stream'], "application/octet-stream")
                ->post($uploadUrl);
        };

        $result = Util::performHttpRequestWithTimeoutRetries($upload,  $mediaItem->isVideo() ? 7 * 60 : 60);

        Util::closeAndDeleteStreamResource($stream);

        return $this->buildResponse($result, function () use ($registerResponse) {
            return $registerResponse->context();
        });
    }

    public function deletePost($id): SocialProviderResponse
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
            ->delete("$this->apiUrl/$this->apiVersion/ugcPosts/$id");

        return $this->buildResponse($response);
    }

    private function author(): string
    {
        return $this->values['provider'] === 'linkedin_page' ? 'organization' : 'person';
    }
}
