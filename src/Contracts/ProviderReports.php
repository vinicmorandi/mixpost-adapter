<?php

namespace SaguiAi\MixpostAdapter\Contracts;

use SaguiAi\MixpostAdapter\Models\Account;

interface ProviderReports
{
    public function __invoke(Account $account, string $period): array;
}
