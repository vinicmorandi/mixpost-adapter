<?php

namespace SaguiAi\MixpostAdapter\SocialProviders\Twitter;

use Abraham\TwitterOAuth\TwitterOAuth;
use Illuminate\Http\Request;
use SaguiAi\MixpostAdapter\Abstracts\SocialProvider;
use SaguiAi\MixpostAdapter\Facades\Services;
use SaguiAi\MixpostAdapter\Http\Resources\AccountResource;
use SaguiAi\MixpostAdapter\SocialProviders\Twitter\Concerns\ManagesOAuth;
use SaguiAi\MixpostAdapter\SocialProviders\Twitter\Concerns\ManagesRateLimit;
use SaguiAi\MixpostAdapter\SocialProviders\Twitter\Concerns\ManagesResources;

class TwitterProvider extends SocialProvider
{
    use ManagesRateLimit;
    use ManagesOAuth;
    use ManagesResources;

    public array $callbackResponseKeys = ['oauth_token', 'oauth_verifier'];

    protected string $apiVersion = '2';

    public TwitterOAuth $connection;

    // Overwrite __construct to use Twitter SDK
    public function __construct(Request $request, string $clientId, string $clientSecret, string $redirectUrl, array $values = [])
    {
        $this->connection = new TwitterOAuth($clientId, $clientSecret);
        $this->connection->setApiVersion($this->apiVersion);
        $this->connection->setTimeouts(10, 60);

        parent::__construct($request, $clientId, $clientSecret, $redirectUrl, $values);
    }

    public function getTier(): string
    {
        return Services::get('twitter', 'tier') ?? 'legacy';
    }

    public static function externalPostUrl(AccountResource $accountResource): string
    {
        return "https://twitter.com/$accountResource->username/status/{$accountResource->pivot->provider_post_id}";
    }

    public static function mapErrorMessage(string $key): string
    {
        return match ($key) {
            'access_token_expired' => __('mixpost::account.access_token_expired'),
            'upload_failed' => __('mixpost::service.twitter.upload_failed'),
            default => $key,
        };
    }
}
