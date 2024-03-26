<?php

namespace SaguiAi\MixpostAdapter\Http\Requests\Main;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use SaguiAi\MixpostAdapter\Concerns\UsesAuth;
use SaguiAi\MixpostAdapter\Concerns\UsesUserModel;

class UpdateAuthUser extends FormRequest
{
    use UsesAuth;
    use UsesUserModel;

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique(app(self::getUserClass())->getTable())->ignore(self::getAuthGuard()->id())],
        ];
    }

    public function handle(): void
    {
        $user = self::getUserClass()::findOrFail(self::getAuthGuard()->user()->id);

        $user->update([
            'name' => $this->input('name'),
            'email' => $this->input('email'),
        ]);
    }
}
