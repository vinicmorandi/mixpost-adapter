<?php

namespace SaguiAi\MixpostAdapter\SocialProviders\Meta\Concerns;

use Illuminate\Support\Collection;
use SaguiAi\MixpostAdapter\Enums\SocialProviderResponseStatus;
use SaguiAi\MixpostAdapter\Models\Media;
use SaguiAi\MixpostAdapter\Support\SocialProviderResponse;
use SaguiAi\MixpostAdapter\Util;

trait FacebookReelPublication
{
    protected function publishFacebookReel(string $text, Collection $media): SocialProviderResponse
    {
        if (!$media->count() || !$media->first()->isVideo()) {
            return $this->response(SocialProviderResponseStatus::ERROR, ['reel_supports_one_video']);
        }

        $session = $this->startUploadReelSession();

        if ($session->hasError()) {
            return $session;
        }

        $uploadResult = $this->uploadReel($session->upload_url, $media->first());

        if ($uploadResult->hasError()) {
            return $uploadResult;
        }

        $publishResult = $this->publishReel($session->video_id, $text);

        if ($publishResult->hasError()) {
            return $publishResult;
        }

        $processingResult = $this->handleVideoProcessing($session->video_id, $media->first()->size);

        if ($processingResult->hasError()) {
            return $processingResult;
        }

        return $processingResult->useContext([
            'id' => $processingResult->id,
            'data' => [
                'reel' => true,
            ]
        ]);
    }

    private function startUploadReelSession(): SocialProviderResponse
    {
        return $this->buildResponse(
            $this->getHttpClient()::post("$this->apiUrl/$this->apiVersion/{$this->values['provider_id']}/video_reels", [
                'upload_phase' => 'start',
                'access_token' => $this->accessToken()
            ])
        );
    }

    private function uploadReel(string $uploadUrl, Media $media): SocialProviderResponse
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

    private function publishReel(string $videoId, string $text): SocialProviderResponse
    {
        $result = $this->getHttpClient()::post("$this->apiUrl/$this->apiVersion/{$this->values['provider_id']}/video_reels", [
            'access_token' => $this->accessToken(),
            'video_id' => $videoId,
            'upload_phase' => 'finish',
            'video_state' => 'PUBLISHED',
            'description' => $text,
        ]);

        return $this->buildResponse($result);
    }
}
