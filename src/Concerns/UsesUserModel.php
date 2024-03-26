<?php

namespace SaguiAi\MixpostAdapter\Concerns;

use SaguiAi\MixpostAdapter\Models\User;
use SaguiAi\MixpostAdapter\Util;

trait UsesUserModel
{
    public static function getUserClass(): string
    {
        return Util::config('user_model', User::class);
    }
}
