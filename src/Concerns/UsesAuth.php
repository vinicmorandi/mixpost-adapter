<?php

namespace SaguiAi\MixpostAdapter\Concerns;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Support\Facades\Auth;
use SaguiAi\MixpostAdapter\Util;

trait UsesAuth
{
    public static function getAuthGuard(): Guard|StatefulGuard
    {
        return Auth::guard(self::getAuthGuardName());
    }

    public static function getAuthGuardName(): string|null
    {
        return Util::config('auth_guard');
    }
}
