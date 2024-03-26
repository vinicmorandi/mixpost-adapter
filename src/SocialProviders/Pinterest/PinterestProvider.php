<?php

namespace SaguiAi\MixpostAdapter\SocialProviders\Pinterest;

use SaguiAi\MixpostAdapter\Abstracts\SocialProvider;
use SaguiAi\MixpostAdapter\Facades\Services;
use SaguiAi\MixpostAdapter\Http\Resources\AccountResource;
use SaguiAi\MixpostAdapter\SocialProviders\Pinterest\Concerns\ManagesOAuth;
use SaguiAi\MixpostAdapter\SocialProviders\Pinterest\Concerns\ManagesRateLimit;
use SaguiAi\MixpostAdapter\SocialProviders\Pinterest\Concerns\ManagesResources;

class PinterestProvider extends SocialProvider
{
    public array $callbackResponseKeys = ['code'];

    public string $apiVersion = 'v5';
    public string $apiUrlProduction = 'https://api.pinterest.com';
    public string $apiUrlSandbox = 'https://api-sandbox.pinterest.com';

    use ManagesRateLimit;
    use ManagesOAuth;
    use ManagesResources;

    protected function getApiUrl(): string
    {
        if ($this->values['environment'] === 'sandbox') {
            return $this->apiUrlSandbox;
        }

        return $this->apiUrlProduction;
    }

    public function getEnvironment(): string
    {
        return Services::get('pinterest', 'environment') ?? 'sandbox';
    }

    public static function externalPostUrl(AccountResource $accountResource): string
    {
        return "https://www.pinterest.com/pin/{$accountResource->pivot->provider_post_id}/";
    }

    public static function mapErrorMessage(string $key): string
    {
        return match ($key) {
            'access_token_expired' => __('mixpost::account.access_token_expired'),
            'no_media_selected' => __('mixpost::post.no_media_selected'),
            'not_support_video' => __('mixpost::service.pinterest.not_support_video'),
            'video_upload_failed' => __('mixpost::service.pinterest.video_upload_failed'),
            default => $key
        };
    }
}
