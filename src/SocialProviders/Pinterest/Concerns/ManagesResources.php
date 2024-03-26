<?php

namespace SaguiAi\MixpostAdapter\SocialProviders\Pinterest\Concerns;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use SaguiAi\MixpostAdapter\Enums\SocialProviderResponseStatus;
use SaguiAi\MixpostAdapter\Support\SocialProviderResponse;

trait ManagesResources
{
    public function getAccount(): SocialProviderResponse
    {
        if ($this->tokenIsAboutToExpire()) {
            $newAccessToken = $this->refreshToken();

            if ($newAccessToken->hasError()) {
                return $newAccessToken;
            }

            $this->updateToken($newAccessToken->context());
        }

        $response = $this->buildResponse(
            $this->getHttpClient()::withToken($this->getAccessToken()['access_token'])
                ->get("{$this->getApiUrl()}/$this->apiVersion/user_account")
        );

        if ($response->hasError()) {
            return $response;
        }

        $boards = $this->getBoards();

        $relationships = [];

        if (!$boards->hasError()) {
            $relationships['boards'] = Arr::map($boards->items ?? [], function ($item) {
                return [
                    'id' => $item['id'],
                    'name' => $item['name']
                ];
            });
        }

        return $this->response(SocialProviderResponseStatus::OK, [
            'id' => $response->username,
            'name' => $response->business_name ?? $response->username,
            'username' => $response->username,
            'image' => $response->profile_image,
            'data' => [
                'relationships' => $relationships
            ]
        ]);
    }

    public function getBoards(): SocialProviderResponse
    {
        if ($this->tokenIsAboutToExpire()) {
            $newAccessToken = $this->refreshToken();

            if ($newAccessToken->hasError()) {
                return $newAccessToken;
            }

            $this->updateToken($newAccessToken->context());
        }

        $token = $this->getAccessToken()['access_token'];

        $response = $this->getHttpClient()::withToken($token)->get("{$this->getApiUrl()}/$this->apiVersion/boards");

        return $this->buildResponse($response);
    }

    public function getAccountMetrics(): SocialProviderResponse
    {
        if ($this->tokenIsAboutToExpire()) {
            $newAccessToken = $this->refreshToken();

            if ($newAccessToken->hasError()) {
                return $newAccessToken;
            }

            $this->updateToken($newAccessToken->context());
        }

        return $this->buildResponse(
            $this->getHttpClient()::withToken($this->getAccessToken()['access_token'])
                ->get("{$this->getApiUrl()}/$this->apiVersion/user_account")
        );
    }

    public function publishPost(string $text, Collection $media, array $params = []): SocialProviderResponse
    {
        if ($this->tokenIsAboutToExpire()) {
            $newAccessToken = $this->refreshToken();

            if ($newAccessToken->hasError()) {
                return $newAccessToken;
            }

            $this->updateToken($newAccessToken->context());
        }

        if (!$media->count()) {
            return $this->response(SocialProviderResponseStatus::ERROR, ['no_media_selected']);
        }

        // Get board id only for the current account
        $params['board_id'] = Arr::get($params, "boards.account-{$this->values['account_id']}");

        $isVideo = $media->count() === 1 && $media->first()->isVideo();

        if (!$isVideo) {
            $mediaData = [
                'media_source' => [
                    'source_type' => 'image_base64',
                    'content_type' => 'image/jpeg',
                    'data' => base64_encode($media->first()->contents())
                ]
            ];
        }

        if ($isVideo) {
            // TODO: pinterest upload video
            return $this->response(SocialProviderResponseStatus::ERROR, ['not_support_video']);

//            $uploadVideoResponse = $this->uploadVideo($media[0]);
//
//            if ($uploadVideoResponse->hasError()) {
//                return $uploadVideoResponse;
//            }
//
//            $mediaData = [
//                'media_upload_id' => $uploadVideoResponse->id
//            ];
        }

        $response = $this->getHttpClient()::withToken($this->getAccessToken()['access_token'])
            ->post("{$this->getApiUrl()}/$this->apiVersion/pins", array_merge([
                'link' => $params['link'],
                'title' => $params['title'],
                'board_id' => $params['board_id'],
                'description' => $text,
            ], $mediaData));

        return $this->buildResponse($response, function () use ($response) {
            $data = $response->json();

            return [
                'id' => $data['id']
            ];
        });
    }

    public function uploadVideo(array $media): SocialProviderResponse
    {
        if ($this->tokenIsAboutToExpire()) {
            $newAccessToken = $this->refreshToken();

            if ($newAccessToken->hasError()) {
                return $newAccessToken;
            }

            $this->updateToken($newAccessToken->context());
        }

        $response = $this->buildResponse(
            $this->getHttpClient()::withToken($this->getAccessToken()['access_token'])
                ->post("{$this->getApiUrl()}/$this->apiVersion/media", [
                    'media_type' => 'video',
                ])
        );

        if ($response->hasError()) {
            return $response;
        }

        $stream = fopen($media['path'], 'r');
        $uploadSpeed = 2 * 1024 * 1024; // 2MB/s
//        $estimatedUploadTime = filesize($media['path']) / $uploadSpeed;

        $responseUpload = $this->getHttpClient()::timeout(100)
            ->attach('file', $stream)
            ->post($response->upload_url, $response->upload_parameters);

        if ($responseUpload->status() !== 204) {
            return $this->response(SocialProviderResponseStatus::ERROR, ['video_upload_failed']);
        }

        return $this->response(SocialProviderResponseStatus::OK, ['id' => $response->media_id]);
    }

    public function getPins(string $bookmark = ''): SocialProviderResponse
    {
        if ($this->tokenIsAboutToExpire()) {
            $newAccessToken = $this->refreshToken();

            if ($newAccessToken->hasError()) {
                return $newAccessToken;
            }

            $this->updateToken($newAccessToken->context());
        }

        $data = [
            'pin_filter' => 'exclude_repins',
            'page_size' => 100,
        ];

        if ($bookmark) {
            $data['bookmark'] = $bookmark;
        }

        return $this->buildResponse(
            $this->getHttpClient()::withToken($this->getAccessToken()['access_token'])
                ->get("{$this->getApiUrl()}/$this->apiVersion/pins", $data)
        );
    }

    public function getPinAnalytics(string $id, array $query): SocialProviderResponse
    {
        if ($this->tokenIsAboutToExpire()) {
            $newAccessToken = $this->refreshToken();

            if ($newAccessToken->hasError()) {
                return $newAccessToken;
            }

            $this->updateToken($newAccessToken->context());
        }

        return $this->buildResponse(
            $this->getHttpClient()::withToken($this->getAccessToken()['access_token'])
                ->get("{$this->getApiUrl()}/$this->apiVersion/pins/$id/analytics", $query)
        );
    }

    public function deletePost($id): SocialProviderResponse
    {
        if ($this->tokenIsAboutToExpire()) {
            $newAccessToken = $this->refreshToken();

            if ($newAccessToken->hasError()) {
                return $newAccessToken;
            }

            $this->updateToken($newAccessToken->context());
        }

        $token = $this->getAccessToken()['access_token'];

        $response = $this->getHttpClient()::withToken($token)->delete("{$this->getApiUrl()}/$this->apiVersion/pins/$id");

        return $this->buildResponse($response);
    }

    public function createBoard(string $name): SocialProviderResponse
    {
        if ($this->tokenIsAboutToExpire()) {
            $newAccessToken = $this->refreshToken();

            if ($newAccessToken->hasError()) {
                return $newAccessToken;
            }

            $this->updateToken($newAccessToken->context());
        }

        $token = $this->getAccessToken()['access_token'];

        return $this->buildResponse(
            $this->getHttpClient()::withToken($token)
                ->post("{$this->getApiUrl()}/$this->apiVersion/boards", [
                    'name' => $name,
                    'privacy' => 'PUBLIC',
                ])
        );
    }
}
