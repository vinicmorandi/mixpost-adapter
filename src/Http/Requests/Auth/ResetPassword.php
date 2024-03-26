<?php

namespace SaguiAi\MixpostAdapter\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;
use SaguiAi\MixpostAdapter\Concerns\UsesAuth;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use SaguiAi\MixpostAdapter\Concerns\UsesUserModel;

class ResetPassword extends FormRequest
{
    use UsesAuth;
    use UsesUserModel;

    public function rules(): array
    {
        return [
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', PasswordRule::defaults()],
        ];
    }

    public function handle()
    {
        $appProviderModel = Config::get('auth.providers.users.model');
        Config::set('auth.providers.users.model', self::getUserClass());

        $status = Password::reset(
            $this->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) {
                $user->forceFill([
                    'password' => Hash::make($this->input('password')),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        Config::set('auth.providers.users.model', $appProviderModel);

        return $status;
    }
}
