<?php

namespace SaguiAi\MixpostAdapter\Commands\Workspace;

use Illuminate\Console\Command;
use SaguiAi\MixpostAdapter\Concerns\Command\AccountsOption;
use SaguiAi\MixpostAdapter\Jobs\CheckAndRefreshAccountTokenJob;

class CheckAndRefreshAccountToken extends Command
{
    use AccountsOption;

    public $signature = 'mixpost:check-and-refresh-account-token {--accounts=}';

    public $description = 'Check and refresh social account token';

    protected function providers(): array
    {
        return [
            'youtube',
            'linkedin',
            'pinterest',
            'tiktok'
        ];
    }

    public function handle(): int
    {
        $this->authorizedAccounts()
            ->whereIn('provider', $this->providers())
            ->each(function ($account) {
                CheckAndRefreshAccountTokenJob::dispatch($account);
            });

        return self::SUCCESS;
    }
}
