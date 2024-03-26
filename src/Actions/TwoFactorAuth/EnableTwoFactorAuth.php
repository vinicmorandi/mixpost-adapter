<?php

namespace SaguiAi\MixpostAdapter\Actions\TwoFactorAuth;

use Illuminate\Support\Collection;
use SaguiAi\MixpostAdapter\Support\RecoveryCode;
use SaguiAi\MixpostAdapter\TwoFactorAuthProvider;

class EnableTwoFactorAuth
{
    public function __construct(private readonly TwoFactorAuthProvider $provider)
    {
    }

    public function __invoke($user): void
    {
        $data = [
            'secret_key' => $this->provider->generateSecretKey(),
            'recovery_codes' => Collection::times(8, function () {
                return RecoveryCode::generate();
            })->all(),
        ];

        if ($twoFactorAuth = $user->twoFactorAuth) {
            $twoFactorAuth->update($data);
        } else {
            $user->twoFactorAuth()->create($data);
        }
    }
}
