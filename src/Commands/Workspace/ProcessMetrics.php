<?php

namespace SaguiAi\MixpostAdapter\Commands\Workspace;

use Illuminate\Console\Command;
use SaguiAi\MixpostAdapter\Concerns\Command\AccountsOption;
use SaguiAi\MixpostAdapter\SocialProviders\Mastodon\Jobs\ProcessMastodonMetricsJob;
use SaguiAi\MixpostAdapter\SocialProviders\Meta\Jobs\ProcessInstagramMetricsJob;
use SaguiAi\MixpostAdapter\SocialProviders\TikTok\Jobs\ProcessTikTokMetricsJob;
use SaguiAi\MixpostAdapter\SocialProviders\Twitter\Jobs\ProcessTwitterMetricsJob;

class ProcessMetrics extends Command
{
    use AccountsOption;

    public $signature = 'mixpost:process-metrics {--accounts=}';

    public $description = 'Process metrics for the social providers';

    public function handle(): int
    {
        $this->authorizedAccounts()->each(function ($account) {
            $job = match ($account->provider) {
                'twitter' => ProcessTwitterMetricsJob::class,
                'mastodon' => ProcessMastodonMetricsJob::class,
                'instagram' => ProcessInstagramMetricsJob::class,
                'tiktok' => ProcessTikTokMetricsJob::class,
                default => null,
            };

            if ($job) {
                $job::dispatch($account);
            }
        });

        return self::SUCCESS;
    }
}
