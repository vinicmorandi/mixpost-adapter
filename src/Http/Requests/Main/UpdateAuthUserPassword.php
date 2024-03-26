<?php

namespace SaguiAi\MixpostAdapter\Http\Requests\Main;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use SaguiAi\MixpostAdapter\Concerns\UsesAuth;
use SaguiAi\MixpostAdapter\Concerns\UsesUserModel;
use SaguiAi\MixpostAdapter\Rules\CheckUserPasswordRule;

class UpdateAuthUserPassword extends FormRequest
{
    use UsesAuth;
    use UsesUserModel;

    protected $user;

    public function rules(): array
    {
        $this->user = self::getUserClass()::findOrFail(self::getAuthGuard()->user()->id);

        return [
            'current_password' => ['required', new CheckUserPasswordRule($this->user, __('mixpost::auth.password_dont_match'))],
            'password' => ['required', 'string', 'confirmed', Password::defaults()],
        ];
    }

    public function handle(): void
    {
        $this->user->update([
            'password' => Hash::make($this->input('password')),
        ]);
    }
}
