<?php

namespace SaguiAi\MixpostAdapter\SocialProviders\Google;

use SaguiAi\MixpostAdapter\Abstracts\SocialProvider;
use SaguiAi\MixpostAdapter\Http\Resources\AccountResource;
use SaguiAi\MixpostAdapter\SocialProviders\Google\Concerns\ManagesOAuth;
use SaguiAi\MixpostAdapter\SocialProviders\Google\Concerns\ManagesRateLimit;
use SaguiAi\MixpostAdapter\SocialProviders\Google\Concerns\ManagesYoutubeResources;

class YoutubeProvider extends SocialProvider
{
    public bool $onlyUserAccount = false;
    public array $callbackResponseKeys = ['code'];

    use ManagesRateLimit;
    use ManagesOAuth;
    use ManagesYoutubeResources;

    public static function externalPostUrl(AccountResource $accountResource): string
    {
        return "https://www.youtube.com/watch?v={$accountResource->pivot->provider_post_id}";
    }

    public static function mapErrorMessage(string $key): string
    {
        return match ($key) {
            'access_token_expired' => __('mixpost::account.access_token_expired'),
            'request_timeout' => __('mixpost::error.request_timeout'),
            'unknown_error' => __('mixpost::error.unknown_error'),
            'video_not_selected' => __('mixpost::post.video_not_selected'),
            default => $key
        };
    }
}
