<?php

namespace SaguiAi\MixpostAdapter\Http\Requests\Main;

use Illuminate\Foundation\Http\FormRequest;
use SaguiAi\MixpostAdapter\Concerns\ConfirmsPasswords;
use SaguiAi\MixpostAdapter\Concerns\UsesAuth;
use SaguiAi\MixpostAdapter\Concerns\UsesUserModel;
use SaguiAi\MixpostAdapter\Rules\CheckUserPasswordRule;

class ConfirmPassword extends FormRequest
{
    use UsesAuth;
    use UsesUserModel;
    use ConfirmsPasswords;

    public function rules(): array
    {
        $user = self::getUserClass()::findOrFail(self::getAuthGuard()->user()->id);

        return [
            'password' => [
                'required',
                new CheckUserPasswordRule($user, __('mixpost::auth.password_dont_match'))
            ],
        ];
    }

    public function handle(): void
    {
        $this->confirmPassword();
    }
}
