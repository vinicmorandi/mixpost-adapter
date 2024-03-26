<?php

namespace SaguiAi\MixpostAdapter\Support;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use SaguiAi\MixpostAdapter\Models\Account;
use SaguiAi\MixpostAdapter\Models\Media;
use SaguiAi\MixpostAdapter\Models\Post;
use SaguiAi\MixpostAdapter\Models\Variable;

class PostContentParser
{
    public function __construct(
        private readonly Account $account,
        private readonly Post    $post
    )
    {
    }

    public function getVersionContent(): array
    {
        $accountVersion = $this->post->versions->where('account_id', $this->account->id)->first();

        if (empty($accountVersion)) {
            return $this->post->versions->where('is_original', true)->first()->content ?: [];
        }

        return $accountVersion->content;
    }

    public function getVersionOptions(): array
    {
        $accountVersion = $this->post->versions->where('account_id', $this->account->id)->first();

        if (empty($accountVersion)) {
            $original = $this->post->versions->where('is_original', true)->first();

            return Arr::get($original->options ?? [], $this->account->provider, []);
        }

        return Arr::get($accountVersion->options ?? [], $this->account->provider, []);
    }

    public function formatBody(?string $text): string
    {
        if (!$text) {
            return '';
        }

        $replaceDiv = str_replace(["<div>", "</div>"], ["", "\n"], $text);

        $decode = html_entity_decode($replaceDiv);
        $stripTags = strip_tags($decode);

        $variables = Variable::pluck('value', 'name')->toArray();

        $variables['platform'] = match ($this->account->provider) {
            'facebook_page' => 'Facebook',
            'facebook_group' => 'Facebook',
            'tiktok' => 'TikTok',
            'linkedin_page' => 'LinkedIn',
            'linkedin' => 'LinkedIn',
            default => Str::title($this->account->provider)
        };

        $variables['account'] = $this->account->name;

        return str_replace(
            Arr::map(array_keys($variables), fn($variable) => '{{' . $variable . '}}'),
            array_values($variables),
            $stripTags
        );
    }

    public function formatMedia(array $ids): Collection
    {
        $media = Media::whereIn('id', $ids)->get();

        return collect($ids)->map(function ($id) use ($media) {
            return $media->where('id', $id)->first();
        })->filter();
    }
}
