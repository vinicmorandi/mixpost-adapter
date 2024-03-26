<?php

namespace SaguiAi\MixpostAdapter\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use SaguiAi\MixpostAdapter\Concerns\UsesUserModel;
use SaguiAi\MixpostAdapter\TwoFactorAuthProvider;

class TwoFactorLoginRequest extends FormRequest
{
    use UsesUserModel;

    protected mixed $challengedUser = null;

    protected bool $remember = false;

    public function rules(): array
    {
        return [
            'code' => ['nullable', 'string'],
            'recovery_code' => ['nullable', 'string'],
        ];
    }

    public function hasValidCode(): bool
    {
        return $this->input('code') && tap(app(TwoFactorAuthProvider::class)->verify(
                $this->challengedUser()->twoFactorAuthSecretKey(), $this->input('code')
            ), function ($result) {
                if ($result) {
                    $this->session()->forget('login.id');
                }
            });
    }

    public function challengedUser()
    {
        if ($this->challengedUser) {
            return $this->challengedUser;
        }

        if (!$this->session()->has('login.id') ||
            !$user = self::getUserClass()::find($this->session()->get('login.id'))) {
            throw ValidationException::withMessages([
                'code' => [__('mixpost::auth.two_factor_auth_code_invalid')],
            ]);
        }

        return $this->challengedUser = $user;
    }

    public function validRecoveryCode(): ?string
    {
        if (!$this->input('recovery_code')) {
            return null;
        }

        return tap(collect($this->challengedUser()->twoFactorRecoveryCodes())->first(function ($code) {
            return hash_equals($code, $this->input('recovery_code')) ? $code : null;
        }), function ($code) {
            if ($code) {
                $this->session()->forget('login.id');
            }
        });
    }

    public function hasChallengedUser(): bool
    {
        if ($this->challengedUser) {
            return true;
        }

        return $this->session()->has('login.id') &&
            self::getUserClass()::find($this->session()->get('login.id'));
    }

    public function remember()
    {
        if (!$this->remember) {
            $this->remember = $this->session()->pull('login.remember', false);
        }

        return $this->remember;
    }
}
