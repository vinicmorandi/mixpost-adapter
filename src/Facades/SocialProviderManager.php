<?php

namespace SaguiAi\MixpostAdapter\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \SaguiAi\MixpostAdapter\Contracts\SocialProvider connect(string $provider, array $values = [])
 * @method static \SaguiAi\MixpostAdapter\Contracts\SocialProvider useAccessToken(array $token = [])
 *
 * @see \SaguiAi\MixpostAdapter\Abstracts\SocialProviderManager
 */
class SocialProviderManager extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'MixpostSocialProviderManager';
    }
}
