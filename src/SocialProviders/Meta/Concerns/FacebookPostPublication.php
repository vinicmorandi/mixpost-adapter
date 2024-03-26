<?php

namespace SaguiAi\MixpostAdapter\SocialProviders\Meta\Concerns;

use Illuminate\Support\Collection;
use SaguiAi\MixpostAdapter\Enums\SocialProviderResponseStatus;
use SaguiAi\MixpostAdapter\Models\Media;
use SaguiAi\MixpostAdapter\Support\SocialProviderResponse;

trait FacebookPostPublication
{
    protected function publishFacebookStandardPost(string $text, Collection $media): SocialProviderResponse
    {
        $isVideo = $media->count() === 1 && $media->first()->isVideo();

        if (!$isVideo) {
            return $this->publishFacebookStandardPostDefault($text, $media);
        }

        return $this->publishFacebookStandardPostVideo($text, $media);
    }

    protected function publishFacebookStandardPostDefault(string $text, Collection $media): SocialProviderResponse
    {
        $uploadPhotos = $this->uploadPhotos($media);

        if ($uploadPhotos instanceof SocialProviderResponse) {
            return $uploadPhotos;
        }

        $params = [
            'message' => $text,
            'access_token' => $this->accessToken()
        ];

        // `attached_media` = support only images
        if (!empty($uploadPhotos)) {
            $params['attached_media'] = $uploadPhotos;
        }

        $response = $this->getHttpClient()::post("$this->apiUrl/$this->apiVersion/{$this->values['provider_id']}/feed", $params);

        return $this->buildResponse($response, function () use ($response) {
            return [
                'id' => $response->json()['id']
            ];
        });
    }

    protected function publishFacebookStandardPostVideo(string $text, Collection $media): SocialProviderResponse
    {
        $thumbReadStream = $media->first()->readStream('thumb');

        $meta = [
            'description' => $text,
            'thumb' => $thumbReadStream['stream']
        ];

        $response = $this->uploadVideoPost($media->first(), $meta);

        if (is_resource($thumbReadStream['stream'])) {
            fclose($thumbReadStream['stream']);
        }

        $thumbReadStream['temporaryDirectory']?->delete();

        return $response;
    }

    protected function uploadVideoPost(Media $mediaItem, array $meta): SocialProviderResponse
    {
        // Start
        $session = $this->buildResponse($this->getHttpClient()::post("$this->apiUrl/$this->apiVersion/{$this->values['provider_id']}/videos", [
            'upload_phase' => 'start',
            'file_size' => $mediaItem->size,
            'access_token' => $this->accessToken()
        ]));

        if ($session->hasError()) {
            return $session;
        }

        // Upload chunk
        $uploadSessionId = $session->context()['upload_session_id'];
        $startOffset = $session->context()['start_offset'];
        $endOffset = $session->context()['end_offset'];

        $readStream = $mediaItem->readStream();

        do {
            $partialFile = stream_get_contents($readStream['stream'], ($endOffset - $startOffset), $startOffset);

            $chunkResponse = $this->buildResponse($this->getHttpClient()::attach('video_file_chunk', $partialFile, $mediaItem->name, [
                'Content-Type' => $mediaItem->mime_type
            ])
                ->post("$this->apiUrl/$this->apiVersion/{$this->values['provider_id']}/videos", [
                    'upload_phase' => 'transfer',
                    'upload_session_id' => $uploadSessionId,
                    'start_offset' => $startOffset,
                    'access_token' => $this->accessToken()
                ]));

            if ($chunkResponse->hasError()) {
                if (is_resource($readStream['stream'])) {
                    fclose($readStream['stream']);
                }

                $readStream['temporaryDirectory']?->delete();

                return $chunkResponse;
            }

            $startOffset = $chunkResponse->context()['start_offset'];
            $endOffset = $chunkResponse->context()['end_offset'];
        } while ($startOffset !== $endOffset);

        if (is_resource($readStream['stream'])) {
            fclose($readStream['stream']);
        }

        $readStream['temporaryDirectory']?->delete();

        // Finish
        $finish = $this->buildResponse($this->getHttpClient()::asMultipart()->post("$this->apiUrl/$this->apiVersion/{$this->values['provider_id']}/videos", array_merge([
            'upload_phase' => 'finish',
            'upload_session_id' => $uploadSessionId,
            'access_token' => $this->accessToken()
        ], $meta)));

        if ($finish->hasError()) {
            return $finish;
        }

        if (!$finish->context()['success']) {
            return new SocialProviderResponse(SocialProviderResponseStatus::ERROR, ['error_upload_video']);
        }

        return new SocialProviderResponse(SocialProviderResponseStatus::OK, ['id' => $session->context()['video_id']]);
    }

    protected function uploadPhotos(Collection $media): array|SocialProviderResponse
    {
        $ids = [];

        foreach ($media as $item) {
            if ($item->isImage()) {
                $result = $this->uploadPhoto($item);

                if ($result->hasExceededRateLimit()) {
                    return $result;
                }

                if ($id = $result->id) {
                    $ids[] = ['media_fbid' => $id];
                }
            }
        }

        return $ids;
    }
}
