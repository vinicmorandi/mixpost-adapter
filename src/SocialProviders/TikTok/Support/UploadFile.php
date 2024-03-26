<?php

namespace SaguiAi\MixpostAdapter\SocialProviders\TikTok\Support;

use SaguiAi\MixpostAdapter\Concerns\UsesSocialProviderResponse;
use SaguiAi\MixpostAdapter\Enums\SocialProviderResponseStatus;
use SaguiAi\MixpostAdapter\Models\Media;
use SaguiAi\MixpostAdapter\SocialProviders\TikTok\Concerns\ManagesRateLimit;
use Illuminate\Support\Facades\Http;
use SaguiAi\MixpostAdapter\Support\SocialProviderResponse;

class UploadFile
{
    use UsesSocialProviderResponse;
    use ManagesRateLimit;

    private int $minChunkSize;
    private int $maxChunkSize;
    private int $maxFinalChunkSize;
    private int $totalChunks;

    private SocialProviderResponse $initUploadResponse;

    public function __construct(
        private readonly Media  $media,
        private readonly ?array $postInfo,
        private readonly Http   $httpClient,
        private readonly string $apiVersion,
        private readonly string $apiUrl,
        private readonly string $accessToken,
    )
    {
        $this->minChunkSize = 5 * 1024 * 1024; // 5 MB
        $this->maxChunkSize = 64 * 1024 * 1024; // 64 MB
        $this->maxFinalChunkSize = 128 * 1024 * 1024; // 128 MB
        $this->totalChunks = min(ceil($this->media->size / $this->maxChunkSize), 1000);
    }

    public function upload(): SocialProviderResponse
    {
        if ($this->totalChunks > 1) {
            return $this->response(SocialProviderResponseStatus::ERROR, [
                'error' => 'Temporarily, videos larger than 64Mb cannot be uploaded to the TikTok server.'
            ]);
        }

        $data['source_info'] = [
            'source' => 'FILE_UPLOAD',
            'video_size' => $this->media->size,
            'chunk_size' => (int)floor($this->media->size / $this->totalChunks),
            'total_chunk_count' => $this->totalChunks
        ];

        // Direct Post
        if ($this->postInfo) {
            $data['post_info'] = $this->postInfo;
        }

        $initUploadResponse = $this->buildResponse(
            $this->httpClient::withToken($this->accessToken)
                ->asJson()
                ->post("$this->apiUrl/$this->apiVersion/post/publish/" . ($this->postInfo ? 'video' : 'inbox/video') . "/init/", $data)
        );

        if ($initUploadResponse->hasError()) {
            return $initUploadResponse;
        }

        $this->initUploadResponse = $initUploadResponse;

        // Upload the whole video as a single chunk
        if ($this->totalChunks === 1) {
            $response = $this->uploadChunk(0, $this->media->size - 1, $this->media->size);

            if ($response->hasError()) {
                return $response;
            }
        }

        // Upload the video in chunks
        if ($this->totalChunks > 1) {
            $chunkSize = min(ceil($this->media->size / $this->totalChunks), $this->maxChunkSize);

            for ($chunk = 0; $chunk < $this->totalChunks; $chunk++) {
                $firstByte = $chunk * $chunkSize;
                $lastByte = min(($chunk + 1) * $chunkSize - 1, $this->media->size - 1);
                $byteSizeOfChunk = $lastByte - $firstByte + 1;

                $response = $this->uploadChunk((int)$firstByte, (int)$lastByte, (int)$byteSizeOfChunk);

                if ($response->hasError()) {
                    return $response;
                }
            }
        }

        return $this->response(SocialProviderResponseStatus::OK, [
            'data' => [
                'publish_id' => $this->initUploadResponse->data['publish_id']
            ]
        ]);
    }

    private function uploadChunk($firstByte, $lastByte, $byteSizeOfChunk): SocialProviderResponse
    {
        $contentRange = "bytes $firstByte-$lastByte/{$this->media->size}";
        $readStream = $this->media->readStream();

        $binaryFileData = stream_get_contents($readStream['stream'], $byteSizeOfChunk, $firstByte);

        $response = $this->httpClient::timeout(300)
            ->withHeaders([
                'Content-Range' => $contentRange,
                'Content-Length' => $byteSizeOfChunk,
                'Content-Type' => $this->media->mime_type
            ])
            ->withBody($binaryFileData, $this->media->mime_type)
            ->put($this->initUploadResponse->data['upload_url']);

        if (is_resource($readStream['stream'])) {
            fclose($readStream['stream']);
        }

        $readStream['temporaryDirectory']?->delete();

        return $this->buildResponse($response);
    }
}
