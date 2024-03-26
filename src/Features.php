<?php

namespace SaguiAi\MixpostAdapter;

class Features
{
    public static function isTwoFactorAuthEnabled(): bool
    {
        return self::enabled('two_factor_auth');
    }

    public static function isForgotPasswordEnabled(): bool
    {
        return self::enabled('forgot_password');
    }

    private static function enabled(string $feature): bool
    {
        return Util::config("features.$feature");
    }
}
