<?php

namespace SaguiAi\MixpostAdapter\Actions\TwoFactorAuth;

use Illuminate\Support\Collection;
use SaguiAi\MixpostAdapter\Support\RecoveryCode;

class RegenerateTwoFactorAuthRecoveryCodes
{
    public function __invoke($user): void
    {
        $user->twoFactorAuth->update([
            'recovery_codes' => Collection::times(8, function () {
                return RecoveryCode::generate();
            })->all(),
        ]);
    }
}
