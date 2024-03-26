<?php

namespace SaguiAi\MixpostAdapter\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;
use SaguiAi\MixpostAdapter\SocialProviders\Google\YoutubeProvider;
use SaguiAi\MixpostAdapter\SocialProviders\Linkedin\LinkedinPageProvider;
use SaguiAi\MixpostAdapter\SocialProviders\Linkedin\LinkedinProvider;
use SaguiAi\MixpostAdapter\SocialProviders\Mastodon\MastodonProvider;
use SaguiAi\MixpostAdapter\SocialProviders\Meta\FacebookGroupProvider;
use SaguiAi\MixpostAdapter\SocialProviders\Meta\FacebookPageProvider;
use SaguiAi\MixpostAdapter\SocialProviders\Meta\InstagramProvider;
use SaguiAi\MixpostAdapter\SocialProviders\Pinterest\PinterestProvider;
use SaguiAi\MixpostAdapter\SocialProviders\TikTok\TikTokProvider;
use SaguiAi\MixpostAdapter\SocialProviders\Twitter\TwitterProvider;

class AccountResource extends JsonResource
{
    public static $wrap = null;

    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'name' => $this->name . ($this->suffix() ? " ({$this->suffix()})" : ''),
            'suffix' => $this->suffix(),
            'username' => $this->username,
            'image' => $this->image(),
            'provider' => $this->provider,
            'provider_options' => $this->providerOptions(),
            'relationships' => $this->relationships(),
            'data' => $this->data,
            'authorized' => $this->authorized,
            'created_at' => $this->created_at->diffForHumans(),
            'external_url' => $this->whenPivotLoaded('mixpost_post_accounts', function () {
                if (!$this->pivot->provider_post_id) {
                    return null;
                }

                return $this->getExternalPostUrl();
            }),
            'errors' => $this->whenPivotLoaded('mixpost_post_accounts', function () {
                if ($this->pivot->errors) {
                    $errors = Arr::wrap(json_decode($this->pivot->errors, true));

                    return array_map(function ($error) {
                        if (is_string($error) && str_starts_with($error, '$t:')) {
                            $translationKey = substr($error, 3);

                            return __($translationKey);
                        }

                        if (is_string($error)) {
                            return $this->getErrorMessage($error);
                        }

                        return $error;
                    }, $errors);
                }
                return [];
            })
        ];
    }

    protected function getExternalPostUrl(): ?string
    {
        return match ($this->provider) {
            'twitter' => TwitterProvider::externalPostUrl($this),
            'facebook_page' => FacebookPageProvider::externalPostUrl($this),
            'facebook_group' => FacebookGroupProvider::externalPostUrl($this),
            'instagram' => InstagramProvider::externalPostUrl($this),
            'mastodon' => MastodonProvider::externalPostUrl($this),
            'youtube' => YoutubeProvider::externalPostUrl($this),
            'pinterest' => PinterestProvider::externalPostUrl($this),
            'linkedin' => LinkedinProvider::externalPostUrl($this),
            'linkedin_page' => LinkedinPageProvider::externalPostUrl($this),
            'tiktok' => TikTokProvider::externalPostUrl($this),
            default => '#'
        };
    }

    protected function getErrorMessage(string $key): string
    {
        return match ($this->provider) {
            'twitter' => TwitterProvider::mapErrorMessage($key),
            'facebook_page' => FacebookPageProvider::mapErrorMessage($key),
            'facebook_group' => FacebookGroupProvider::mapErrorMessage($key),
            'instagram' => InstagramProvider::mapErrorMessage($key),
            'mastodon' => MastodonProvider::mapErrorMessage($key),
            'youtube' => YoutubeProvider::mapErrorMessage($key),
            'pinterest' => PinterestProvider::mapErrorMessage($key),
            'linkedin' => LinkedinProvider::mapErrorMessage($key),
            'linkedin_page' => LinkedinPageProvider::mapErrorMessage($key),
            'tiktok' => TikTokProvider::mapErrorMessage($key),
            default => $key
        };
    }
}
