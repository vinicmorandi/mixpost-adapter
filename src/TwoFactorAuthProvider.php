<?php

namespace SaguiAi\MixpostAdapter;

use Illuminate\Contracts\Cache\Repository;
use SaguiAi\MixpostAdapter\Contracts\TwoFactorAuthenticationProvider as TwoFactorAuthenticationProviderContract;
use PragmaRX\Google2FA\Google2FA;

class TwoFactorAuthProvider implements TwoFactorAuthenticationProviderContract
{
    public function __construct(public readonly Google2FA $engine, public readonly Repository $cache)
    {
    }

    public function generateSecretKey(): string
    {
        return $this->engine->generateSecretKey();
    }

    public function qrCodeUrl(string $name, string $email, string $secret): string
    {
        return $this->engine->getQRCodeUrl($name, $email, $secret);
    }

    public function verify(string $secret, string $code): bool
    {
        $timestamp = $this->engine->verifyKeyNewer(
            $secret, $code, optional($this->cache)->get($key = 'mixpost.2fa_codes.' . md5($code))
        );

        if ($timestamp !== false) {
            if ($timestamp === true) {
                $timestamp = $this->engine->getTimestamp();
            }

            optional($this->cache)->put($key, $timestamp, ($this->engine->getWindow() ?: 1) * 60);

            return true;
        }

        return false;
    }
}
