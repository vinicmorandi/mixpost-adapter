<?php

namespace SaguiAi\MixpostAdapter\SocialProviders\Meta;

use Illuminate\Support\Arr;
use SaguiAi\MixpostAdapter\Http\Resources\AccountResource;
use SaguiAi\MixpostAdapter\SocialProviders\Meta\Concerns\ManagesFacebookOAuth;
use SaguiAi\MixpostAdapter\SocialProviders\Meta\Concerns\ManagesFacebookPageResources;

class FacebookPageProvider extends MetaProvider
{
    use ManagesFacebookOAuth;
    use ManagesFacebookPageResources;

    public bool $onlyUserAccount = false;

    protected function accessToken(): string
    {
        return $this->getAccessToken()['page_access_token'];
    }

    public static function externalPostUrl(AccountResource $accountResource): string
    {
        $data = $accountResource->pivot->data ? json_decode($accountResource->pivot->data, true) : [];

        $domain = 'https://facebook.com';

        if (Arr::get($data, 'story') && $path = Arr::get($data, 'path')) {
            return "$domain/stories/$path?view_single=1";
        }

        if (Arr::get($data, 'story') && !Arr::get($data, 'path')) {
            return "$domain/$accountResource->provider_id";
        }

        return "$domain/{$accountResource->pivot->provider_post_id}";
    }
}
