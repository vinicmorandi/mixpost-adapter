<?php

namespace SaguiAi\MixpostAdapter\Actions\TwoFactorAuth;

class DisableTwoFactorAuth
{
    public function __invoke($user): void
    {
        if (!is_null($user->twoFactorAuth->secret_key) ||
            !is_null($user->twoFactorAuth->recovery_codes) ||
            !is_null($user->twoFactorAuth->confirmed_at)) {
            $user->twoFactorAuth()->delete();
        }
    }
}
