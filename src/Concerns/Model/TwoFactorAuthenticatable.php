<?php

namespace SaguiAi\MixpostAdapter\Concerns\Model;

use BaconQrCode\Renderer\Color\Rgb;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\Fill;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Config;
use SaguiAi\MixpostAdapter\Models\UserTwoFactorAuth;
use SaguiAi\MixpostAdapter\Support\RecoveryCode;
use SaguiAi\MixpostAdapter\TwoFactorAuthProvider;

trait TwoFactorAuthenticatable
{
    public function twoFactorAuth(): HasOne
    {
        return $this->hasOne(UserTwoFactorAuth::class, 'user_id');
    }

    public function hasTwoFactorAuthEnabled(): bool
    {
        if ($this->twoFactorAuth) {
            return !is_null($this->twoFactorAuth->secret_key) &&
                !is_null($this->twoFactorAuth->confirmed_at);
        }

        return false;
    }

    public function twoFactorAuthSecretKey(): ?string
    {
        return $this->twoFactorAuth?->secret_key;
    }

    public function twoFactorRecoveryCodes(): array
    {
        return $this->twoFactorAuth?->recovery_codes->toArray() ?? [];
    }

    public function twoFactorReplaceRecoveryCode($code): void
    {
        $this->twoFactorAuth->update([
            'recovery_codes' => str_replace(
                $code,
                RecoveryCode::generate(),
                $this->twoFactorRecoveryCodes()
            ),
        ]);
    }

    public function twoFactorQrCodeSvg(): string
    {
        $svg = (new Writer(
            new ImageRenderer(
                new RendererStyle(192, 0, null, null, Fill::uniformColor(new Rgb(255, 255, 255), new Rgb(45, 55, 72))),
                new SvgImageBackEnd
            )
        ))->writeString($this->twoFactorQrCodeUrl());

        return trim(substr($svg, strpos($svg, "\n") + 1));
    }

    public function twoFactorQrCodeUrl(): string
    {
        return app(TwoFactorAuthProvider::class)->qrCodeUrl(
            Config::get('app.name'),
            $this->email,
            $this->twoFactorAuthSecretKey()
        );
    }
}
