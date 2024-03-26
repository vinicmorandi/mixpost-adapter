<?php

namespace SaguiAi\MixpostAdapter\Concerns\Command;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use SaguiAi\MixpostAdapter\Models\Account;

trait AccountsOption
{
    public function accounts()
    {
        $ids = Arr::wrap($this->option('accounts'));

        return Account::when(count($ids), function (Builder $query) use ($ids) {
            $query->whereIn('id', $ids);
        })->get();
    }

    public function authorizedAccounts()
    {
        return $this->accounts()->where(function ($account) {
            return $account->isAuthorized();
        });
    }
}
