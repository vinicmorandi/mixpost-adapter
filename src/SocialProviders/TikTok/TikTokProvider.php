<?php

namespace SaguiAi\MixpostAdapter\SocialProviders\TikTok;

use SaguiAi\MixpostAdapter\Abstracts\SocialProvider;
use SaguiAi\MixpostAdapter\Facades\Services;
use SaguiAi\MixpostAdapter\Http\Resources\AccountResource;
use SaguiAi\MixpostAdapter\SocialProviders\TikTok\Concerns\ManagesOAuth;
use SaguiAi\MixpostAdapter\SocialProviders\TikTok\Concerns\ManagesRateLimit;
use SaguiAi\MixpostAdapter\SocialProviders\TikTok\Concerns\ManagesResources;

class TikTokProvider extends SocialProvider
{
    public array $callbackResponseKeys = ['code'];

    use ManagesRateLimit;
    use ManagesOAuth;
    use ManagesResources;

    public function identifier(): string
    {
        return 'tiktok';
    }

    public static function getShareType(): string
    {
        return Services::get('tiktok', 'share_type') ?? 'inbox';
    }

    public static function isInboxShareType(): bool
    {
        return self::getShareType() === 'inbox';
    }

    public static function isDirectShareType(): bool
    {
        return self::getShareType() === 'direct';
    }

    public static function externalPostUrl(AccountResource $accountResource): string
    {
        if (!$accountResource->pivot->provider_post_id) {
            return '';
        }

        return "https://www.tiktok.com/{$accountResource->username}/video/{$accountResource->pivot->provider_post_id}";
    }

    public static function mapErrorMessage(string $key): string
    {
        return match ($key) {
            'access_token_expired' => __('mixpost::account.access_token_expired'),
            'video_not_selected' => __('mixpost::post.video_not_selected'),
            'supports_only_videos' => __('mixpost::service.tiktok.supports_only_videos'),
            default => $key
        };
    }
}
