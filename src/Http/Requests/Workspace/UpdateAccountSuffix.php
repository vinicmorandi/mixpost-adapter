<?php

namespace SaguiAi\MixpostAdapter\Http\Requests\Workspace;

use Illuminate\Foundation\Http\FormRequest;
use SaguiAi\MixpostAdapter\Models\Account;
use SaguiAi\MixpostAdapter\Support\AccountSuffix;

class UpdateAccountSuffix extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['max:100'],
        ];
    }

    public function handle(): void
    {
        $record = Account::firstOrFailByUuid($this->route('account'));

        $record->update([
            'data' => array_merge($record->data ?? [], AccountSuffix::schema(strval($this->input('name')), true))
        ]);
    }
}
