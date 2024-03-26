<?php

namespace SaguiAi\MixpostAdapter\SocialProviders\TikTok\Concerns;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use SaguiAi\MixpostAdapter\Enums\SocialProviderResponseStatus;
use SaguiAi\MixpostAdapter\Models\Media;
use SaguiAi\MixpostAdapter\SocialProviders\TikTok\Support\UploadFile;
use SaguiAi\MixpostAdapter\Support\SocialProviderResponse;

trait ManagesResources
{
    public string $apiVersion = 'v2';
    public string $apiUrl = 'https://open.tiktokapis.com';

    public function getAccount(): SocialProviderResponse
    {
        $userInfo = $this->getUserInfo();

        if ($userInfo->hasError()) {
            return $userInfo;
        }

        $context = $userInfo->context();

        if (self::isDirectShareType()) {
            $creatorInfo = $this->getCreatorInfo();

            if ($creatorInfo->hasError()) {
                return $creatorInfo;
            }

            $context['username'] = $creatorInfo->username;
            $context['data'] = array_merge($context['data'], Arr::except($creatorInfo->context(), 'username'));
        }

        return $this->response(SocialProviderResponseStatus::OK, $context);
    }

    public function getUserInfo(): SocialProviderResponse
    {
        if ($this->tokenIsAboutToExpire()) {
            $newAccessToken = $this->refreshToken();

            if ($newAccessToken->hasError()) {
                return $newAccessToken;
            }

            $this->updateToken($newAccessToken->context());
        }

        $response = $this->getHttpClient()::withToken($this->getAccessToken()['access_token'])
            ->get("$this->apiUrl/$this->apiVersion/user/info/", [
                'fields' => 'open_id,union_id,avatar_url,display_name'
            ]);

        return $this->buildResponse($response, function () use ($response) {
            $data = $response->json()['data'];

            return [
                'id' => $data['user']['open_id'],
                'name' => $data['user']['display_name'],
                'username' => '',
                'image' => $data['user']['avatar_url'],
                'data' => [
                    'union_id' => $data['user']['union_id']
                ]
            ];
        });
    }

    public function getCreatorInfo(): SocialProviderResponse
    {
        if ($this->tokenIsAboutToExpire()) {
            $newAccessToken = $this->refreshToken();

            if ($newAccessToken->hasError()) {
                return $newAccessToken;
            }

            $this->updateToken($newAccessToken->context());
        }

        $response = $this->getHttpClient()::withToken($this->getAccessToken()['access_token'])
            ->withBody('', 'application/json')
            ->post("$this->apiUrl/$this->apiVersion/post/publish/creator_info/query/");

        return $this->buildResponse($response, function () use ($response) {
            $data = $response->json('data');

            return [
                'is_private' => !in_array('PUBLIC_TO_EVERYONE', Arr::wrap($data['privacy_level_options'])),
                'username' => $data['creator_username'],
                'privacy_levels' => $data['privacy_level_options'],
                'comment_disabled' => $data['comment_disabled'],
                'duet_disabled' => $data['duet_disabled'],
                'stitch_disabled' => $data['stitch_disabled'],
                'max_video_post_duration_sec' => $data['max_video_post_duration_sec'],
            ];
        });
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
                ->get("$this->apiUrl/$this->apiVersion/user/info/", [
                    'fields' => 'follower_count,following_count,likes_count,video_count'
                ])
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
            return $this->response(SocialProviderResponseStatus::ERROR, ['video_not_selected']);
        }

        /** @var $mediaItem Media * */
        $mediaItem = $media->first();

        if (!$mediaItem->isVideo()) {
            return $this->response(SocialProviderResponseStatus::ERROR, ['supports_only_videos']);
        }

        if (self::isDirectShareType()) {
            $params['privacy_level'] = Arr::get($params, "privacy_level.account-{$this->values['account_id']}");
            $params['allow_comments'] = Arr::get($params, "allow_comments.account-{$this->values['account_id']}");
            $params['allow_duet'] = Arr::get($params, "allow_duet.account-{$this->values['account_id']}");
            $params['allow_stitch'] = Arr::get($params, "allow_stitch.account-{$this->values['account_id']}");
            $params['brand_content_toggle'] = Arr::get($params, "brand_content_toggle", false);
            $params['brand_organic_toggle'] = Arr::get($params, "brand_organic_toggle", false);

            $postInfo = [
                'title' => $text,
                'privacy_level' => $params['privacy_level'],
                'disable_comment' => !$params['allow_comments'],
                'disable_duet' => !$params['allow_duet'],
                'disable_stitch' => !$params['allow_stitch'],
                'brand_content_toggle' => $params['brand_content_toggle'],
                'brand_organic_toggle' => $params['brand_organic_toggle'],
                'video_cover_timestamp_ms' => 0,
            ];
        }

        $uploadFile = new UploadFile(
            $mediaItem,
            $postInfo ?? null,
            $this->getHttpClient(),
            $this->apiVersion,
            $this->apiUrl,
            ($this->getAccessToken()['access_token'] ?? '')
        );

        $uploadResponse = $uploadFile->upload();

        return $uploadResponse;

        // TODO: Check video status/ Webhook
        // https://developers.tiktok.com/doc/content-posting-api-reference-get-video-status/

//        if (self::isInboxShareType()) {
//            return $uploadResponse;
//        }
//
//        if ($uploadResponse->hasError()) {
//            return $uploadResponse;
//        }

//        do {
//            sleep(10);
//
//            $statusResponse = $this->getVideoStatus($uploadResponse->data['publish_id']);

//            if ($statusResponse->hasError()) {
//                return $statusResponse;
//            }
//
//        } while ($statusResponse->data['status'] === 'PROCESSING_UPLOAD');
//
//        if ($statusResponse->data['status'] === 'FAILED') {
//            return $this->response(SocialProviderResponseStatus::ERROR, $statusResponse->data);
//        }
//
//        return $this->response(SocialProviderResponseStatus::OK, [
//            'id' => Arr::first(Arr::wrap($statusResponse->data['publicaly_available_post_id']))
//        ]);
    }

    public function getVideoStatus(string $publishId): SocialProviderResponse
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
                ->asJson()
                ->post("$this->apiUrl/$this->apiVersion/post/publish/status/fetch/", [
                    'publish_id' => $publishId
                ])
        );
    }

    public function deletePost($id): SocialProviderResponse
    {
        return $this->buildResponse(
            $this->getHttpClient()::withToken($this->getAccessToken()['access_token'])
                ->delete("$this->apiUrl/$this->apiVersion/$id")
        );
    }

    public function getVideos(?string $cursor = null): SocialProviderResponse
    {
        if ($this->tokenIsAboutToExpire()) {
            $newAccessToken = $this->refreshToken();

            if ($newAccessToken->hasError()) {
                return $newAccessToken;
            }

            $this->updateToken($newAccessToken->context());
        }

        $data = $cursor ? [
            'cursor' => $cursor
        ] : [];

        return $this->buildResponse(
            $this->getHttpClient()::withToken($this->getAccessToken()['access_token'])
                ->post("$this->apiUrl/$this->apiVersion/video/list/?fields=id,title,create_time,cover_image_url,share_url,video_description,like_count,comment_count,share_count,view_count", array_merge([
                    'max_count' => 20,
                ], $data))
        );
    }
}
