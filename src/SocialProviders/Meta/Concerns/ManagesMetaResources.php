<?php

namespace SaguiAi\MixpostAdapter\SocialProviders\Meta\Concerns;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use SaguiAi\MixpostAdapter\Enums\SocialProviderResponseStatus;
use SaguiAi\MixpostAdapter\Models\Media;
use SaguiAi\MixpostAdapter\SocialProviders\Meta\Support\FacebookVideoErrorCodes;
use SaguiAi\MixpostAdapter\Support\SocialProviderResponse;
use SaguiAi\MixpostAdapter\Util;

trait ManagesMetaResources
{
    public function getUserAccount(): SocialProviderResponse
    {
        $response = Http::get("$this->apiUrl/$this->apiVersion/me", [
            'fields' => 'id,name',
            'access_token' => $this->getAccessToken()['access_token']
        ]);

        return $this->buildResponse($response, function () use ($response) {
            $data = $response->json();

            return [
                'id' => $data['id'],
                'name' => $data['name'],
                'username' => '',
                'image' => $this->apiUrl . '/' . $this->apiVersion . '/' . $data['id'] . '/picture?normal',
            ];
        });
    }

    public function getAccount(): SocialProviderResponse
    {
        return $this->getUserAccount();
    }

    public function deletePost($id): SocialProviderResponse
    {
        return $this->response(SocialProviderResponseStatus::OK, []);
    }

    public function publishPost(string $text, Collection $media, array $params = []): SocialProviderResponse
    {
        return $this->response(SocialProviderResponseStatus::NO_CONTENT, []);
    }

    public function uploadPhoto(Media $mediaItem): SocialProviderResponse
    {
        $readStream = $mediaItem->readStream();

        $response = $this->getHttpClient()::attach('source', $readStream['stream'])
            ->post("$this->apiUrl/$this->apiVersion/{$this->values['provider_id']}/photos", [
                'published' => false,
                'access_token' => $this->accessToken(),
            ]);

        Util::closeAndDeleteStreamResource($readStream);

        return $this->buildResponse($response);
    }

    public function getVideoStatus(string $videoId): SocialProviderResponse
    {
        $result = $this->getHttpClient()::get("$this->apiUrl/$this->apiVersion/$videoId", [
            'fields' => 'status',
            'access_token' => $this->accessToken()
        ]);

        return $this->buildResponse($result);
    }

    public function handleVideoProcessing(string $videoId, int $fileSize, int $maxAttempts = 10): SocialProviderResponse
    {
        $status = function () use ($videoId) {
            $status = $this->getVideoStatus($videoId);

            if ($status->hasError()) {
                return $status;
            }

            if ($status->status['video_status'] === 'error') {
                return $this->response(SocialProviderResponseStatus::ERROR, FacebookVideoErrorCodes::handleResponse($status));
            }

            if ($status->status['video_status'] === 'expired') {
                return $this->response(SocialProviderResponseStatus::ERROR, ['publication_video_expired']);
            }

            if ($status->status['video_status'] !== 'processing') {
                return $status;
            }

            return null;
        };

        $delay = Util::estimateDelayByFileSize($fileSize);

        $result = Util::performTaskWithDelay($status, $delay['initial'], $delay['max'], $maxAttempts);

        if (!$result) {
            return $this->response(SocialProviderResponseStatus::ERROR, ['request_timeout']);
        }

        return $result;
    }
}
