<?php

namespace SaguiAi\MixpostAdapter\Commands\Workspace;

use Illuminate\Console\Command;
use SaguiAi\MixpostAdapter\Concerns\Command\AccountsOption;
use SaguiAi\MixpostAdapter\SocialProviders\Mastodon\Jobs\ImportMastodonFollowersJob;
use SaguiAi\MixpostAdapter\SocialProviders\Meta\Jobs\ImportFacebookGroupMembersJob;
use SaguiAi\MixpostAdapter\SocialProviders\Meta\Jobs\ImportFacebookPageFollowersJob;
use SaguiAi\MixpostAdapter\SocialProviders\Meta\Jobs\ImportInstagramFollowersJob;
use SaguiAi\MixpostAdapter\SocialProviders\Pinterest\Jobs\ImportPinterestFollowersJob;
use SaguiAi\MixpostAdapter\SocialProviders\TikTok\Jobs\ImportTikTokFollowersJob;
use SaguiAi\MixpostAdapter\SocialProviders\Twitter\Jobs\ImportTwitterFollowersJob;
use SaguiAi\MixpostAdapter\SocialProviders\Linkedin\Jobs\ImportLinkedinPageFollowersJob;

class ImportAccountAudience extends Command
{
    use AccountsOption;

    public $signature = 'mixpost:import-account-audience {--accounts=}';

    public $description = 'Import audience(count of followers, fans...etc.) for the social providers';

    public function handle(): int
    {
        $this->authorizedAccounts()->each(function ($account) {
            $job = match ($account->provider) {
                'twitter' => ImportTwitterFollowersJob::class,
                'facebook_page' => ImportFacebookPageFollowersJob::class,
                'facebook_group' => ImportFacebookGroupMembersJob::class,
                'instagram' => ImportInstagramFollowersJob::class,
                'mastodon' => ImportMastodonFollowersJob::class,
                'pinterest' => ImportPinterestFollowersJob::class,
                'tiktok' => ImportTikTokFollowersJob::class,
                'linkedin_page' => ImportLinkedinPageFollowersJob::class,
                default => null,
            };

            if ($job) {
                $job::dispatch($account);
            }
        });

        return self::SUCCESS;
    }
}
