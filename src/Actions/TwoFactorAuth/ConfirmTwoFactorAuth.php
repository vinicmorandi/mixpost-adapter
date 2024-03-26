<?php

namespace SaguiAi\MixpostAdapter\Actions\TwoFactorAuth;

use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;
use SaguiAi\MixpostAdapter\TwoFactorAuthProvider;

class ConfirmTwoFactorAuth
{
    public function __construct(private readonly TwoFactorAuthProvider $provider)
    {
    }

    public function __invoke($user, ?string $code): void
    {
        if (empty($user->twoFactorAuthSecretKey()) ||
            empty($code) ||
            !$this->provider->verify($user->twoFactorAuthSecretKey(), $code)) {
            throw ValidationException::withMessages([
                'code' => [__('The provided two factor authentication code was invalid.')],
            ]);
        }

        $user->twoFactorAuth->update([
            'confirmed_at' => Carbon::now(),
        ]);
    }
}
