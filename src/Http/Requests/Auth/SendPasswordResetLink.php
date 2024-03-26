<?php

namespace SaguiAi\MixpostAdapter\Http\Requests\Auth;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Password;
use SaguiAi\MixpostAdapter\Concerns\UsesUserModel;

class SendPasswordResetLink extends FormRequest
{
    use UsesUserModel;

    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
        ];
    }

    public function handle(): string
    {
        $appProviderModel = Config::get('auth.providers.users.model');
        Config::set('auth.providers.users.model', self::getUserClass());

        ResetPassword::createUrlUsing(function ($user, string $token) {
            return url(
                route('mixpost.password.reset', [
                    'token' => $token
                ], false)
            );
        });

        $status = Password::sendResetLink(
            $this->only('email')
        );

        Config::set('auth.providers.users.model', $appProviderModel);

        return $status;
    }
}
