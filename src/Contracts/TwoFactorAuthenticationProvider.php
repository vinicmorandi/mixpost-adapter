<?php

namespace SaguiAi\MixpostAdapter\Contracts;

interface TwoFactorAuthenticationProvider
{
    public function generateSecretKey(): string;

    public function qrCodeUrl(string $name, string $email, string $secret): string;

    public function verify(string $secret, string $code): bool;
}
