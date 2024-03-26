<?php

namespace SaguiAi\MixpostAdapter\Commands\Workspace;

use Illuminate\Console\Command;
use SaguiAi\MixpostAdapter\Concerns\Command\AccountsOption;
use SaguiAi\MixpostAdapter\SocialProviders\Mastodon\Jobs\ImportMastodonPostsJob;
use SaguiAi\MixpostAdapter\SocialProviders\Meta\Jobs\ImportFacebookInsightsJob;
use SaguiAi\MixpostAdapter\SocialProviders\Meta\Jobs\ImportInstagramInsightsJob;
use SaguiAi\MixpostAdapter\SocialProviders\Meta\Jobs\ImportInstagramMediaJob;
use SaguiAi\MixpostAdapter\SocialProviders\Pinterest\Jobs\ImportPinterestDataJob;
use SaguiAi\MixpostAdapter\SocialProviders\TikTok\Jobs\ImportTikTokVideosJob;
use SaguiAi\MixpostAdapter\SocialProviders\Twitter\Jobs\ImportTwitterPostsJob;

class ImportAccountData extends Command
{
    use AccountsOption;

    public $signature = 'mixpost:import-account-data {--accounts=}';

    public $description = 'Import data from social service providers';

    public function handle(): int
    {
        $this->authorizedAccounts()->each(function ($account) {
            // TODO:: Move provider jobs to provider class
            // example: $account->provider()->importDataJobs();
            $jobs = match ($account->provider) {
                'twitter' => [
                    ImportTwitterPostsJob::class
                ],
                'facebook_page' => [
                    ImportFacebookInsightsJob::class,
//                    ImportFacebookPagePostsJob::class,
                ],
                'instagram' => [
                    ImportInstagramInsightsJob::class,
                    ImportInstagramMediaJob::class,
                ],
                'mastodon' => [
                    ImportMastodonPostsJob::class
                ],
                'pinterest' => [
                    ImportPinterestDataJob::class,
                ],
                'tiktok' => [
                    ImportTikTokVideosJob::class
                ],
                default => [],
            };

            foreach ($jobs as $job) {
                $job::dispatch($account);
            }
        });

        return self::SUCCESS;
    }
}
