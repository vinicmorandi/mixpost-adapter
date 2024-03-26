<?php

namespace SaguiAi\MixpostAdapter\SocialProviders\Meta\Concerns;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use SaguiAi\MixpostAdapter\Enums\SocialProviderResponseStatus;
use SaguiAi\MixpostAdapter\Models\Media;
use SaguiAi\MixpostAdapter\Support\SocialProviderResponse;
use SaguiAi\MixpostAdapter\Util;

trait FacebookStoryPublication
{
    protected function publishFacebookStory(Collection $media): SocialProviderResponse
    {
        if (!$media->count()) {
            return $this->response(SocialProviderResponseStatus::ERROR, ['story_single_media_limit']);
        }

        $media = $media->first();

        if ($media->isVideo()) {
            $session = $this->startUploadVideoStorySession();

            if ($session->hasError()) {
                return $session;
            }

            $uploadResult = $this->uploadVideoStory($session->upload_url, $media);

            if ($uploadResult->hasError()) {
                return $uploadResult;
            }

            $publishResult = $this->publishVideoStory($session->video_id);

            if ($publishResult->hasError()) {
                return $publishResult;
            }

            $postId = $publishResult->post_id;

            $processingResult = $this->handleVideoProcessing($session->video_id, $media->size);

            if ($processingResult->hasError()) {
                return $processingResult;
            }
        } else {
            $uploadResult = $this->uploadPhoto($media);

            if ($uploadResult->hasError()) {
                return $uploadResult;
            }

            $publishResult = $this->publishPhotoStory($uploadResult->id);

            if ($publishResult->hasError()) {
                return $publishResult;
            }

            $postId = $publishResult->post_id;
        }

        $storiesResponse = $this->getStories();

        if ($storiesResponse->hasError()) {
            return $storiesResponse;
        }

        $story = collect($storiesResponse->data)
            ->firstWhere('post_id', $postId);

        return $this->response(SocialProviderResponseStatus::OK, [
            'id' => $postId,
            'data' => [
                'story' => true,
                'path' => Str::of($story['url'] ?? '')
                    ->after('https://facebook.com/stories/')
                    ->replace('?view_single=1', '')
                    ->__toString()
            ]
        ]);
    }

    private function startUploadVideoStorySession(): SocialProviderResponse
    {
        return $this->buildResponse(
            $this->getHttpClient()::post("$this->apiUrl/$this->apiVersion/{$this->values['provider_id']}/video_stories", [
                'upload_phase' => 'start',
                'access_token' => $this->accessToken()
            ])
        );
    }

    private function uploadVideoStory(string $uploadUrl, Media $media): SocialProviderResponse
    {
        $stream = $media->readStream();

        $upload = function ($timeout) use ($uploadUrl, $stream, $media) {
            return $this->getHttpClient()::withToken($this->accessToken(), 'OAuth')
                ->timeout($timeout)
                ->withHeaders([
                    'offset' => 0,
                    'file_size' => $media->size,
                ])
                ->withBody($stream['stream'], "application/octet-stream")
                ->post($uploadUrl);
        };

        $result = Util::performHttpRequestWithTimeoutRetries($upload, 7 * 60);

        Util::closeAndDeleteStreamResource($stream);

        if (!$result) {
            return $this->response(SocialProviderResponseStatus::ERROR, ['request_timeout']);
        }

        return $this->buildResponse($result);
    }

    private function publishVideoStory(string $videoId): SocialProviderResponse
    {
        $result = $this->getHttpClient()::post("$this->apiUrl/$this->apiVersion/{$this->values['provider_id']}/video_stories", [
            'access_token' => $this->accessToken(),
            'video_id' => $videoId,
            'upload_phase' => 'finish'
        ]);

        return $this->buildResponse($result);
    }

    private function publishPhotoStory(string $photoId): SocialProviderResponse
    {
        return $this->buildResponse(
            $this->getHttpClient()::post("$this->apiUrl/$this->apiVersion/{$this->values['provider_id']}/photo_stories", [
                'photo_id' => $photoId,
                'access_token' => $this->accessToken()
            ])
        );
    }
}
