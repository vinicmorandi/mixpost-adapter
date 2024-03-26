<?php

namespace SaguiAi\MixpostAdapter\SocialProviders\Mastodon;

use Illuminate\Http\Request;
use SaguiAi\MixpostAdapter\Abstracts\SocialProvider;
use SaguiAi\MixpostAdapter\Http\Resources\AccountResource;
use SaguiAi\MixpostAdapter\SocialProviders\Mastodon\Concerns\ManagesOAuth;
use SaguiAi\MixpostAdapter\SocialProviders\Mastodon\Concerns\ManagesRateLimit;
use SaguiAi\MixpostAdapter\SocialProviders\Mastodon\Concerns\ManagesResources;

class MastodonProvider extends SocialProvider
{
    use ManagesRateLimit;
    use ManagesOAuth;
    use ManagesResources;

    public array $callbackResponseKeys = ['code'];

    protected string $apiVersion = 'v1';
    protected string $serverUrl;

    public function __construct(Request $request, string $clientId, string $clientSecret, string $redirectUrl, array $values = [])
    {
        $this->serverUrl = "https://{$values['data']['server']}";

        parent::__construct($request, $clientId, $clientSecret, $redirectUrl, $values);
    }

    public static function externalPostUrl(AccountResource $accountResource): string
    {
        $server = $accountResource->data['server'] ?? 'undefined';

        return "https://$server/@$accountResource->username/{$accountResource->pivot->provider_post_id}";
    }

    public static function mapErrorMessage(string $key): string
    {
        return match ($key) {
            'access_token_expired' => __('mixpost::account.access_token_expired'),
            'upload_failed' => __('mixpost::service.mastodon.upload_failed'),
            default => $key
        };
    }
}
