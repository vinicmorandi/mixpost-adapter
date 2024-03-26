<?php

namespace SaguiAi\MixpostAdapter\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use SaguiAi\MixpostAdapter\Models\Media;
use SaguiAi\MixpostAdapter\Util;

class PostVersionResource extends JsonResource
{
    public static $wrap = null;

    public function toArray($request)
    {
        return [
            'post_id' => $this->post_id,
            'account_id' => $this->account_id,
            'is_original' => $this->is_original,
            'content' => $this->content(),
            'options' => $this->options()
        ];
    }

    protected function isIndexPage(): bool
    {
        return request()->route()->getName() === 'mixpost.posts.index';
    }

    protected function isCalendarPage(): bool
    {
        return request()->route()->getName() === 'mixpost.calendar';
    }

    protected function content(): Collection
    {
        $mediaCollection = $this->mediaCollection();

        return collect($this->content)->map(function ($item) use ($mediaCollection) {
            $data = [
                'body' => (string)$item['body'],
                'media' => collect($item['media'])->map(function ($mediaId) use ($mediaCollection) {
                    $media = $mediaCollection->where('id', $mediaId)->first();

                    if (!$media) {
                        return null;
                    }

                    return new MediaResource($media);
                })->filter()->values()
            ];

            if ($this->isIndexPage()) {
                $data['excerpt'] = Str::limit(Util::removeHtmlTags($item['body']), 150);
            }

            if ($this->isCalendarPage()) {
                $data['excerpt'] = Str::limit(Util::removeHtmlTags($item['body']), 50);
            }

            return $data;
        });
    }

    protected function options(): array
    {
        return [
            'mastodon' => [
                'sensitive' => Arr::get($this->options, 'mastodon.sensitive', false)
            ],
            'facebook_page' => [
                'type' => Arr::get($this->options, 'facebook_page.type', 'post')
            ],
            'instagram' => [
                'type' => Arr::get($this->options, 'instagram.type', 'post')
            ],
            'youtube' => [
                'title' => Arr::get($this->options, 'youtube.title', ''),
                'status' => Arr::get($this->options, 'youtube.status', 'public')
            ],
            'pinterest' => [
                'title' => Arr::get($this->options, 'pinterest.title', ''),
                'link' => Arr::get($this->options, 'pinterest.link', ''),
                'boards' => Arr::get($this->options, 'pinterest.boards', ['account-0' => null])
            ],
            'linkedin' => [
                'visibility' => Arr::get($this->options, 'linkedin.visibility', 'PUBLIC'),
            ],
            'tiktok' => [
                'privacy_level' => Arr::get($this->options, 'tiktok.privacy_level', ['account-0' => '']),
                'allow_comments' => Arr::get($this->options, 'tiktok.allow_comments', ['account-0' => false]),
                'allow_duet' => Arr::get($this->options, 'tiktok.allow_duet', ['account-0' => false]),
                'allow_stitch' => Arr::get($this->options, 'tiktok.allow_stitch', ['account-0' => false]),
                'brand_content_toggle' => Arr::get($this->options, 'tiktok.brand_content_toggle', false),
                'brand_organic_toggle' => Arr::get($this->options, 'tiktok.brand_organic_toggle', false),
            ]
        ];
    }

    protected function mediaCollection()
    {
        $mediaIds = [];

        foreach ($this->content as $item) {
            $mediaIds = array_merge($mediaIds, $item['media']);
        }

        return Media::whereIn('id', $mediaIds)->get();
    }
}
