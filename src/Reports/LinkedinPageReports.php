<?php

namespace SaguiAi\MixpostAdapter\Reports;

use SaguiAi\MixpostAdapter\Abstracts\Report;
use SaguiAi\MixpostAdapter\Models\Account;

class LinkedinPageReports extends Report
{
    public function __invoke(Account $account, string $period): array
    {
        return [
            'metrics' => [],
            'audience' => $this->audience($account, $period)
        ];
    }
}
