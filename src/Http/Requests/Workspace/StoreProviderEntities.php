<?php

namespace SaguiAi\MixpostAdapter\Http\Requests\Workspace;

use Illuminate\Foundation\Http\FormRequest;
use SaguiAi\MixpostAdapter\Actions\Account\StoreProviderEntitiesAsAccounts as StoreProviderEntitiesAsAccountsAction;
use SaguiAi\MixpostAdapter\Events\Account\StoringAccountEntities;

class StoreProviderEntities extends FormRequest
{
    public function rules(): array
    {
        return [
            'items' => ['required', 'array', 'min:1'],
        ];
    }

    public function handle()
    {
        StoringAccountEntities::dispatch($this->route('provider'), $this->input('items'));

        (new StoreProviderEntitiesAsAccountsAction())($this->route('provider'), $this->input('items'));
    }
}
