<?php

namespace SaguiAi\MixpostAdapter\Concerns;

use SaguiAi\MixpostAdapter\Contracts\SocialProvider;
use SaguiAi\MixpostAdapter\Facades\SocialProviderManager;
use SaguiAi\MixpostAdapter\Models\Account;

trait UsesSocialProviderManager
{
    public function connectProvider(Account $account): SocialProvider
    {
        return SocialProviderManager::connect($account->provider, $account->values())
            ->useAccessToken($account->access_token->toArray());
    }
}
