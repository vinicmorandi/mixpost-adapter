<?php

namespace SaguiAi\MixpostAdapter\SocialProviders\Meta;

use Illuminate\Support\Arr;
use SaguiAi\MixpostAdapter\Http\Resources\AccountResource;
use SaguiAi\MixpostAdapter\SocialProviders\Meta\Concerns\ManagesFacebookOAuth;
use SaguiAi\MixpostAdapter\SocialProviders\Meta\Concerns\ManagesInstagramResources;

class InstagramProvider extends MetaProvider
{
    use ManagesFacebookOAuth;
    use ManagesInstagramResources;

    public bool $onlyUserAccount = false;

    protected function setScope(): void
    {
        // Remove `publish_to_groups` scope.
        $newScopes = array_values(
            Arr::where($this->getSupportedScopeList(), function ($scope) {
                return $scope !== 'publish_to_groups';
            })
        );

        $this->scope = implode(',', $newScopes);
    }

    public static function externalPostUrl(AccountResource $accountResource): string
    {
        $data = $accountResource->pivot->data ? json_decode($accountResource->pivot->data, true) : [];

        if (Arr::get($data, 'story')) {
            return "https://www.instagram.com/stories/$accountResource->username/";
        }

        $shortcode = Arr::get($data, 'shortcode');

        if (!$shortcode) {
            return '';
        }

        return "https://www.instagram.com/p/$shortcode/";
    }
}
