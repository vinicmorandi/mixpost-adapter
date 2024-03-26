<?php

namespace SaguiAi\MixpostAdapter\SocialProviders\Meta;

use SaguiAi\MixpostAdapter\Http\Resources\AccountResource;
use SaguiAi\MixpostAdapter\SocialProviders\Meta\Concerns\ManagesFacebookGroupResources;
use SaguiAi\MixpostAdapter\SocialProviders\Meta\Concerns\ManagesFacebookOAuth;

class FacebookGroupProvider extends MetaProvider
{
    use ManagesFacebookOAuth;
    use ManagesFacebookGroupResources;

    public bool $onlyUserAccount = false;

    protected function accessToken(): string
    {
        return $this->getAccessToken()['access_token'];
    }

    public static function externalPostUrl(AccountResource $accountResource): string
    {
        return "https://www.facebook.com/{$accountResource->pivot->provider_post_id}";
    }
}
